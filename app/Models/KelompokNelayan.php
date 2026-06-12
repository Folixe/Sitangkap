<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KelompokNelayan extends Model
{
    use HasUuids;

    protected $table = 'kelompok_nelayan';

    protected $fillable = [
        'desa_id',
        'nama_kelompok',
        'kode_kelompok',
        'nama_ketua',
        'no_telepon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class, 'desa_id');
    }

    public function profilNelayan(): HasMany
    {
        return $this->hasMany(ProfilNelayan::class, 'kelompok_id');
    }
}
