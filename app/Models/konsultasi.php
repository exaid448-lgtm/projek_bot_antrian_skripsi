<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konsultasi extends Model
{
    protected $table = 'konsul';

    protected $primaryKey = 'id_konsul';

    public $timestamps = false;

    protected $fillable = [
        'id_loket',
        'id_pengunjung',
        'konsultasi',
        'tanggal_konsul',
        'pelayanan_status',
    ];

    protected $casts = [
        'tanggal_konsul' => 'datetime',
    ];

    public function loket()
    {
        return $this->belongsTo(Loket::class, 'id_loket');
    }

    public function pengunjung()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    // app/Models/Konsultasi.php

    public function data_pengunjung()
    {
        return $this->belongsTo(\App\Models\ProfilPengunjung::class, 'id_pengunjung', 'id_pengunjung');
    }
}
