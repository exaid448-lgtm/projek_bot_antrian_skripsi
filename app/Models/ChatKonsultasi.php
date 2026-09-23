<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatKonsultasi extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk mendefinisikan nama tabel secara manual
    protected $table = 'chat_konsultasi'; 

    // Tentukan primary key jika bukan 'id' (misal: 'id_chat')
    protected $primaryKey = 'id_chat'; 

    protected $fillable = [
        'id_loket',
        'id_profil',
        'id_pengunjung',
        'tipe_pengirim',
        'pesan',
        'file_lampiran',
        'ukuran_file',
        'status_baca',
    ];


    // Relasi ke tabel loket/instansi
    public function loket()
    {
        return $this->belongsTo(Loket::class, 'id_loket');
    }

    // Relasi ke Karyawan/Petugas
    public function karyawan()
    {
        return $this->belongsTo(Profil::class, 'id_profil');
    }

    // Relasi ke Pengunjung
    public function pengunjung()
    {
        return $this->belongsTo(ProfilPengunjung::class, 'id_pengunjung');
    }
}