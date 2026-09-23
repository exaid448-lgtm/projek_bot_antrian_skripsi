<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'user'; 
    protected $primaryKey = 'id_user'; 
    public $incrementing = true; // Pastikan ini true
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = ['username', 'password', 'kategori', 'status_login','last_seen'];

    public function profil()
    {
        
        return $this->hasOne(Profil::class, 'id_user', 'id_user');
    }
    public function profil_pengunjung()
    {
        // id_user di profil_pengunjung (foreign key) merujuk ke id_user di tabel user (local key)
        return $this->hasOne(ProfilPengunjung::class, 'id_user', 'id_user');
    }
}