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
        Schema::create('kelompok_nelayan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('desa_id')->nullable();
            $table->string('nama_kelompok');
            $table->string('kode_kelompok');
            $table->string('nama_ketua')->nullable();
            $table->string('no_telepon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('desa_id')->references('id')->on('desa')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelompok_nelayan');
    }
};
