<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoinKinerja extends Model
{
    protected $table = 'poin_kinerja';
    protected $primaryKey = 'id_poin';
    public $timestamps = false; // Only created_at timestamp is present, we handle it manually or let DB handle it

    protected $fillable = [
        'id_profil',
        'tanggal',
        'jenis_pelanggaran',
        'waktu_kejadian',
        'poin_dipotong',
        'keterangan',
        'created_at'
    ];

    // Relasi ke Profil Karyawan
    public function profil()
    {
        return $this->belongsTo(Profil::class, 'id_profil', 'id_profil');
    }
}
