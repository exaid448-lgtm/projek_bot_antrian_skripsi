<?php

namespace App\Http\Controllers;

use App\Models\ChatKonsultasi;
use App\Models\Profil; // App\Models\Profil maps to 'profil_karyawan'
use App\Models\ProfilPengunjung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatLoketController extends Controller
{
    // Menampilkan Halaman Utama Chat Konsul Loket
    public function index($id_pengunjung = null)
    {
        $id_loket = session('id_loket');
        $id_profil = session('id_profil'); // id of logged-in employee

        if (!$id_loket) {
            return redirect()->route('dashboard')->with('error', 'Sesi loket tidak ditemukan.');
        }

        // Cari semua id_pengunjung yang pernah melakukan konsultasi di loket ini
        $latestChatPerVisitor = ChatKonsultasi::where('id_loket', $id_loket)
            ->whereNotNull('id_pengunjung')
            ->selectRaw('id_pengunjung, MAX(created_at) as max_time')
            ->groupBy('id_pengunjung')
            ->orderBy('max_time', 'desc')
            ->get();

        $daftar_pengunjung = $latestChatPerVisitor->map(function ($chat) use ($id_loket) {
            $visitor = ProfilPengunjung::find($chat->id_pengunjung);
            if (!$visitor) return null;

            $pesanTerakhirObj = ChatKonsultasi::where('id_loket', $id_loket)
                ->where('id_pengunjung', $chat->id_pengunjung)
                ->orderBy('created_at', 'desc')
                ->first();

            $unreadCount = ChatKonsultasi::where('id_loket', $id_loket)
                ->where('id_pengunjung', $chat->id_pengunjung)
                ->where('tipe_pengirim', 'pengunjung')
                ->where('status_baca', false)
                ->count();

            // Set dynamic properties
            $visitor->pesanTerakhir = $pesanTerakhirObj ? $pesanTerakhirObj->pesan : null;
            $visitor->pesanTerakhirObj = $pesanTerakhirObj;
            $visitor->unread_count = $unreadCount;
            $visitor->updated_at = $pesanTerakhirObj ? $pesanTerakhirObj->created_at : null;

            return $visitor;
        })->filter();

        // Jika ada id_pengunjung yang dipilih atau jika daftar_pengunjung tidak kosong
        if (!$id_pengunjung && $daftar_pengunjung->count() > 0) {
            $id_pengunjung = $daftar_pengunjung->first()->id_pengunjung;
        }

        $pengunjung_aktif = null;
        $riwayat_chat = collect();

        if ($id_pengunjung) {
            $pengunjung_aktif = ProfilPengunjung::find($id_pengunjung);
            if ($pengunjung_aktif) {
                // Ambil semua riwayat chat antara loket ini dengan pengunjung terkait
                $riwayat_chat = ChatKonsultasi::where('id_loket', $id_loket)
                    ->where('id_pengunjung', $id_pengunjung)
                    ->orderBy('created_at', 'asc')
                    ->get();

                // Tandai pesan dari pengunjung sebagai terbaca saat halaman dibuka
                ChatKonsultasi::where('id_loket', $id_loket)
                    ->where('id_pengunjung', $id_pengunjung)
                    ->where('tipe_pengirim', 'pengunjung')
                    ->update(['status_baca' => true]);
            }
        }

        // Ambil profil karyawan dari DB untuk footer sidebar
        $karyawan = Profil::find($id_profil);

        return view('admin_loket.chat_konsul_loket', compact('daftar_pengunjung', 'pengunjung_aktif', 'riwayat_chat', 'karyawan'));
    }

    // Menyimpan Pesan Baru ke Database
    public function kirimPesan(Request $request, $id_pengunjung)
    {
        $request->validate([
            'pesan' => 'nullable|string',
            // Batasan upload file: mimes untuk mencegah tipe berkas berbahaya, max 5MB
            'file_upload' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt,jpg,jpeg,png,gif|max:5120'
        ]);

        if (!$request->pesan && !$request->hasFile('file_upload')) {
            return redirect()->back();
        }

        $id_loket = session('id_loket');
        $id_profil = session('id_profil');

        if (!$id_loket || !$id_profil) {
            return redirect()->back()->with('error', 'Sesi pegawai tidak valid.');
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
            'id_profil' => $id_profil,
            'id_pengunjung' => $id_pengunjung,
            'tipe_pengirim' => 'karyawan',
            'pesan' => $request->pesan,
            'file_lampiran' => $path_file,
            'ukuran_file' => $ukuran_format,
            'status_baca' => false,
        ]);

        return redirect()->route('chatloket.index', ['id_pengunjung' => $id_pengunjung]);
    }

    // Mengedit Pesan Teks
    public function editPesan(Request $request, $id)
    {
        $request->validate([
            'pesan' => 'required|string',
        ]);

        $id_profil = session('id_profil');

        $pesan = ChatKonsultasi::where('id_chat', $id)
            ->where('id_profil', $id_profil)
            ->where('tipe_pengirim', 'karyawan')
            ->firstOrFail();

        $pesan->update([
            'pesan' => $request->pesan
        ]);

        return redirect()->back()->with('success', 'Pesan berhasil diperbarui.');
    }

    // Menghapus Pesan
    public function hapusPesan($id)
    {
        $id_profil = session('id_profil');

        $pesan = ChatKonsultasi::where('id_chat', $id)
            ->where('id_profil', $id_profil)
            ->where('tipe_pengirim', 'karyawan')
            ->firstOrFail();

        if ($pesan->file_lampiran) {
            Storage::disk('public')->delete($pesan->file_lampiran);
        }

        $pesan->delete();

        return redirect()->back()->with('success', 'Pesan berhasil dihapus.');
    }
}
