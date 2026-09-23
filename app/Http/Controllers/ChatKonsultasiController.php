<?php

namespace App\Http\Controllers;

use App\Models\ChatKonsultasi;
use App\Models\Loket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatKonsultasiController extends Controller
{
    // Menampilkan Halaman Utama Chat Konsultasi
    public function index($id_loket = null)
    {
        $user = Auth::user();
        $user_pengunjung = $user->profil_pengunjung;

        if (!$user_pengunjung) {
            return redirect()->back()->with('error', 'Profil pengunjung tidak ditemukan.');
        }

        // Mengambil semua loket beserta pesan terakhir khusus untuk pengunjung ini
        $daftar_instansi = Loket::all()->map(function ($loket) use ($user_pengunjung) {
            // Ambil pesan paling akhir antara pengunjung ini dengan loket terkait
            $pesanTerakhir = ChatKonsultasi::where('id_loket', $loket->id_loket)
                ->where(function($query) use ($user_pengunjung) {
                    $query->where('id_pengunjung', $user_pengunjung->id_pengunjung)
                          ->orWhereNull('id_pengunjung');
                })
                ->orderBy('created_at', 'desc')
                ->first();

            // Hitung pesan dari karyawan yang belum dibaca oleh pengunjung
            $unreadCount = ChatKonsultasi::where('id_loket', $loket->id_loket)
                ->where('id_pengunjung', $user_pengunjung->id_pengunjung)
                ->where('tipe_pengirim', 'karyawan')
                ->where('status_baca', false)
                ->count();

            // Pasang properti dinamis ke dalam object loket
            $loket->pesanTerakhir = $pesanTerakhir;
            $loket->unread_count = $unreadCount;

            return $loket;
        });

        if (!$id_loket && $daftar_instansi->count() > 0) {
            $id_loket = $daftar_instansi->first()->id_loket;
        }

        $instansi_aktif = Loket::find($id_loket);

        $riwayat_chat = ChatKonsultasi::where('id_loket', $id_loket)
            ->where(function($query) use ($user_pengunjung) {
                $query->where('id_pengunjung', $user_pengunjung->id_pengunjung)
                      ->orWhereNull('id_pengunjung'); 
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Tandai pesan dari karyawan sebagai terbaca saat halaman loket ini dibuka
        ChatKonsultasi::where('id_loket', $id_loket)
            ->where('id_pengunjung', $user_pengunjung->id_pengunjung)
            ->where('tipe_pengirim', 'karyawan')
            ->update(['status_baca' => true]);

        return view('pengunjung.chat_konsultasi', compact('daftar_instansi', 'instansi_aktif', 'riwayat_chat', 'user_pengunjung'));
    }

    // Menyimpan Pesan atau Berkas Baru ke Database
    public function kirimPesan(Request $request, $id_loket)
    {
        $request->validate([
            'pesan' => 'nullable|string',
            'file_upload' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,docx|max:5120'
        ]);

        if (!$request->pesan && !$request->hasFile('file_upload')) {
            return redirect()->back();
        }

        $user = Auth::user();
        $profil_pengunjung = \App\Models\ProfilPengunjung::where('id_user', $user->id_user)->first();

        if (!$profil_pengunjung) {
            return redirect()->back()->with('error', 'Profil pengunjung tidak ditemukan.');
        }
        
        $path_file = null;
        $ukuran_format = null;

        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $path_file = $file->store('attachments_chat', 'public');
            
            $bytes = $file->getSize();
            if ($bytes >= 1048576) {
                $ukuran_format = number_format($bytes / 1048576, 1) . ' MB';
            } else {
                $ukuran_format = number_format($bytes / 1024, 0) . ' KB';
            }
        }

        ChatKonsultasi::create([
            'id_loket' => $id_loket,
            'id_profil' => null, 
            'id_pengunjung' => $profil_pengunjung->id_pengunjung, 
            'tipe_pengirim' => 'pengunjung',
            'pesan' => $request->pesan,
            'file_lampiran' => $path_file,
            'ukuran_file' => $ukuran_format,
            'status_baca' => false, 
        ]);

        return redirect()->route('chatkonsultasi.index', ['id_loket' => $id_loket]);
    }

    // Fitur Edit Pesan Teks
    public function editPesan(Request $request, $id)
    {
        $request->validate([
            'pesan' => 'required|string',
        ]);

        $user = Auth::user();
        $profil_pengunjung = \App\Models\ProfilPengunjung::where('id_user', $user->id_user)->first();

        $pesan = ChatKonsultasi::where('id_chat', $id)
            ->where('id_pengunjung', $profil_pengunjung->id_pengunjung)
            ->where('tipe_pengirim', 'pengunjung')
            ->firstOrFail();

        $pesan->update([
            'pesan' => $request->pesan
        ]);

        return redirect()->back()->with('success', 'Pesan berhasil diperbarui.');
    }

    // Fitur Hapus Pesan
    public function hapusPesan($id)
    {
        $user = Auth::user();
        $profil_pengunjung = \App\Models\ProfilPengunjung::where('id_user', $user->id_user)->first();

        $pesan = ChatKonsultasi::where('id_chat', $id)
            ->where('id_pengunjung', $profil_pengunjung->id_pengunjung)
            ->where('tipe_pengirim', 'pengunjung')
            ->firstOrFail();

        if ($pesan->file_lampiran) {
            Storage::disk('public')->delete($pesan->file_lampiran);
        }

        $pesan->delete();

        return redirect()->back()->with('success', 'Pesan berhasil dihapus.');
    }
}