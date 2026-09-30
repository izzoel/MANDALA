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
        Schema::table('sertifikats', function (Blueprint $table) {
            $table->string('drive_file_id')->nullable()->after('file_path');
            $table->text('drive_link')->nullable()->after('drive_file_id');
        });

        Schema::table('perguruan_tinggis', function (Blueprint $table) {
            $table->string('file_mou')->nullable()->after('email_pt');
            $table->string('drive_file_id')->nullable()->after('file_mou');
            $table->text('drive_link')->nullable()->after('drive_file_id');
        });

        Schema::table('permohonan_praktiks', function (Blueprint $table) {
            $table->string('drive_file_id')->nullable()->after('file_surat_permohonan');
            $table->text('drive_link')->nullable()->after('drive_file_id');
        });

        Schema::table('surat_persetujuans', function (Blueprint $table) {
            $table->string('drive_file_id')->nullable()->after('file_pdf');
            $table->text('drive_link')->nullable()->after('drive_file_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sertifikats', function (Blueprint $table) {
            $table->dropColumn(['drive_file_id', 'drive_link']);
        });

        Schema::table('perguruan_tinggis', function (Blueprint $table) {
            $table->dropColumn(['file_mou', 'drive_file_id', 'drive_link']);
        });

        Schema::table('permohonan_praktiks', function (Blueprint $table) {
            $table->dropColumn(['drive_file_id', 'drive_link']);
        });

        Schema::table('surat_persetujuans', function (Blueprint $table) {
            $table->dropColumn(['drive_file_id', 'drive_link']);
        });
    }
};
