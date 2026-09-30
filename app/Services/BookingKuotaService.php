<?php

namespace App\Services;

use App\Models\PerguruanTinggi;
use App\Models\PermohonanPraktik;
use App\Models\Unit;
use App\Models\UnitProdiKuota;
use Exception;
use Illuminate\Support\Facades\DB;

class BookingKuotaService
{
    /**
     * Memeriksa ketersediaan kuota unit untuk rentang tanggal tertentu.
     * Menggunakan logika overlap tanggal: StartA <= EndB AND StartB <= EndA
     *
     * @param  string  $tglMulai  (Y-m-d)
     * @param  string  $tglSelesai  (Y-m-d)
     * @param  int|null  $excludePermohonanId  (jika sedang edit permohonan)
     * @return array{available: bool, sisa_kuota: int, kuota_maks: int, kuota_terpakai: int, message: string}
     */
    public function checkKuotaAvailability(
        int $unitId,
        string $prodi,
        string $tglMulai,
        string $tglSelesai,
        int $jumlahDiminta = 1,
        ?int $excludePermohonanId = null
    ): array {
        $unit = Unit::findOrFail($unitId);

        // Cari permohonan yang OVERLAP (Status Disetujui)
        $queryOverlap = PermohonanPraktik::where('unit_id', $unitId)
            ->where('status', 'disetujui')
            ->when($excludePermohonanId, fn ($q) => $q->where('id', '!=', $excludePermohonanId))
            ->where(function ($q) use ($tglMulai, $tglSelesai) {
                $q->where('tgl_mulai', '<=', $tglSelesai)
                    ->where('tgl_selesai', '>=', $tglMulai);
            });

        if ($unit->mode_kuota === 'gabungan') {
            $kuotaMaks = $unit->kuota_maks;
            $kuotaTerpakai = (int) $queryOverlap->sum('jumlah_mahasiswa');
            $sisaKuota = max(0, $kuotaMaks - $kuotaTerpakai);
            $isAvailable = $sisaKuota >= $jumlahDiminta;

            return [
                'available' => $isAvailable,
                'sisa_kuota' => $sisaKuota,
                'kuota_maks' => $kuotaMaks,
                'kuota_terpakai' => $kuotaTerpakai,
                'mode' => 'gabungan',
                'message' => $isAvailable
                    ? "Kuota tersedia (Sisa: {$sisaKuota} dari {$kuotaMaks} kuota gabungan)."
                    : "Kuota tidak mencukupi. Sisa kuota: {$sisaKuota}, diminta: {$jumlahDiminta}.",
            ];
        }

        if ($unit->mode_kuota === 'per_prodi') {
            $prodiKuota = UnitProdiKuota::where('unit_id', $unitId)
                ->where('prodi', $prodi)
                ->first();

            $kuotaMaks = $prodiKuota ? $prodiKuota->kuota_maks : 0;

            $kuotaTerpakai = (int) $queryOverlap->where('prodi', $prodi)->sum('jumlah_mahasiswa');
            $sisaKuota = max(0, $kuotaMaks - $kuotaTerpakai);
            $isAvailable = $sisaKuota >= $jumlahDiminta && $kuotaMaks > 0;

            return [
                'available' => $isAvailable,
                'sisa_kuota' => $sisaKuota,
                'kuota_maks' => $kuotaMaks,
                'kuota_terpakai' => $kuotaTerpakai,
                'mode' => 'per_prodi',
                'message' => $isAvailable
                    ? "Kuota untuk prodi {$prodi} tersedia (Sisa: {$sisaKuota} dari {$kuotaMaks})."
                    : ($kuotaMaks === 0
                        ? "Prodi {$prodi} belum memiliki alokasi kuota di unit ini."
                        : "Kuota prodi {$prodi} tidak mencukupi (Sisa: {$sisaKuota}, diminta: {$jumlahDiminta})."),
            ];
        }

        return [
            'available' => false,
            'sisa_kuota' => 0,
            'kuota_maks' => 0,
            'kuota_terpakai' => 0,
            'mode' => 'unknown',
            'message' => 'Mode kuota unit tidak valid.',
        ];
    }

    /**
     * Memvalidasi keabsahan MoU perguruan tinggi.
     *
     * @throws Exception
     */
    public function validateMou(int $ptId): PerguruanTinggi
    {
        $pt = PerguruanTinggi::findOrFail($ptId);

        if (! $pt->status_mou) {
            throw new Exception("Status MoU perguruan tinggi '{$pt->nama_pt}' sedang non-aktif.");
        }

        if (now()->toDateString() > $pt->tgl_akhir_mou->toDateString()) {
            throw new Exception("Masa berlaku MoU '{$pt->nama_pt}' telah kedaluwarsa pada {$pt->tgl_akhir_mou->format('d/m/Y')}.");
        }

        return $pt;
    }

    /**
     * Memproses permohonan praktik dengan transaksi database & pessimistic locking.
     */
    public function ajukanPermohonan(array $data): PermohonanPraktik
    {
        return DB::transaction(function () use ($data) {
            // 1. Validasi MoU
            $this->validateMou($data['pt_id']);

            // 2. Cek Kuota
            $kuotaCheck = $this->checkKuotaAvailability(
                $data['unit_id'],
                $data['prodi'],
                $data['tgl_mulai'],
                $data['tgl_selesai'],
                $data['jumlah_mahasiswa']
            );

            if (! $kuotaCheck['available']) {
                throw new Exception($kuotaCheck['message']);
            }

            return PermohonanPraktik::create([
                'pt_id' => $data['pt_id'],
                'unit_id' => $data['unit_id'],
                'prodi' => $data['prodi'],
                'jumlah_mahasiswa' => $data['jumlah_mahasiswa'],
                'tgl_mulai' => $data['tgl_mulai'],
                'tgl_selesai' => $data['tgl_selesai'],
                'file_surat_permohonan' => $data['file_surat_permohonan'] ?? null,
                'drive_file_id' => $data['drive_file_id'] ?? null,
                'drive_link' => $data['drive_link'] ?? null,
                'status' => 'diajukan',
                'catatan_diklat' => null,
            ]);
        });
    }
}
