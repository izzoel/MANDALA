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
        Schema::create('target_pelatihan_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_pelatihan_id')->constrained('target_pelatihans')->cascadeOnDelete();
            $table->foreignId('peserta_id')->constrained('pesertas')->cascadeOnDelete();
            $table->enum('status', ['belum', 'proses', 'selesai'])->default('belum');
            $table->foreignId('sertifikat_id')->nullable()->constrained('sertifikats')->nullOnDelete();
            $table->timestamps();

            $table->unique(['target_pelatihan_id', 'peserta_id'], 'unique_pelatihan_peserta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_pelatihan_peserta');
    }
};
