<?php
 namespace App\Http\Controllers;

 use App\Models\Antrian;
 use App\Models\ProfilPengunjung;
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Auth;
 
 class HistoryTiketController extends Controller
 {
public function index()
{
    $user = Auth::user();
    
    // Pastikan id_user di tabel profil_pengunjung sesuai dengan id di tabel users
    $profil = ProfilPengunjung::where('id_user', $user->id_user)->first();

    if ($profil) {
        $data = Antrian::where('id_pengunjung', $profil->id_pengunjung)
            ->with('loket') 
            ->orderBy('id_antrian', 'desc')
            ->get();

        $histories = $data->map(function ($item) {
            // Pastikan kita mengonversi string ke Carbon jika tidak menggunakan $casts di Model
            $waktu = \Carbon\Carbon::parse($item->waktu_voice);

            return [
                'id'       => $item->id_antrian,
                'nomor'    => $item->nomor_antrian,
                'instansi' => $item->loket->nama_loket ?? 'N/A',
                'nama_pelayanan' => $item->loket->nama_pelayanan ?? 'Pelayanan Umum',
                'metode'   => $item->jenis_antrian,
                'layanan'  => $item->jenis_antrian, // Backward compatibility if needed
                'tanggal'  => $item->waktu_voice ? $waktu->translatedFormat('d M Y') : '-',
                'tanggal_raw' => $item->waktu_voice ? $waktu->format('Y-m-d') : null,
                'jam'      => $item->waktu_voice ? $waktu->format('H:i') : '-',
                'status'   => strtolower($item->status_antrian), 
                'loket'    => $item->id_loket,
                'kode_booking' => $item->kode_booking_unik,
                'slot_waktu' => $item->slot_waktu,
                'status_booking' => $item->status_booking,
                'waktu_check_in' => $item->waktu_check_in ? \Carbon\Carbon::parse($item->waktu_check_in)->format('H:i') : null,
            ];
        });
    } else {
        $histories = collect();
    }

    return view('pengunjung.history_antrian', compact('histories'));
}
 }