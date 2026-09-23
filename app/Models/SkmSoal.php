<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkmSoal extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'skm_soal';

    // Primary Key tabel ini
    protected $primaryKey = 'id_soal';

    // === TAMBAHKAN BARIS INI UNTUK MEMATIKAN UPDATED_AT OTOMATIS ===
    public $timestamps = false;

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'id_loket',
        'pertanyaan',
        'is_active',
        'created_at'
    ];

    // Relasi ke tabel Loket (Setiap soal milik satu loket)
    public function loket()
    {
        return $this->belongsTo(Loket::class, 'id_loket', 'id_loket');
    }

    // Relasi ke Jawaban (Satu soal bisa punya banyak jawaban pengunjung)
    public function jawaban()
    {
        return $this->hasMany(SkmJawaban::class, 'id_soal', 'id_soal');
    }
}