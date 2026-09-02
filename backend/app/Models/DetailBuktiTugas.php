<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailBuktiTugas extends Model
{
    use HasFactory;

    protected $table = 'detail_bukti_tugas';

    protected $fillable = [
        'bukti_piket_id',
        'task_id',
    ];

    public function buktiPiket(): BelongsTo
    {
        return $this->belongsTo(BuktiPiket::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}