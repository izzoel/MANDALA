<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kategori_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Insert default initial categories
        DB::table('kategori_pelatihans')->insertOrIgnore([
            ['nama' => 'Wajib', 'deskripsi' => 'Pelatihan wajib untuk keselamatan & K3 RS', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Fungsional', 'deskripsi' => 'Pelatihan kompetensi fungsional profesi', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Pilihan', 'deskripsi' => 'Pelatihan pilihan atau pengembangan diri', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Klinis', 'deskripsi' => 'Pelatihan klinis tenaga medis dan nakes', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Manajerial', 'deskripsi' => 'Pelatihan kepemimpinan dan manajemen', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('target_pelatihans', function (Blueprint $table) {
            $table->foreignId('pegawai_id')->nullable()->change();
            $table->foreignId('kategori_id')->nullable()->after('kategori')->constrained('kategori_pelatihans')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('target_pelatihans', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropColumn('kategori_id');
        });

        Schema::dropIfExists('kategori_pelatihans');
    }
};
