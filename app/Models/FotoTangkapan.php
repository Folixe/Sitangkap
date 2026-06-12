<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoTangkapan extends Model
{
    use HasUuids;

    protected $table = 'foto_tangkapan';

    public $timestamps = false; // Uploaded at is database-managed

    protected $fillable = [
        'tangkapan_id',
        'file_path',
        'file_name',
        'ukuran_byte',
        'mime_type',
        'is_primary',
        'uploaded_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'uploaded_at' => 'datetime',
        'ukuran_byte' => 'integer',
    ];

    public function tangkapan(): BelongsTo
    {
        return $this->belongsTo(Tangkapan::class, 'tangkapan_id');
    }
}
