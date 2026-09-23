<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoiceTraining extends Model
{
    use HasFactory;

    protected $table = 'voice_training';
    protected $primaryKey = 'id_training'; // Sesuai gambar DB kamu
    protected $fillable = [
        'id_loket', 
        'teks_transkripsi', 
        'file_audio', 
        'sumber_data'
    ];
    
    public $timestamps = false;

    /**
     * Relasi ke model Loket
     * VoiceTraining dimiliki oleh satu Loket
     */
    public function loket()
    {
        return $this->belongsTo(Loket::class, 'id_loket', 'id_loket');
    }
}