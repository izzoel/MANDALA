<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google\Service\Drive\Permission;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected ?Client $client = null;

    protected ?Drive $driveService = null;

    protected ?string $clientId = null;

    protected ?string $clientSecret = null;

    protected ?string $refreshToken = null;

    protected ?string $serviceAccountPath = null;

    protected ?string $rootFolderId = null;

    public function __construct()
    {
        $this->clientId = config('services.google.client_id') ?? env('GOOGLE_DRIVE_CLIENT_ID');
        $this->clientSecret = config('services.google.client_secret') ?? env('GOOGLE_DRIVE_CLIENT_SECRET');
        $this->refreshToken = config('services.google.refresh_token') ?? env('GOOGLE_DRIVE_REFRESH_TOKEN');

        $jsonPath = config('services.google.service_account_json') ?? env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON');
        if ($jsonPath) {
            $this->serviceAccountPath = str_starts_with($jsonPath, '/') ? $jsonPath : base_path($jsonPath);
        }

        $folderId = config('services.google.folder_id') ?? env('GOOGLE_DRIVE_FOLDER_ID');
        if ($folderId && ! str_contains($folderId, '@')) {
            $this->rootFolderId = trim($folderId);
        }
    }

    /**
     * Check if OAuth2 credentials are configured.
     */
    public function isOAuth2Configured(): bool
    {
        return ! empty($this->clientId) && ! empty($this->clientSecret) && ! empty($this->refreshToken);
    }

    /**
     * Check if Service Account credentials exist.
     */
    public function isServiceAccountConfigured(): bool
    {
        return ! empty($this->serviceAccountPath) && file_exists($this->serviceAccountPath);
    }

    /**
     * Check if either OAuth2 or Service Account is configured.
     */
    public function isConfigured(): bool
    {
        return $this->isOAuth2Configured() || $this->isServiceAccountConfigured();
    }

    /**
     * Get initialized Google Drive client.
     */
    public function getDriveService(): ?Drive
    {
        if ($this->driveService !== null) {
            return $this->driveService;
        }

        try {
            $this->client = new Client;
            $this->client->addScope(Drive::DRIVE);
            $this->client->setAccessType('offline');

            if ($this->isOAuth2Configured()) {
                $this->client->setClientId($this->clientId);
                $this->client->setClientSecret($this->clientSecret);
                $this->client->refreshToken($this->refreshToken);
            } elseif ($this->isServiceAccountConfigured()) {
                $this->client->setAuthConfig($this->serviceAccountPath);
            } else {
                return null;
            }

            $this->driveService = new Drive($this->client);

            return $this->driveService;
        } catch (\Throwable $e) {
            Log::error('Google Drive initialization error: '.$e->getMessage(), ['exception' => $e]);

            return null;
        }
    }

    /**
     * Get or create a subfolder inside the root Google Drive folder.
     */
    public function getOrCreateFolder(string $folderName, ?string $parentId = null): ?string
    {
        $service = $this->getDriveService();
        if (! $service) {
            return null;
        }

        $parent = $parentId ?: $this->rootFolderId;
        $cacheKey = 'gdrive_folder_'.md5(($parent ?? 'root').'_'.$folderName);

        return Cache::remember($cacheKey, 3600, function () use ($service, $folderName, $parent) {
            try {
                $query = "name = '".addslashes($folderName)."' and mimeType = 'application/vnd.google-apps.folder' and trashed = false";
                if ($parent) {
                    $query .= " and '{$parent}' in parents";
                }

                $response = $service->files->listFiles([
                    'q' => $query,
                    'spaces' => 'drive',
                    'fields' => 'files(id, name)',
                    'pageSize' => 1,
                    'supportsAllDrives' => true,
                    'includeItemsFromAllDrives' => true,
                ]);

                if (count($response->getFiles()) > 0) {
                    return $response->getFiles()[0]->getId();
                }

                // Create folder
                $fileMetadata = new DriveFile([
                    'name' => $folderName,
                    'mimeType' => 'application/vnd.google-apps.folder',
                ]);

                if ($parent) {
                    $fileMetadata->setParents([$parent]);
                }

                $folder = $service->files->create($fileMetadata, [
                    'fields' => 'id',
                    'supportsAllDrives' => true,
                ]);

                return $folder->getId();
            } catch (\Throwable $e) {
                Log::warning("Could not create/find Google Drive folder '{$folderName}': ".$e->getMessage());

                return $parent;
            }
        });
    }

    /**
     * Upload a file to Google Drive.
     *
     * @param  UploadedFile|string  $file  UploadedFile instance or local absolute file path
     * @param  string  $subfolder  Subfolder name in Drive (e.g. 'sertifikat', 'mou', 'surat_permohonan')
     * @param  string|null  $customFileName  Custom filename
     */
    public function uploadFile($file, string $subfolder = '', ?string $customFileName = null): array
    {
        $service = $this->getDriveService();
        if (! $service) {
            return [
                'success' => false,
                'error' => 'Google Drive belum dikonfigurasi (OAuth2 / Service Account).',
                'id' => null,
                'webViewLink' => null,
                'webContentLink' => null,
                'name' => null,
            ];
        }

        try {
            if ($file instanceof UploadedFile) {
                $fileName = $customFileName ?: (time().'_'.preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName()));
                $mimeType = $file->getMimeType() ?: 'application/octet-stream';
                $content = file_get_contents($file->getRealPath());
            } elseif (is_string($file) && file_exists($file)) {
                $fileName = $customFileName ?: basename($file);
                $mimeType = mime_content_type($file) ?: 'application/octet-stream';
                $content = file_get_contents($file);
            } else {
                return [
                    'success' => false,
                    'error' => 'File yang diberikan tidak valid.',
                    'id' => null,
                    'webViewLink' => null,
                    'webContentLink' => null,
                    'name' => null,
                ];
            }

            $driveFile = new DriveFile;
            $driveFile->setName($fileName);
            $driveFile->setDescription('Uploaded from MANDALA RS System');

            // Set parent folder
            $targetFolderId = null;
            if (! empty($subfolder)) {
                $targetFolderId = $this->getOrCreateFolder($subfolder);
            } elseif (! empty($this->rootFolderId)) {
                $targetFolderId = $this->rootFolderId;
            }

            if ($targetFolderId) {
                $driveFile->setParents([$targetFolderId]);
            }

            $createdFile = $service->files->create($driveFile, [
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id, name, mimeType, webViewLink, webContentLink',
                'supportsAllDrives' => true,
            ]);

            // Set public read permission so anyone with link can view
            try {
                $permission = new Permission([
                    'type' => 'anyone',
                    'role' => 'reader',
                ]);
                $service->permissions->create($createdFile->getId(), $permission, [
                    'supportsAllDrives' => true,
                ]);
            } catch (\Throwable $permException) {
                Log::notice('Could not set public permission on Drive file: '.$permException->getMessage());
            }

            return [
                'success' => true,
                'id' => $createdFile->getId(),
                'name' => $createdFile->getName(),
                'mimeType' => $createdFile->getMimeType(),
                'webViewLink' => $createdFile->getWebViewLink(),
                'webContentLink' => $createdFile->getWebContentLink(),
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to upload file to Google Drive: '.$e->getMessage(), ['exception' => $e]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'id' => null,
                'webViewLink' => null,
                'webContentLink' => null,
                'name' => null,
            ];
        }
    }

    /**
     * Upload with local storage safety backup.
     */
    public function uploadWithFallback(UploadedFile $file, string $folder, ?string $customFileName = null): array
    {
        // 1. Always save locally first as reliable backup
        $localPath = $file->store($folder, 'public');

        // 2. Upload to Google Drive
        $driveResult = $this->uploadFile($file, $folder, $customFileName);

        return [
            'file_path' => $localPath,
            'drive_file_id' => $driveResult['id'] ?? null,
            'drive_link' => $driveResult['webViewLink'] ?? null,
            'drive_success' => $driveResult['success'] ?? false,
            'error' => $driveResult['error'] ?? null,
        ];
    }

    /**
     * Download / stream file content from Google Drive by File ID.
     */
    public function getFileStream(string $fileId)
    {
        $service = $this->getDriveService();
        if (! $service) {
            return null;
        }

        try {
            $response = $service->files->get($fileId, [
                'alt' => 'media',
                'supportsAllDrives' => true,
            ]);

            return $response->getBody();
        } catch (\Throwable $e) {
            Log::error("Failed to download file {$fileId} from Google Drive: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Get file metadata from Google Drive.
     */
    public function getFileMetadata(string $fileId): ?DriveFile
    {
        $service = $this->getDriveService();
        if (! $service) {
            return null;
        }

        try {
            return $service->files->get($fileId, [
                'fields' => 'id, name, mimeType, size, webViewLink, webContentLink',
                'supportsAllDrives' => true,
            ]);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Delete file from Google Drive.
     */
    public function deleteFile(string $fileId): bool
    {
        $service = $this->getDriveService();
        if (! $service) {
            return false;
        }

        try {
            $service->files->delete($fileId, ['supportsAllDrives' => true]);

            return true;
        } catch (\Throwable $e) {
            Log::warning("Failed to delete file {$fileId} from Google Drive: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Test connection to Google Drive API.
     */
    public function testConnection(): array
    {
        $authMode = $this->isOAuth2Configured() ? 'OAuth 2.0 (User Account)' : ($this->isServiceAccountConfigured() ? 'Service Account' : 'Belum Dikonfigurasi');

        if (! $this->isConfigured()) {
            return [
                'connected' => false,
                'authMode' => $authMode,
                'message' => 'Konfigurasi Google Drive (OAuth2 / Service Account) belum lengkap pada file .env.',
            ];
        }

        try {
            $service = $this->getDriveService();
            if (! $service) {
                return [
                    'connected' => false,
                    'authMode' => $authMode,
                    'message' => 'Gagal menginisialisasi Google Drive Client.',
                ];
            }

            $about = $service->about->get([
                'fields' => 'user, storageQuota',
            ]);

            $email = $about->getUser() ? $about->getUser()->getEmailAddress() : 'Unknown';
            $quota = $about->getStorageQuota();
            $limitGb = $quota && $quota->getLimit() ? round($quota->getLimit() / (1024 * 1024 * 1024), 2).' GB' : 'Unlimited / Shared';
            $usageGb = $quota && $quota->getUsage() ? round($quota->getUsage() / (1024 * 1024 * 1024), 2).' GB' : '0 GB';

            return [
                'connected' => true,
                'authMode' => $authMode,
                'email' => $email,
                'quota' => "Terpakai: {$usageGb} dari {$limitGb}",
                'rootFolderId' => $this->rootFolderId ?: '(Root / My Drive)',
                'message' => "Terhubung dengan sukses ke Google Drive API sebagai {$email} ({$authMode})",
            ];
        } catch (\Throwable $e) {
            return [
                'connected' => false,
                'authMode' => $authMode,
                'message' => $e->getMessage(),
            ];
        }
    }
}
