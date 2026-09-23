<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    protected $table = 'profil_karyawan';
    protected $primaryKey = 'id_profil';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nama_user',
        'status_devisi',
        'jenis_kelamin',
        'tanggal_lahir',
        'id_loket',
        'img_user',
        'email'
    ];

    public function user()
    {
        // Menunjuk ke id_user di tabel user
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'id_profil', 'id_profil');
    }

    public function poinKinerja()
    {
        return $this->hasMany(PoinKinerja::class, 'id_profil', 'id_profil');
    }

    public function loket()
    {
        // Pastikan foreign key di tabel profil adalah id_loket
        return $this->belongsTo(Loket::class, 'id_loket', 'id_loket');
    }
}