<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Syarat extends Model
{
    use HasFactory;

    protected $table = 'syarat';
    protected $primaryKey = 'id_syarat';
    
    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'id_loket',
        'nama_syarat',
        'keterangan',
        'tipe_syarat'
    ];

    /**
     * Relasi ke tabel loket (Many to One)
     */
    public function loket()
    {
        return $this->belongsTo(Loket::class, 'id_loket', 'id_loket');
    }
}