<?php

namespace App\Http\Controllers;

use App\Models\PerguruanTinggi;
use App\Models\PermohonanPraktik;
use App\Models\Sertifikat;
use App\Models\SuratPersetujuan;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __construct(
        protected GoogleDriveService $driveService
    ) {}

    public function view(Request $request, string $type, int|string $id)
    {
        return $this->handleDocument($type, $id, false);
    }

    public function download(Request $request, string $type, int|string $id)
    {
        return $this->handleDocument($type, $id, true);
    }

    protected function handleDocument(string $type, int|string $id, bool $download = false)
    {
        $fileInfo = $this->resolveDocument($type, $id);

        if (! $fileInfo) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        $driveFileId = $fileInfo['drive_file_id'] ?? null;
        $driveLink = $fileInfo['drive_link'] ?? null;
        $localPath = $fileInfo['local_path'] ?? null;
        $fileName = $fileInfo['name'] ?? 'dokumen.pdf';
        $mimeType = $fileInfo['mime_type'] ?? 'application/pdf';

        // 1. Try Google Drive Stream if drive_file_id is available
        if (! empty($driveFileId)) {
            $stream = $this->driveService->getFileStream($driveFileId);
            if ($stream) {
                $disposition = $download ? "attachment; filename=\"{$fileName}\"" : "inline; filename=\"{$fileName}\"";

                return response()->stream(function () use ($stream) {
                    while (! $stream->eof()) {
                        echo $stream->read(1024 * 64);
                    }
                }, 200, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => $disposition,
                ]);
            }

            // Fallback to web link if stream failed
            if (! empty($driveLink) && ! $download) {
                return redirect()->away($driveLink);
            }
        }

        // 2. Try Local Storage
        if (! empty($localPath)) {
            if (Storage::disk('public')->exists($localPath)) {
                $filePath = Storage::disk('public')->path($localPath);
                $mimeType = mime_content_type($filePath) ?: 'application/pdf';

                if ($download) {
                    return response()->download($filePath, $fileName);
                }

                return response()->file($filePath, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => "inline; filename=\"{$fileName}\"",
                ]);
            }

            if (str_starts_with($localPath, 'http')) {
                return redirect()->away($localPath);
            }
        }

        // 3. Fallback to driveLink if available
        if (! empty($driveLink)) {
            return redirect()->away($driveLink);
        }

        abort(404, 'Berkas dokumen fisik tidak ditemukan di Google Drive maupun di penyimpanan lokal.');
    }

    protected function resolveDocument(string $type, int|string $id): ?array
    {
        switch ($type) {
            case 'sertifikat':
                $sertifikat = Sertifikat::find($id);
                if (! $sertifikat) {
                    return null;
                }

                return [
                    'drive_file_id' => $sertifikat->drive_file_id,
                    'drive_link' => $sertifikat->drive_link,
                    'local_path' => $sertifikat->file_path,
                    'name' => 'Sertifikat_'.preg_replace('/[^a-zA-Z0-9_-]/', '_', $sertifikat->nama_pelatihan).'.pdf',
                    'mime_type' => 'application/pdf',
                ];

            case 'mou':
                $pt = PerguruanTinggi::find($id);
                if (! $pt) {
                    return null;
                }

                return [
                    'drive_file_id' => $pt->drive_file_id,
                    'drive_link' => $pt->drive_link,
                    'local_path' => $pt->file_mou,
                    'name' => 'MoU_'.preg_replace('/[^a-zA-Z0-9_-]/', '_', $pt->nama_pt).'.pdf',
                    'mime_type' => 'application/pdf',
                ];

            case 'surat_permohonan':
                $booking = PermohonanPraktik::find($id);
                if (! $booking) {
                    return null;
                }

                return [
                    'drive_file_id' => $booking->drive_file_id,
                    'drive_link' => $booking->drive_link,
                    'local_path' => $booking->file_surat_permohonan,
                    'name' => 'Surat_Permohonan_'.($booking->prodi ?? 'Praktik').'.pdf',
                    'mime_type' => 'application/pdf',
                ];

            case 'surat_persetujuan':
                $persetujuan = SuratPersetujuan::find($id);
                if (! $persetujuan) {
                    return null;
                }

                return [
                    'drive_file_id' => $persetujuan->drive_file_id,
                    'drive_link' => $persetujuan->drive_link,
                    'local_path' => $persetujuan->file_pdf,
                    'name' => 'Surat_Persetujuan_'.preg_replace('/[^a-zA-Z0-9_-]/', '_', $persetujuan->nomor_surat).'.pdf',
                    'mime_type' => 'application/pdf',
                ];

            default:
                return null;
        }
    }
}
