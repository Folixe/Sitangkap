<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Nelayan extends Authenticatable
{
    use HasUuids, Notifiable;

    protected $table = 'nelayan';

    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'no_telepon',
        'tempat_lahir',
        'tanggal_lahir',
        'status_akun',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function profil(): HasOne
    {
        return $this->hasOne(ProfilNelayan::class, 'nelayan_id');
    }

    public function tangkapan(): HasMany
    {
        return $this->hasMany(Tangkapan::class, 'nelayan_id');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'nelayan_id');
    }
}
