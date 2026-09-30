<?php

namespace App\Console\Commands;

use App\Services\GoogleDriveService;
use Illuminate\Console\Command;

class TestGoogleDriveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drive:test {--upload : Test uploading a sample test file to Google Drive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tes koneksi Google Drive API dan Service Account JSON';

    /**
     * Execute the console command.
     */
    public function handle(GoogleDriveService $driveService): int
    {
        $this->info('Memeriksa Konfigurasi Google Drive API MANDALA...');

        $jsonConfig = config('services.google.service_account_json') ?? env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON');
        $folderId = config('services.google.folder_id') ?? env('GOOGLE_DRIVE_FOLDER_ID');

        $this->line("• Path Service Account: <comment>{$jsonConfig}</comment>");
        $this->line("• Folder ID Target: <comment>{$folderId}</comment>");

        $result = $driveService->testConnection();

        if ($result['connected']) {
            $this->info('✓ Berhasil terhubung ke Google Drive API!');
            $this->line("• Metode Autentikasi: <comment>{$result['authMode']}</comment>");
            $this->line("• Akun Google Terhubung: <info>{$result['email']}</info>");
            if (! empty($result['quota'])) {
                $this->line("• Kuota Penyimpanan: <info>{$result['quota']}</info>");
            }
            $this->line("• Root Folder ID: <info>{$result['rootFolderId']}</info>");

            if ($this->option('upload')) {
                $this->line('');
                $this->info('Menguji proses upload file ke Google Drive...');
                $tempFile = tempnam(sys_get_temp_dir(), 'mandala_test_');
                file_put_contents($tempFile, 'Ini adalah file uji coba integrasi Google Drive MANDALA RS '.now());

                $uploadResult = $driveService->uploadFile($tempFile, 'test', 'mandala_test_'.time().'.txt');
                @unlink($tempFile);

                if ($uploadResult['success']) {
                    $this->info('✓ Berhasil mengunggah file uji coba!');
                    $this->line("• File ID: <info>{$uploadResult['id']}</info>");
                    $this->line("• Link View: <info>{$uploadResult['webViewLink']}</info>");
                } else {
                    $this->error('✗ Gagal mengunggah file: '.($uploadResult['error'] ?? 'Unknown error'));
                }
            }

            return Command::SUCCESS;
        } else {
            $this->error('✗ Gagal terhubung ke Google Drive:');
            $this->line($result['message']);

            if (str_contains($result['message'], 'Google Drive API has not been used') || str_contains($result['message'], 'SERVICE_DISABLED')) {
                $this->line('');
                $this->warn('PENTING: Google Drive API belum diaktifkan pada Google Cloud Project Anda.');
                $this->line('Silakan buka link berikut untuk mengaktifkan Google Drive API:');
                $this->line('<info>https://console.developers.google.com/apis/api/drive.googleapis.com/overview</info>');
            }

            return Command::FAILURE;
        }
    }
}
