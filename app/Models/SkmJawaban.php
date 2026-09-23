<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkmJawaban extends Model
{
    use HasFactory;

    protected $table = 'skm_jawaban';

    protected $primaryKey = 'id_jawaban';

    // Matikan timestamps jika kamu hanya pakai created_at manual, 
    // tapi lebih baik biarkan true jika ada kolom updated_at juga.
    public $timestamps = true; 
    const UPDATED_AT = null;

    protected $fillable = [
        'id_soal',
        'id_loket',
        'id_antrain',
        'jawaban',
        'created_at'
    ];

    // Relasi balik ke Soal
    public function soal()
    {
        return $this->belongsTo(SkmSoal::class, 'id_soal', 'id_soal');
    }

    // Relasi ke Antrian (Untuk tahu ini jawaban dari antrian nomor berapa)
    public function antrian()
    {
        return $this->belongsTo(Antrian::class, 'id_antrain', 'id_antrian');
    }

    // Relasi ke Loket
    public function loket()
    {
        return $this->belongsTo(Loket::class, 'id_loket', 'id_loket');
    }
}
