<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_tugas',
        'deskripsi',
    ];

    // Relasi Many-to-Many ke BuktiPiket melalui tabel pivot detail_bukti_tugas
    public function buktiPikets(): BelongsToMany
    {
        return $this->belongsToMany(BuktiPiket::class, 'detail_bukti_tugas');
    }
}