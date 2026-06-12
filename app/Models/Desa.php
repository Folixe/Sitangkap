<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Desa extends Model
{
    use HasUuids;

    protected $table = 'desa';

    protected $fillable = [
        'kecamatan_id',
        'nama',
        'kode',
    ];

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    public function kelompokNelayan(): HasMany
    {
        return $this->hasMany(KelompokNelayan::class, 'desa_id');
    }

    public function profilNelayan(): HasMany
    {
        return $this->hasMany(ProfilNelayan::class, 'desa_id');
    }
}
