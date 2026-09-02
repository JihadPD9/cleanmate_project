<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SanksiSiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sanksi_id',
        'tipe_sanksi',
        'alasan',
        'status_penyelesaian',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sanksi(): BelongsTo
    {
        return $this->belongsTo(Sanksi::class);
    }
}