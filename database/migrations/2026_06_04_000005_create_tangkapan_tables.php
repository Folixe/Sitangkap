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
        Schema::create('jenis_ikan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('admin_id')->nullable();
            $table->string('nama_lokal');
            $table->string('nama_ilmiah')->nullable();
            $table->string('kategori')->nullable();
            $table->string('foto_referensi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
        });

        Schema::create('tangkapan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('nelayan_id');
            $table->date('tanggal_penangkapan');
            $table->string('lokasi_nama')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('kondisi_cuaca')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->uuid('admin_id')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->foreign('nelayan_id')->references('id')->on('nelayan')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
        });

        Schema::create('detail_tangkapan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tangkapan_id');
            $table->uuid('jenis_ikan_id');
            $table->string('nama_ikan')->nullable();
            $table->decimal('berat_kg', 10, 2);
            $table->integer('jumlah_ekor')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('tangkapan_id')->references('id')->on('tangkapan')->onDelete('cascade');
            $table->foreign('jenis_ikan_id')->references('id')->on('jenis_ikan')->onDelete('restrict');
        });

        Schema::create('foto_tangkapan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tangkapan_id');
            $table->string('file_path');
            $table->string('file_name');
            $table->bigInteger('ukuran_byte')->nullable();
            $table->string('mime_type')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamp('uploaded_at')->useCurrent();

            $table->foreign('tangkapan_id')->references('id')->on('tangkapan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foto_tangkapan');
        Schema::dropIfExists('detail_tangkapan');
        Schema::dropIfExists('tangkapan');
        Schema::dropIfExists('jenis_ikan');
    }
};
