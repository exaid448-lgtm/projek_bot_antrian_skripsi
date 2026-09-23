<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Algoritma extends Model
{
    protected $table = 'algoritma';
    protected $primaryKey = 'id_algoritma';
    
    // TAMBAHKAN INI: Matikan fitur timestamp otomatis
    public $timestamps = false; 

    protected $fillable = [
        'id_loket',
        'algoritma',
        'tanggal_dan_waktu',
        'tipe_layanan',
    ];

    // Relasi ke tabel loket
    public function loket()
    {
        return $this->belongsTo(Loket::class, 'id_loket', 'id_loket');
    }
}