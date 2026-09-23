<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $primaryKey = 'id_absen';

    public $timestamps = false;

    protected $fillable = [
        'id_profil',
        'waktu_masuk',
        'waktu_pulang',
        'tanggal',
        'status_absen',
        'surat_izin'
    ];

    public function profil()
    {
        return $this->belongsTo(Profil::class, 'id_profil');
    }
}
