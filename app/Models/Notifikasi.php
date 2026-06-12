<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    use HasUuids;

    protected $table = 'notifikasi';

    protected $fillable = [
        'nelayan_id',
        'judul',
        'pesan',
        'tipe',
        'action_url',
        'is_read',
        'dibaca_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'dibaca_at' => 'datetime',
    ];

    public function nelayan(): BelongsTo
    {
        return $this->belongsTo(Nelayan::class, 'nelayan_id');
    }
}
