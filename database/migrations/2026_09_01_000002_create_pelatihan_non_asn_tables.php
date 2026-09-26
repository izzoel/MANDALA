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
        Schema::create('sertifikats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai_non_asns')->cascadeOnDelete();
            $table->string('nama_pelatihan');
            $table->string('penyelenggara');
            $table->date('tgl_pelaksanaan');
            $table->string('no_sertifikat');
            $table->string('file_path');
            $table->enum('status_verifikasi', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_verifikator')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('target_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai_non_asns')->cascadeOnDelete();
            $table->string('nama_target');
            $table->string('kategori')->default('Wajib'); // Wajib, Pilihan, Fungsional
            $table->string('periode')->default('2026');
            $table->date('tenggat');
            $table->enum('status', ['belum', 'proses', 'selesai'])->default('belum');
            $table->foreignId('sertifikat_pemenuhan_id')->nullable()->constrained('sertifikats')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_pelatihans');
        Schema::dropIfExists('sertifikats');
    }
};
