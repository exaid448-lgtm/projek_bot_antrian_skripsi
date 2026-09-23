<?php

use App\Http\Controllers\DataLoketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LoketDashboardController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\KonsulController;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DataAntrianController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\DataAbsensiController;
use App\Http\Controllers\DataAkunController;
use App\Http\Controllers\AlgoritmaController;
use App\Http\Controllers\DataJadwalController;
use App\Http\Controllers\RegisterPengunjungController;
use App\Http\Controllers\LoginPengunjungController;
use App\Http\Controllers\DashbordPengunjungController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\InformasiSyaratController;
use App\Http\Controllers\HistoryTiketController;
use App\Http\Controllers\ProfilPengunjungController;
use App\Http\Controllers\CatatanKonsulController;
use App\Http\Controllers\LaporanSkmController;
use App\Http\Controllers\PersyaratanLoketController;
use App\Http\Controllers\InformasiLoketController;
use App\Http\Controllers\AntrianManualController;
use App\Http\Controllers\DataTrainingController;
use App\Http\Controllers\EstimasiWaktuController;
use App\Http\Controllers\ChatKonsultasiController;
use App\Http\Controllers\ChatLoketController;
use App\Http\Controllers\DataSkmController;
use App\Http\Controllers\DataPengunjungController;
use App\Http\Controllers\DataKinerjaLoketController;
use App\Http\Controllers\ValidasiPrioritasController;
use App\Http\Controllers\DataLoketBermasalahcontroller;
use App\Http\Controllers\ValidasiController;
use App\Http\Controllers\DataBebanKerjaController;
use App\Http\Controllers\BookingAntrianController;
use App\Http\Controllers\DataBookingController;
use App\Http\Controllers\RegistrasiKaryawanController;
use App\Http\Controllers\ResetAkunKaryawanController;
use App\Http\Controllers\LogSistemController;
/*
|--------------------------------------------------------------------------
| HALAMAN PUBLIK (TANPA LOGIN)
|--------------------------------------------------------------------------
*/

Route::get('/register', function () {
    return view('pengunjung.register_pengunjung');
})->name('register');

Route::get('/', function () {
    return view('indax');
});

Route::get('/simulasi-suara', function () {
    $profil = \App\Models\ProfilPengunjung::where('id_user', Auth::id())->first();
    
    $isPermanen = false;
    $isLansia = false;
    
    if ($profil) {
        // Cek jika punya prioritas yang disetujui dan belum kadaluarsa
        if ($profil->status_prioritas === 'disetujui') {
            if ($profil->jenis_prioritas === 'disabilitas_permanen') {
                $isPermanen = true;
            } elseif ($profil->tanggal_berakhir_prioritas && \Carbon\Carbon::today()->lte(\Carbon\Carbon::parse($profil->tanggal_berakhir_prioritas))) {
                $isPermanen = true;
            }
        }
        
        // Cek jika lansia
        if ($profil->tanggal_lahir) {
            $umur = \Carbon\Carbon::parse($profil->tanggal_lahir)->age;
            if ($umur >= 60) $isLansia = true;
        }
    }
    
    return view('indax', compact('isPermanen', 'isLansia'));
})->name('simulasi.suara')->middleware('auth');

Route::post('/upload-dokumen-kiosk', function (\Illuminate\Http\Request $request) {
    if (!$request->hasFile('dokumen_prioritas_sementara')) {
        return response()->json(['success' => false, 'message' => 'Tidak ada file diunggah.']);
    }
    $file = $request->file('dokumen_prioritas_sementara');
    $filename = time() . '_prioritas_kiosk_' . $file->getClientOriginalName();
    $path = $file->storeAs('dokumen_prioritas', $filename, 'public');
    
    // Simpan ke Cache berdasar ID User karena akan diambil oleh python server yang tidak punya session ini
    \Illuminate\Support\Facades\Cache::put('temp_dokumen_kiosk_' . \Illuminate\Support\Facades\Auth::id(), $path, now()->addMinutes(10));

    return response()->json([
        'success' => true, 
        'path' => $path
    ]);
});

// Endpoint API Pencatatan Kegagalan Sistem
Route::post('/api/catat-kegagalan-sistem', [LogSistemController::class, 'catatKegagalan'])->name('api.log.kegagalan');

Route::get('/register_karyawan/{token}', [RegistrasiKaryawanController::class, 'index'])->name('register_karyawan.index');
Route::post('/register_karyawan/proses', [RegistrasiKaryawanController::class, 'proses'])->name('register_karyawan.proses');

Route::get('/riset_akun/{token}', [ResetAkunKaryawanController::class, 'index'])->name('riset_akun.index');
Route::post('/riset_akun/proses', [ResetAkunKaryawanController::class, 'proses'])->name('riset_akun.proses');

Route::get('/antrian-manual', [AntrianManualController::class, 'index'])
    ->name('antrian.manual.index');
Route::post('/antrian-manual/store', [AntrianManualController::class, 'store'])
    ->name('antrian.manual.store');

// Halaman laporan SKM dipindah ke grup middleware ceklogin agar aman
use App\Http\Controllers\Riset_akunController;

Route::post('/reset-password/request', [Riset_akunController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [Riset_akunController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [Riset_akunController::class, 'resetPassword'])->name('password.update');


Route::get('/profil', function () {
    return view('pengunjung.profil_pengunjung');
});

// Route Validasi QR Code Digital
Route::get('/validasi-dokumen', [ValidasiController::class, 'index'])->name('validasi.dokumen');


// Route::get('/catatan_konsul', function () {
//     return view('pengunjung.catatan_konsul');
// });
/*
|--------------------------------------------------------------------------
| HALAMAN PUBLIK & PENGUNJUNG (GUEST)
|--------------------------------------------------------------------------
*/

// Grouping untuk Pengunjung yang BELUM Login
Route::middleware('guest')->group(function () {
    // Halaman Login Pengunjung
    Route::get('/pengunjung', function () {
        return view('pengunjung.login_pengunjung');
    })->name('login_pengunjung');

    // Proses Login Pengunjung
    Route::post('/login-proses-pengunjung', [LoginPengunjungController::class, 'loginProses'])->name('login.proses.pengunjung');

    // Halaman Register Pengunjung
    Route::get('/register', function () {
        return view('pengunjung.register_pengunjung');
    })->name('register');

    // Proses Simpan Register
    Route::post('/register-pengunjung', [RegisterPengunjungController::class, 'store'])->name('register.submit');
    
});

/*
|--------------------------------------------------------------------------
| AREA WAJIB LOGIN PENGUNJUNG (AUTH)
|--------------------------------------------------------------------------
*/

Route::middleware('ceklogin')->group(function () {
    // Dashboard Pengunjung
    Route::get('/pengunjung/dashboard', [DashbordPengunjungController::class, 'index'])->name('dashboard.pengunjung');
    Route::get('/dashboard-pengunjung', [DashbordPengunjungController::class, 'index'])->name('pengunjung.dashboard');
    // Logout Pengunjung
    Route::post('/logout-pengunjung', [LoginPengunjungController::class, 'logout'])->name('logout.pengunjung');
    Route::get('/riwayat-antrian', [HistoryTiketController::class, 'index'])->name('antrian.history');
    // Fitur Tambahan Pengunjung
    Route::get('/api/estimasi-antrian-realtime', [EstimasiWaktuController::class, 'getEstimasiDinamis']);
    Route::post('/pengunjung/antrian/batal/{id}', [DashbordPengunjungController::class, 'batalAntrian'])->name('antrian.batal');
    Route::post('/pengunjung/antrian/check-in/{id}', [DashbordPengunjungController::class, 'checkIn'])->name('antrian.checkin');
    Route::get('/cek-status-skm', [AntrianController::class, 'cekStatusSkm'])->name('skm.cek');
    Route::post('/simpan-skm', [AntrianController::class, 'simpanSkm'])->name('skm.simpan');
    Route::get('/monitor-antrian', [AntrianController::class, 'monitor'])->name('monitor.antrian');
    Route::get('/catatan_konsul', [CatatanKonsulController::class, 'index'])->name('catatan.konsul');
    Route::get('/profil', [ProfilPengunjungController::class, 'index'])->name('profil.pengunjung');
    Route::post('/profil/update-foto', [ProfilPengunjungController::class, 'updateFoto'])->name('profil.update_foto');
    Route::post('/profil/upload-prioritas', [ProfilPengunjungController::class, 'uploadPrioritas'])->name('profil.upload_prioritas');
    Route::get('/syarat-layanan', [InformasiSyaratController::class, 'index'])->name('informasi.syarat');
    // Route::get('/konsultasi', [KonsulController::class, 'index'])->name('konsul.index');
    Route::get('/konsultasi/room/{id_loket?}', [ChatKonsultasiController::class, 'index'])->name('chatkonsultasi.index');
    Route::post('/konsultasi/kirim/{id_loket}', [ChatKonsultasiController::class, 'kirimPesan'])->name('chatkonsultasi.kirim');
    Route::post('/chat-konsultasi/edit/{id}', [ChatKonsultasiController::class, 'editPesan'])->name('chatkonsultasi.edit');
    Route::delete('/chat-konsultasi/hapus/{id}', [ChatKonsultasiController::class, 'hapusPesan'])->name('chatkonsultasi.hapus');
    Route::post('/konsultasi', [KonsulController::class, 'store'])->name('konsul.store');
    Route::get('/pusat-informasi', [InformasiController::class, 'index'])->name('pusat.informasi');
    Route::get('/status-antrian/{id}', [AntrianController::class, 'statusPengunjung'])->name('antrian.status');
    
    // Fitur Booking Antrian Terjadwal
    Route::get('/booking-antrian', [BookingAntrianController::class, 'index'])->name('booking.index');
    Route::post('/api/booking/get-kuota', [BookingAntrianController::class, 'getKuota'])->name('booking.kuota');
    Route::post('/booking-antrian', [BookingAntrianController::class, 'store'])->name('booking.store');
});

// Route penunjang lainnya (Manual sesuai kode Anda)
Route::post('/antrian/store-voice', [AntrianController::class, 'storeFromVoice']);
Route::post('/register-pengunjung', [RegisterPengunjungController::class, 'store'])->name('register.submit');
Route::get('/admin-loket/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
Route::get('/status-antrian/{id}', [AntrianController::class, 'statusPengunjung'])->name('antrian.status');

/*
|--------------------------------------------------------------------------
| AUTH PEGAWAI
|--------------------------------------------------------------------------
*/

Route::get('/login_karyawan', [LoginController::class, 'index'])->name('login');
Route::post('/login/proses', [LoginController::class, 'proses'])->name('login.proses');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| AREA WAJIB LOGIN PEGAWAI (DIBUNGKUS CEKLOGIN)
|--------------------------------------------------------------------------
*/

Route::middleware(['ceklogin'])->group(function () {

    // --- FITUR YANG BISA DIAKSES SEMUA ROLE (ADMIN & SUPER) ---
    Route::get('/admin-loket/jadwal/cetak', [JadwalController::class, 'cetak'])->name('jadwal.cetak_loket');
    Route::get('/admin-loket/profil', [ProfilController::class, 'index'])->name('profil.loket');
    Route::post('/admin-loket/profil/update', [ProfilController::class, 'update'])->name('profil.loket.update');
    Route::get('/admin-loket/data-antrian', [DataAntrianController::class, 'index'])->name('data.antrian');
    Route::get('/admin-loket/data-antrian/cetak', [DataAntrianController::class, 'cetak'])->name('data.antrian.cetak');
    Route::get('/loket/konsultasi/cetak', [KonsultasiController::class, 'cetakPDF'])->name('loket.konsultasi.cetak');
    Route::get('/loket/konsultasi', [KonsultasiController::class, 'index'])->name('loket.konsultasi');
    Route::get('/laporan-skm', [LaporanSkmController::class, 'index'])->name('laporan.skm');
    Route::get('/laporan-skm/cetak', [LaporanSkmController::class, 'cetak'])->name('laporan.skm.cetak');
    Route::post('/laporan-skm/store-soal', [LaporanSkmController::class, 'storeSoal'])->name('laporan.skm.store-soal');
    Route::post('/loket/konsultasi/update-status', [KonsultasiController::class, 'updateStatus'])->name('konsultasi.updateStatus');
    Route::post('/loket/antrian/{id}/panggil', [LoketDashboardController::class, 'panggil'])->name('antrian.panggil');
    Route::post('/loket/antrian/{id}/selesai', [LoketDashboardController::class, 'selesai'])->name('antrian.selesai');
    Route::post('/loket/antrian/{id}/tolak-prioritas', [LoketDashboardController::class, 'tolakPrioritas'])->name('loket.antrian.tolakPrioritas');
    Route::post('/pelayanan/toggle', [LoketDashboardController::class, 'togglePelayanan'])->name('pelayanan.toggle');
    Route::get('/laporan-skm/edit/{id}', [LaporanSkmController::class, 'edit'])->name('laporan.skm.edit');
    Route::get('/laporan-skm/delete/{id}', [LaporanSkmController::class, 'delete'])->name('laporan.skm.delete');
    // ================= ROUTE PERSYARATAN LOKET YANG SUDAH DIPERBAIKI =================
    Route::get('/admin-loket/persyaratan', [PersyaratanLoketController::class, 'index'])->name('persyaratan.index');
    Route::post('/admin-loket/persyaratan/store', [PersyaratanLoketController::class, 'store'])->name('persyaratan.store');
    Route::put('/admin-loket/persyaratan/update/{id}', [PersyaratanLoketController::class, 'update'])->name('persyaratan.update');
    Route::delete('/admin-loket/persyaratan/delete/{id}', [PersyaratanLoketController::class, 'destroy'])->name('persyaratan.destroy');
    // ================= ROUTE NOTIFIKASI LOKET BERMASALAH =================
    Route::get('/admin-loket/notif-bermasalah', [InformasiLoketController::class, 'index'])->name('notif_bermasalah.index');
    Route::post('/admin-loket/notif-bermasalah/store', [InformasiLoketController::class, 'store'])->name('notif_bermasalah.store');
    Route::put('/admin-loket/notif-bermasalah/update/{id}', [InformasiLoketController::class, 'update'])->name('notif_bermasalah.update');
    Route::delete('/admin-loket/notif-bermasalah/delete/{id}', [InformasiLoketController::class, 'destroy'])->name('notif_bermasalah.destroy');
    // Tambahkan rute ini di barisan rute loket / admin milikmu
    Route::post('/antrian/update-status', [LoketDashboardController::class, 'updateStatus'])->name('antrian.updateStatus');
    Route::get('/admin-loket/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/admin-loket/absensi/store', [AbsensiController::class, 'store'])->name('absensi.store');
    Route::post('/admin-loket/absensi/pulang', [AbsensiController::class, 'pulang'])->name('absensi.pulang');
    Route::post('/admin-loket/absensi/ajukan-izin', [AbsensiController::class, 'ajukanIzin'])->name('absensi.ajukanIzin');

    // ================= KHUSUS ADMIN LOKET =================
    Route::middleware('role:admin_loket')->group(function () {
        Route::get('/dashboard', [LoketDashboardController::class, 'index'])->name('dashboard');
        Route::get('/algoritma', [AlgoritmaController::class, 'index'])->name('algoritma.index');
        Route::post('/algoritma/store', [AlgoritmaController::class, 'store'])->name('algoritma.store');
        Route::put('/algoritma/update/{id}', [AlgoritmaController::class, 'update'])->name('algoritma.update');
        Route::delete('/algoritma/delete/{id}', [AlgoritmaController::class, 'destroy'])->name('algoritma.destroy');

        // Chat Konsultasi Loket
        Route::get('/chat_loket/{id_pengunjung?}', [ChatLoketController::class, 'index'])->name('chatloket.index');
        Route::post('/chat_loket/kirim/{id_pengunjung}', [ChatLoketController::class, 'kirimPesan'])->name('chatloket.kirim');
        Route::post('/chat_loket/edit/{id}', [ChatLoketController::class, 'editPesan'])->name('chatloket.edit');
        Route::delete('/chat_loket/hapus/{id}', [ChatLoketController::class, 'hapusPesan'])->name('chatloket.hapus');
    });

    // ================= KHUSUS SUPER ADMIN =================
    Route::middleware('role:administrator')->group(function () {
        Route::get('/super/dashboard', [SuperAdminController::class, 'index'])->name('super.dashboard');

        Route::prefix('super-admin/data-akun')->group(function () {
            Route::get('/', [DataAkunController::class, 'index'])->name('data-akun.index');
            Route::post('/store', [DataAkunController::class, 'store'])->name('data-akun.store');
            Route::post('/undang', [DataAkunController::class, 'undangKaryawan'])->name('data_akun.undang');
            Route::post('/update/{id}', [DataAkunController::class, 'update'])->name('data-akun.update');
            Route::post('/reset-password/{id}', [DataAkunController::class, 'sendResetLink'])->name('data-akun.reset');
            Route::delete('/delete/{id}', [DataAkunController::class, 'destroy'])->name('data-akun.delete');
        });

        Route::get('/super-admin/jadwal', [DataJadwalController::class, 'index'])->name('super.jadwal.index');
        Route::post('/super-admin/jadwal/store', [DataJadwalController::class, 'store'])->name('super.jadwal.store');
        Route::put('/super-admin/jadwal/update/{id}', [DataJadwalController::class, 'update'])->name('super.jadwal.update');
        Route::delete('/super-admin/jadwal/{id}', [DataJadwalController::class, 'destroy'])->name('super.jadwal.destroy');
        Route::get('/super-admin/jadwal/cetak', [DataJadwalController::class, 'cetak'])->name('super.jadwal.cetak');
          
        // Validasi Prioritas
        Route::get('/super-admin/validasi-prioritas', [ValidasiPrioritasController::class, 'index'])->name('admin.validasi_prioritas');
        Route::get('/super-admin/validasi-prioritas/cetak', [ValidasiPrioritasController::class, 'cetak'])->name('admin.validasi_prioritas.cetak');
        Route::post('/super-admin/validasi-profil/{id}/setuju', [ValidasiPrioritasController::class, 'setujuProfil'])->name('admin.validasi_profil.setuju');
        Route::post('/super-admin/validasi-profil/{id}/tolak', [ValidasiPrioritasController::class, 'tolakProfil'])->name('admin.validasi_profil.tolak');
        Route::post('/super-admin/validasi-profil/{id}/edit', [ValidasiPrioritasController::class, 'editProfil'])->name('admin.validasi_profil.edit');
        Route::post('/super-admin/validasi-profil/{id}/hapus', [ValidasiPrioritasController::class, 'hapusProfil'])->name('admin.validasi_profil.hapus');
        Route::post('/super-admin/validasi-dokumen-base64', [ValidasiPrioritasController::class, 'getDokumenBase64'])->name('admin.validasi_dokumen.base64');

        Route::get('/api/get-karyawan/{id_loket}', [DataJadwalController::class, 'getKaryawanByLoket']);
        Route::get('/data-absensi', [DataAbsensiController::class, 'index'])->name('data_absensi.index');
        Route::get('/data-absensi/cetak', [DataAbsensiController::class, 'cetak'])->name('data_absensi.cetak');
        Route::get('/data-absensi/surat-izin/{filename}', [DataAbsensiController::class, 'showSuratIzin'])->name('data_absensi.suratIzin');
        Route::get('/data-absensi/get-karyawan-satu-loket', [DataAbsensiController::class, 'getKaryawanSatuLoket']);
        Route::post('/data-absensi/tukar-shift', [DataAbsensiController::class, 'tukarShift'])->name('data_absensi.tukarShift');
        Route::get('/data-loket', [DataLoketController::class, 'index'])->name('loket.index');
        Route::post('/data-loket/store', [DataLoketController::class, 'store'])->name('loket.store');
        Route::post('/data-loket/update/{id}', [DataLoketController::class, 'update'])->name('loket.update');
        Route::delete('/data-loket/delete/{id}', [DataLoketController::class, 'destroy'])->name('loket.destroy');
        Route::get('/super-admin/data-skm', [DataSkmController::class, 'index'])->name('data-skm.index');
        Route::get('/super-admin/data-skm/cetak', [DataSkmController::class, 'cetakGlobal'])->name('data-skm.cetak');
        Route::get('/super-admin/data-pengunjung', [DataPengunjungController::class, 'index'])->name('super.pengunjung.index');
        Route::get('/super-admin/data-pengunjung/cetak', [DataPengunjungController::class, 'cetak'])->name('super.pengunjung.cetak');

        // Route Data Kinerja Karyawan
        Route::get('/super-admin/data-kinerja', [DataKinerjaLoketController::class, 'index'])->name('data-kinerja.index');
        Route::get('/super-admin/data-kinerja/cetak', [DataKinerjaLoketController::class, 'cetak'])->name('data-kinerja.cetak');
        Route::post('/super-admin/data-kinerja/store', [DataKinerjaLoketController::class, 'store'])->name('data-kinerja.store');
        Route::delete('/super-admin/data-kinerja/delete/{id}', [DataKinerjaLoketController::class, 'destroy'])->name('data-kinerja.destroy');

        // Route Laporan Beban Kerja Karyawan
        Route::get('/super-admin/data-beban-kerja', [DataBebanKerjaController::class, 'index'])->name('beban-kerja.index');
        Route::get('/super-admin/data-beban-kerja/cetak', [DataBebanKerjaController::class, 'cetak'])->name('beban-kerja.cetak');

        // Route Data Loket Bermasalah
        Route::get('/super-admin/data-loket-bermasalah', [DataLoketBermasalahcontroller::class, 'index'])->name('data-loket-bermasalah.index');
        Route::get('/super-admin/data-loket-bermasalah/cetak', [DataLoketBermasalahcontroller::class, 'cetak'])->name('data-loket-bermasalah.cetak');
        Route::post('/super-admin/data-loket-bermasalah/store', [DataLoketBermasalahcontroller::class, 'store'])->name('data-loket-bermasalah.store');
        Route::post('/super-admin/data-loket-bermasalah/update/{id}', [DataLoketBermasalahcontroller::class, 'update'])->name('data-loket-bermasalah.update');
        Route::delete('/super-admin/data-loket-bermasalah/delete/{id}', [DataLoketBermasalahcontroller::class, 'destroy'])->name('data-loket-bermasalah.destroy');
        
        // Route Data Booking Antrean
        Route::get('/super-admin/data-booking', [DataBookingController::class, 'index'])->name('superadmin.booking.index');
        Route::get('/super-admin/data-booking/cetak', [DataBookingController::class, 'cetak'])->name('superadmin.booking.cetak');
        Route::post('/super-admin/data-booking/update', [DataBookingController::class, 'updatePengaturan'])->name('superadmin.booking.update');

        // Data Training & Machine Learning
        Route::get('/super-admin/data-training', [DataTrainingController::class, 'index'])->name('super.training.index');
        Route::post('/super-admin/data-training/update-mode', [DataTrainingController::class, 'updateMode'])->name('super.training.updateMode');
        Route::post('/super-admin/data-training/train', [DataTrainingController::class, 'train'])->name('super.training.train');
        Route::delete('/super-admin/data-training/voice/{id}', [DataTrainingController::class, 'destroyVoice'])->name('super.training.destroyVoice');

        // Log Gangguan Sistem & Fallback Manual (Revisi Penguji)
        Route::get('/super-admin/log-sistem', [LogSistemController::class, 'index'])->name('admin.log_sistem.index');
        Route::post('/super-admin/log-sistem/clear', [LogSistemController::class, 'clear'])->name('admin.log_sistem.clear');
    });
});