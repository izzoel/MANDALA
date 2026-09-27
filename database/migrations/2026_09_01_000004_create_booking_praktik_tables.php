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
        Schema::create('unit_prodi_kuotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->string('prodi');
            $table->integer('kuota_maks')->default(5);
            $table->timestamps();
        });

        Schema::create('permohonan_praktiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pt_id')->constrained('perguruan_tinggis')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->string('prodi');
            $table->integer('jumlah_mahasiswa');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->string('file_surat_permohonan')->nullable();
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak'])->default('diajukan');
            $table->text('catatan_diklat')->nullable();
            $table->timestamps();
        });

        Schema::create('surat_persetujuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonan_praktiks')->cascadeOnDelete();
            $table->string('nomor_surat');
            $table->string('file_pdf')->nullable();
            $table->foreignId('diterbitkan_oleh')->constrained('users')->cascadeOnDelete();
            $table->date('tgl_terbit');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('penunjukan_pembimbings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonan_praktiks')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->foreignId('pembimbing_lapangan_id')->nullable()->constrained('pembimbing_lapangans')->nullOnDelete();
            $table->foreignId('pembimbing_dosen_id')->nullable()->constrained('pembimbing_dosens')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('kriteria_penilaians', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kriteria');
            $table->decimal('bobot', 5, 2)->default(20.00);
            $table->integer('urutan')->default(1);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('penilaian_praktiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->foreignId('permohonan_id')->nullable()->constrained('permohonan_praktiks')->nullOnDelete();
            $table->foreignId('penilai_id')->constrained('users')->cascadeOnDelete();
            $table->enum('peran_penilai', ['lapangan', 'dosen'])->default('lapangan');
            $table->date('tgl_isi');
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->text('catatan_umum')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_id')->constrained('penilaian_praktiks')->cascadeOnDelete();
            $table->foreignId('kriteria_id')->constrained('kriteria_penilaians')->cascadeOnDelete();
            $table->decimal('nilai', 5, 2)->default(0.00);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penilaians');
        Schema::dropIfExists('penilaian_praktiks');
        Schema::dropIfExists('kriteria_penilaians');
        Schema::dropIfExists('penunjukan_pembimbings');
        Schema::dropIfExists('surat_persetujuans');
        Schema::dropIfExists('permohonan_praktiks');
        Schema::dropIfExists('unit_prodi_kuotas');
    }
};
