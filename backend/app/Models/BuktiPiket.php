<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BuktiPiket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'foto_1',
        'foto_2',
        'deskripsi',
        'status_approval',
        'catatan_admin',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi Many-to-Many ke Task melalui tabel pivot detail_bukti_tugas
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'detail_bukti_tugas');
    }
}