<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi ke Jadwal Piket
    public function jadwalPikets(): HasMany
    {
        return $this->hasMany(JadwalPiket::class);
    }

    // Relasi ke Bukti Piket
    public function buktiPikets(): HasMany
    {
        return $this->hasMany(BuktiPiket::class);
    }

    // Relasi ke Sanksi Siswa
    public function sanksiSiswas(): HasMany
    {
        return $this->hasMany(SanksiSiswa::class);
    }
}