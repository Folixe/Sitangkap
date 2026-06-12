<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tangkapan extends Model
{
    use HasUuids;

    protected $table = 'tangkapan';

    protected $fillable = [
        'nelayan_id',
        'tanggal_penangkapan',
        'lokasi_nama',
        'latitude',
        'longitude',
        'kondisi_cuaca',
        'keterangan',
        'status',
        'admin_id',
        'verified_at',
    ];

    protected $casts = [
        'tanggal_penangkapan' => 'date',
        'verified_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function nelayan(): BelongsTo
    {
        return $this->belongsTo(Nelayan::class, 'nelayan_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailTangkapan::class, 'tangkapan_id');
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(FotoTangkapan::class, 'tangkapan_id');
    }
}
