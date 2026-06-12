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
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('nelayan_id');
            $table->string('judul');
            $table->text('pesan');
            $table->string('tipe')->default('info');
            $table->string('action_url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamps();

            $table->foreign('nelayan_id')->references('id')->on('nelayan')->onDelete('cascade');
        });

        Schema::create('reset_password_nelayan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('nelayan_id');
            $table->string('token')->unique();
            $table->boolean('is_used')->default(false);
            $table->timestamp('expired_at');
            $table->timestamps();

            $table->foreign('nelayan_id')->references('id')->on('nelayan')->onDelete('cascade');
        });

        Schema::create('reset_password_admin', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('admin_id');
            $table->string('token')->unique();
            $table->boolean('is_used')->default(false);
            $table->timestamp('expired_at');
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });

        Schema::create('admin_log', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('admin_id')->nullable();
            $table->string('aksi');
            $table->string('tabel_target')->nullable();
            $table->uuid('id_target')->nullable();
            $table->json('data_sebelum')->nullable();
            $table->json('data_sesudah')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_log');
        Schema::dropIfExists('reset_password_admin');
        Schema::dropIfExists('reset_password_nelayan');
        Schema::dropIfExists('notifikasi');
    }
};
