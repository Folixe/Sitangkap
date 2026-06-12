<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTangkapan extends Model
{
    use HasUuids;

    protected $table = 'detail_tangkapan';

    protected $fillable = [
        'tangkapan_id',
        'jenis_ikan_id',
        'nama_ikan',
        'berat_kg',
        'jumlah_ekor',
        'keterangan',
    ];

    protected $casts = [
        'berat_kg' => 'decimal:2',
        'jumlah_ekor' => 'integer',
    ];

    public function tangkapan(): BelongsTo
    {
        return $this->belongsTo(Tangkapan::class, 'tangkapan_id');
    }

    public function jenisIkan(): BelongsTo
    {
        return $this->belongsTo(JenisIkan::class, 'jenis_ikan_id');
    }
}
