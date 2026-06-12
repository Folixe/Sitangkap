<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisIkan extends Model
{
    use HasUuids;

    protected $table = 'jenis_ikan';

    protected $fillable = [
        'admin_id',
        'nama_lokal',
        'nama_ilmiah',
        'kategori',
        'foto_referensi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function detailTangkapan(): HasMany
    {
        return $this->hasMany(DetailTangkapan::class, 'jenis_ikan_id');
    }
}
