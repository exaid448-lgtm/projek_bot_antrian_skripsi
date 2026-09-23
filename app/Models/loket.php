<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loket extends Model
{
    use HasFactory;

    protected $table = 'loket';
    protected $primaryKey = 'id_loket';
    public $timestamps = false;

    protected $fillable = [
        'nama_loket',
        'status_pelayanan',
        'nama_pelayanan',
        'waktu_terakhir',
        'tanggal',
        'lokasi_loket',
        'prefix',
        'logo',
        'kuota_booking',
        'status_booking',
        'sesi_booking'
    ];

    public function konsul()
    {
        return $this->hasMany(Konsultasi::class, 'id_loket');
    }

    public function antrian()
    {
        return $this->hasMany(Antrian::class, 'id_loket', 'id_loket');
    }
    public function informasi()
    {
        return $this->hasMany(Informasi::class, 'id_loket', 'id_loket');
    }
    public function syarat()
    {
        // Parameter: ModelTarget, ForeignKey di Syarat, LocalKey di Loket
        return $this->hasMany(Syarat::class, 'id_loket', 'id_loket');
    }
}
