<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilNelayan extends Model
{
    use HasUuids;

    protected $table = 'profil_nelayan';

    protected $fillable = [
        'nelayan_id',
        'kelompok_id',
        'desa_id',
        'rt',
        'rw',
        'jenis_kapal',
        'nama_kapal',
        'no_registrasi_kapal',
        'jenis_tangkapan_utama',
        'foto_ktp',
        'foto_profil',
        'status_verifikasi',
        'catatan_verifikasi',
        'admin_id',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function nelayan(): BelongsTo
    {
        return $this->belongsTo(Nelayan::class, 'nelayan_id');
    }

    public function kelompokNelayan(): BelongsTo
    {
        return $this->belongsTo(KelompokNelayan::class, 'kelompok_id');
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class, 'desa_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
