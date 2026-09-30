<?php

namespace App\Console\Commands;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Console\Command;

class GoogleDriveOAuthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drive:auth {--client_id= : Google Client ID} {--client_secret= : Google Client Secret}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otorisasi OAuth 2.0 Google Drive untuk mendapatkan Refresh Token akun pribadi';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('=== Setup OAuth 2.0 Google Drive MANDALA RS ===');

        $clientId = $this->option('client_id') ?? env('GOOGLE_DRIVE_CLIENT_ID');
        $clientSecret = $this->option('client_secret') ?? env('GOOGLE_DRIVE_CLIENT_SECRET');

        if (empty($clientId)) {
            $clientId = $this->ask('Masukkan Google Client ID Anda');
        }

        if (empty($clientSecret)) {
            $clientSecret = $this->secret('Masukkan Google Client Secret Anda');
        }

        if (empty($clientId) || empty($clientSecret)) {
            $this->error('Client ID dan Client Secret wajib diisi.');

            return Command::FAILURE;
        }

        $client = new Client;
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri('https://developers.google.com/oauthplayground');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->addScope(Drive::DRIVE);

        $authUrl = $client->createAuthUrl();

        $this->line('');
        $this->info('1. Buka URL berikut di browser untuk memberikan izin:');
        $this->line("<comment>{$authUrl}</comment>");
        $this->line('');
        $this->info('2. Setelah menyetujui, Google akan mengarahkan ke halaman dengan kode otorisasi (parameter "code=..." di URL atau di layar).');

        $authCode = $this->ask('3. Tempelkan Authorization Code tersebut di sini');

        if (empty($authCode)) {
            $this->error('Authorization Code tidak boleh kosong.');

            return Command::FAILURE;
        }

        // Clean code in case user pasted the whole URL
        if (str_contains($authCode, 'code=')) {
            parse_str(parse_url($authCode, PHP_URL_QUERY), $queryParams);
            $authCode = $queryParams['code'] ?? $authCode;
        }

        try {
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            if (isset($accessToken['error'])) {
                $this->error('Gagal mendapatkan token: '.($accessToken['error_description'] ?? $accessToken['error']));

                return Command::FAILURE;
            }

            $refreshToken = $accessToken['refresh_token'] ?? null;

            if (! $refreshToken) {
                $this->warn('Google tidak mengembalikan Refresh Token baru (kemungkinan akun sudah pernah diotorisasi sebelumnya).');
                $this->line('Coba jalankan kembali dengan memastikan prompt consent aktif.');

                return Command::FAILURE;
            }

            $this->line('');
            $this->info('✓ Otorisasi Berhasil! Refresh Token telah didapatkan.');
            $this->line('');
            $this->line('Silakan tambahkan / perbarui variabel berikut pada file <info>.env</info> Anda:');
            $this->line('');
            $this->line("<comment>GOOGLE_DRIVE_CLIENT_ID={$clientId}</comment>");
            $this->line("<comment>GOOGLE_DRIVE_CLIENT_SECRET={$clientSecret}</comment>");
            $this->line("<comment>GOOGLE_DRIVE_REFRESH_TOKEN={$refreshToken}</comment>");
            $this->line('');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Terjadi kesalahan: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
