<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nelayan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('password'); // standard password field
            $table->string('no_telepon')->nullable();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('status_akun', ['active', 'suspended', 'inactive'])->default('active');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });

        Schema::create('profil_nelayan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('nelayan_id');
            $table->uuid('kelompok_id')->nullable();
            $table->uuid('desa_id')->nullable();
            $table->string('rt');
            $table->string('rw');
            $table->string('jenis_kapal'); // Compreng, Jukung, Gilnet, Lainnya
            $table->string('nama_kapal')->nullable();
            $table->string('no_registrasi_kapal')->nullable();
            $table->string('jenis_tangkapan_utama')->nullable();
            $table->string('foto_ktp')->nullable();
            $table->string('foto_profil')->nullable();
            $table->enum('status_verifikasi', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('catatan_verifikasi')->nullable();
            $table->uuid('admin_id')->nullable(); // Admin who verified
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->foreign('nelayan_id')->references('id')->on('nelayan')->onDelete('cascade');
            $table->foreign('kelompok_id')->references('id')->on('kelompok_nelayan')->onDelete('set null');
            $table->foreign('desa_id')->references('id')->on('desa')->onDelete('set null');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_nelayan');
        Schema::dropIfExists('nelayan');
    }
};
