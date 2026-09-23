<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilPengunjung extends Model
{
    
    // 1. Nama tabel sesuai di PHPMyAdmin kamu
    protected $table = 'profil_pengunjung';

    // 2. Primary Key
    protected $primaryKey = 'id_pengunjung';

    // 3. Kolom yang boleh diisi
    protected $fillable = [
        'id_pengunjung',
        'nama',
        'email',
        'nomor_whatsapp',
        'tanggal_lahir',
        'jenis_kelamin',
        'foto',
        'id_user',
        'status_prioritas',
        'dokumen_prioritas',
        'jenis_prioritas',
        'tanggal_berakhir_prioritas',
        'alasan_penolakan_prioritas',
    ];

    // Laravel secara default mencari kolom 'created_at' dan 'updated_at'.
    // Jika di tabelmu tidak ada kolom tersebut, matikan fiturnya:
    public $timestamps = false;

    // Relasi balik ke User (Profil ini milik User siapa?)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Relasi ke Antrian
    public function antrian()
    {
        return $this->hasMany(Antrian::class, 'id_pengunjung', 'id_pengunjung');
    }
}