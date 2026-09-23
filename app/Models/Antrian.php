<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    use HasFactory;

    protected $table = 'antrian';
    protected $primaryKey = 'id_antrian'; // Sudah benar
    public $timestamps = false;

    protected $fillable = [
        'id_loket',
        'nomor_antrian',
        'waktu_voice',
        'waktu_panggil',
        'waktu_selesai',
        'jenis_antrian',
        'status_antrian',
        'setatus_pengambilan',
        'id_pengunjung',
        'id_karyawan',
        'is_prioritas',
        'jenis_prioritas',
        'dokumen_prioritas_sementara',
        'status_validasi_prioritas',
        'alasan_penolakan_prioritas',
        'kode_booking_unik',
        'tanggal_booking',
        'slot_waktu',
        'waktu_check_in',
        'batas_check_in',
        'status_booking',
        'notifikasi_email_dikirim',
        'waktu_notifikasi_email',
    ];

    protected $casts = [
        'waktu_voice'              => 'datetime',
        'waktu_panggil'            => 'datetime',
        'waktu_selesai'            => 'datetime',
        'tanggal_booking'          => 'date',
        'waktu_check_in'           => 'datetime',
        'notifikasi_email_dikirim' => 'boolean',
        'waktu_notifikasi_email'   => 'datetime',
    ];

    public function loket()
    {
        return $this->belongsTo(Loket::class, 'id_loket', 'id_loket');
    }

    public function pengunjung()
    {
        return $this->belongsTo(ProfilPengunjung::class, 'id_pengunjung', 'id_pengunjung');
    }
}