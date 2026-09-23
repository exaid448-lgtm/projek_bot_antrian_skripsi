<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class NomorAntrianController
{
    /**
     * Generate nomor antrian otomatis berdasarkan loket dan tanggal hari ini
     */
    public static function generateNomorAntrian($id_loket)
    {
        $tanggalHari = now()->toDateString();
        
        $nomorAntrianTerakhir = DB::table('antrian')
            ->whereDate('waktu_diberikan', $tanggalHari)
            ->where('id_loket', $id_loket)
            ->max('nomor_antrian') ?? 0;

        return $nomorAntrianTerakhir + 1;
    }

    /**
     * Buat entry antrian baru untuk pengunjung
     */
    public static function buatAntrian($id_konsul, $id_loket)
    {
        try {
            $nomorAntrian = self::generateNomorAntrian($id_loket);

            $id_antrian = DB::table('antrian')->insertGetId([
                'id_konsul'        => $id_konsul,
                'id_loket'         => $id_loket,
                'nomor_antrian'    => $nomorAntrian,
                'waktu_voice'      => now(),
                'waktu_panggil'    => null,
                'waktu_selesai'    => null,
                'jenis_antrian'    => null,
            ]);

            if (!$id_antrian) {
                throw new \Exception('Gagal insert antrian');
            }

            $antrian = DB::table('antrian')->find($id_antrian);
            
            if (!$antrian) {
                throw new \Exception('Antrian tidak ditemukan setelah insert');
            }

            return $antrian;
        } catch (\Exception $e) {
            Log::error('Error di buatAntrian: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Format nomor antrian dengan padding (001, 002, dst)
     */
    public static function formatNomorAntrian($nomor)
    {
        return str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Dapatkan nomor antrian terakhir untuk loket hari ini
     */
    public static function getNomorAntrianTerakhir($id_loket)
    {
        $tanggalHari = now()->toDateString();
        
        return DB::table('antrian')
            ->whereDate('waktu_diberikan', $tanggalHari)
            ->where('id_loket', $id_loket)
            ->max('nomor_antrian');
    }

    /**
     * Hitung total antrian yang menunggu untuk loket hari ini
     */
    public static function hitungAntrianMenunggu($id_loket)
    {
        $tanggalHari = now()->toDateString();
        
        return DB::table('antrian')
            ->whereDate('waktu_diberikan', $tanggalHari)
            ->where('id_loket', $id_loket)
            ->whereNull('waktu_voice')
            ->count();
    }

    /**
     * Mulai voice (ubah waktu_voice)
     */
    public static function mulaiVoice($id_antrian)
    {
        return DB::table('antrian')
            ->where('id_antrian', $id_antrian)
            ->update(['waktu_voice' => now()]);
    }

    /**
     * Panggil antrian (ubah waktu_panggil)
     */
    public static function panggilAntrian($id_antrian)
    {
        return DB::table('antrian')
            ->where('id_antrian', $id_antrian)
            ->update(['waktu_panggil' => now()]);
    }

    /**
     * Selesaikan antrian (ubah waktu_selesai)
     */
    public static function selesaiAntrian($id_antrian)
    {
        return DB::table('antrian')
            ->where('id_antrian', $id_antrian)
            ->update(['waktu_selesai' => now()]);
    }

    /**
     * Ambil data antrian berdasarkan ID
     */
    public static function getAntrianById($id_antrian)
    {
        return DB::table('antrian')
            ->join('konsul', 'antrian.id_konsul', '=', 'konsul.id_konsul')
            ->join('loket', 'antrian.id_loket', '=', 'loket.id_loket')
            ->where('antrian.id_antrian', $id_antrian)
            ->select('antrian.*', 'konsul.nama_pengunjung', 'loket.nama_loket')
            ->first();
    }

    /**
     * Reset nomor antrian untuk loket di tengah malam otomatis
     */
    public static function resetNomorAntrianHariBaru()
    {
        return true;
    }
}


