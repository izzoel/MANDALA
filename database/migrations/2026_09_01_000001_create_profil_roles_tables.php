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
        Schema::create('pegawai_non_asns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama');
            $table->string('no_pegawai')->unique();
            $table->string('unit_kerja');
            $table->string('jabatan');
            $table->date('tgl_mulai_kerja')->nullable();
            $table->timestamps();
        });

        Schema::create('perguruan_tinggis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pt');
            $table->boolean('status_mou')->default(true);
            $table->date('tgl_mulai_mou');
            $table->date('tgl_akhir_mou');
            $table->string('kontak')->nullable();
            $table->string('email_pt')->nullable();
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('nama_unit');
            $table->enum('mode_kuota', ['per_prodi', 'gabungan'])->default('gabungan');
            $table->integer('kuota_maks')->default(10);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('pembimbing_lapangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama');
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('kontak')->nullable();
            $table->string('nip_nik')->nullable();
            $table->timestamps();
        });

        Schema::create('pembimbing_dosens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pt_id')->constrained('perguruan_tinggis')->cascadeOnDelete();
            $table->string('nama');
            $table->string('kontak')->nullable();
            $table->string('nidn')->nullable();
            $table->timestamps();
        });

        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pt_id')->constrained('perguruan_tinggis')->cascadeOnDelete();
            $table->string('nama');
            $table->string('nim');
            $table->string('prodi');
            $table->foreignId('id_pembimbing_lapangan')->nullable()->constrained('pembimbing_lapangans')->nullOnDelete();
            $table->foreignId('id_pembimbing_dosen')->nullable()->constrained('pembimbing_dosens')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
        Schema::dropIfExists('pembimbing_dosens');
        Schema::dropIfExists('pembimbing_lapangans');
        Schema::dropIfExists('units');
        Schema::dropIfExists('perguruan_tinggis');
        Schema::dropIfExists('pegawai_non_asns');
    }
};
