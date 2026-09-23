<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = [
        'id_profil',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'shift',
        'setatus',
    ];

    // RELASI KE PROFIL
    public function profil()
    {
        return $this->belongsTo(Profil::class, 'id_profil', 'id_profil');
    }

    public function loket()
    {
        return $this->belongsTo(Loket::class, 'id_loket', 'id_loket');
    }
}
