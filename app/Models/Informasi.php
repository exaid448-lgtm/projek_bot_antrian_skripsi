<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Informasi extends Model
{
    use HasFactory;

    protected $table = 'informasi';
    protected $primaryKey = 'id_informasi';

    protected $fillable = [
        'id_loket',
        'judul_info',
        'deskripsi_info',
        'solusi_info',
        'kategori_info',
        'status_info',
        'tanggal_info'
    ];

    // Relasi ke tabel Loket
    public function loket()
    {
        return $this->belongsTo(Loket::class, 'id_loket', 'id_loket');
    }
}