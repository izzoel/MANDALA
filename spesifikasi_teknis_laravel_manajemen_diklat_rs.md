# Spesifikasi Teknis Pengembangan Sistem (Tech Spec)
**Aplikasi Manajemen Diklat & Diklit Rumah Sakit**
**Versi Dokumen:** 1.0 (Berdasarkan PRD 3 September 2026)
**Domain:** www.teknologi.administrasirs.com
**Tech Stack:** Laravel, Livewire, Tailwind CSS, Flux UI, MySQL

---

## 1. Arsitektur & Teknologi (Tech Stack)

*   **Backend:** Laravel (PHP) - Menangani routing, middleware (RBAC), ORM (Eloquent), dan logika bisnis (terutama pengecekan kuota).
*   **Frontend Reaktif:** Livewire - Digunakan untuk membuat komponen dinamis (tabel data, form persetujuan, kalender booking) tanpa perlu menulis banyak JavaScript.
*   **Styling:** Tailwind CSS - Utility-first CSS framework untuk memastikan tampilan responsif sesuai kebutuhan (terutama untuk akses mobile Admin PT & Mahasiswa).
*   **UI Components:** Flux UI - Mempercepat pengembangan antarmuka dengan komponen siap pakai (Modals, Dropdowns, Cards, Tables, Forms) yang terintegrasi sempurna dengan Livewire dan Tailwind.
*   **Database:** MySQL - Relational database manager.
*   **Storage:** Local Storage (sementara) diarahkan ke arsitektur S3 (AWS/MinIO) untuk file PDF/JPG sertifikat dan surat persetujuan.

---

## 2. Struktur Database (Laravel Migrations & MySQL)

Sistem menggunakan satu tabel `users` utama untuk autentikasi, dengan ekstensi profil menggunakan relasi `One-to-One` atau `One-to-Many`. Gunakan tipe data `uuid` atau `ulid` untuk keamanan ID (opsional) atau tetap gunakan `bigIncrements` sesuai standar Laravel.

### A. Tabel Autentikasi & Profil (Role-Based)
*   **`users`**: `id`, `email`, `password` (Bcrypt), `role` (enum: pegawai_non_asn, admin_diklat, admin_pt, mahasiswa, pembimbing_lapangan, pembimbing_dosen, super_admin), `status`, `remember_token`, `timestamps`.
*   **`pegawai_non_asn`**: `id`, `user_id` (FK), `nama`, `no_pegawai`, `unit_kerja`, `jabatan`, `tgl_mulai_kerja`, `timestamps`.
*   **`perguruan_tinggi`**: `id`, `nama_pt`, `status_mou` (boolean), `tgl_mulai_mou`, `tgl_akhir_mou`, `timestamps`. (Catatan: Admin PT terhubung ke sini).
*   **`mahasiswa`**: `id`, `user_id` (FK), `pt_id` (FK), `nama`, `nim`, `prodi`, `id_pembimbing_lapangan` (FK, nullable), `id_pembimbing_dosen` (FK, nullable), `timestamps`.
*   **`pembimbing_lapangan`**: `id`, `user_id` (FK), `nama`, `unit_id` (FK), `kontak`, `timestamps`.
*   **`pembimbing_dosen`**: `id`, `user_id` (FK), `pt_id` (FK), `nama`, `kontak`, `timestamps`.

### B. Modul 1: Perencanaan Pelatihan (Non-ASN)
*   **`sertifikat`**: `id`, `pegawai_id` (FK), `nama_pelatihan`, `penyelenggara`, `tgl_pelaksanaan`, `no_sertifikat`, `file_path`, `status_verifikasi` (enum: pending, disetujui, ditolak), `catatan_verifikator`, `verified_by` (FK users), `verified_at`, `timestamps`.
*   **`target_pelatihan`**: `id`, `pegawai_id` (FK), `nama_target`, `kategori`, `periode`, `tenggat` (date), `status` (enum: belum, proses, selesai), `sertifikat_pemenuhan_id` (FK, nullable), `created_by` (FK users), `timestamps`.

### C. Modul 2: Booking Unit & Praktik
*   **`unit`**: `id`, `nama_unit`, `mode_kuota` (enum: per_prodi, gabungan), `kuota_maks`, `timestamps`.
*   **`unit_prodi_kuota`**: `id`, `unit_id` (FK), `prodi`, `kuota_maks`, `timestamps`.
*   **`permohonan_praktik`**: `id`, `pt_id` (FK), `unit_id` (FK), `prodi`, `jumlah_mahasiswa`, `tgl_mulai`, `tgl_selesai`, `file_surat_permohonan`, `status` (enum: diajukan, disetujui, ditolak), `catatan_diklat`, `timestamps`.
*   **`surat_persetujuan`**: `id`, `permohonan_id` (FK), `file_pdf`, `diterbitkan_oleh` (FK users), `tgl_terbit`, `timestamps`.
*   **`penunjukan_pembimbing`**: `id`, `permohonan_id` (FK), `mahasiswa_id` (FK), `pembimbing_lapangan_id` (FK), `timestamps`.
*   **`kriteria_penilaian`**: `id`, `nama_kriteria`, `bobot` (integer/decimal), `urutan`, `aktif` (boolean), `timestamps`.
*   **`penilaian_praktik`**: `id`, `mahasiswa_id` (FK), `penilai_id` (FK users), `peran_penilai` (enum: lapangan, dosen), `tgl_isi`, `timestamps`.
*   **`detail_penilaian`**: `id`, `penilaian_id` (FK), `kriteria_id` (FK), `nilai` (integer/decimal), `catatan` (text), `timestamps`.

---

## 3. Strategi Komponen (Livewire & Flux UI)

Flux UI akan sangat mempercepat pembuatan dashboard. Berikut adalah pemetaan komponen utama:

### Modul 1 (Pelatihan)
*   `UploadSertifikat` (Livewire): Form pengunggahan file menggunakan `Livewire\WithFileUploads`. Gunakan Flux UI `File Input` dan integrasikan batas ukuran (max 5MB, mimes:pdf,jpg,png).
*   `VerifikasiSertifikat` (Livewire): Tabel (Flux UI Table) berisi antrean verifikasi untuk Admin Diklat. Gunakan Flux UI `Modal` untuk menampilkan preview PDF/Gambar dan form Approve/Reject beserta catatan.
*   `ManajemenTargetPelatihan` (Livewire): Form penugasan manual oleh Admin Diklat. Gunakan komponen Flux UI `Select` atau `Combobox` untuk memilih pegawai.

### Modul 2 (Booking Praktik)
*   `KalenderBooking` (Livewire): Komponen utama untuk Admin PT. Menampilkan grid ketersediaan unit. 
*   `FormPermohonan` (Livewire): Memuat validasi tanggal MoU. 
    *   *Validasi Gatekeeper:* Di dalam method `mount()` atau saat `submit()`, pastikan: `if (now() > $pt->tgl_akhir_mou) { throw new Exception('MoU Kedaluwarsa'); }`
*   `PenilaianPraktik` (Livewire): Form dinamis yang di-render berdasarkan data tabel `kriteria_penilaian`. Karena format belum fix, komponen ini melooping kriteria dari database.

---

## 4. Logika Bisnis Kritis: Pengecekan Kuota (Laravel Eloquent)

Ini adalah bagian paling berisiko tinggi sesuai PRD. Pengecekan harus dilakukan menggunakan **Database Transaction** dan **Pessimistic Locking** (atau setidaknya validasi ketat di Controller/Livewire method) untuk menghindari *race condition*.

**Contoh Logic di Livewire Component / Service Class:**

```php
public function checkKuotaAvailability($unit_id, $prodi, $tgl_mulai, $tgl_selesai, $jumlah_diminta) {
    $unit = Unit::findOrFail($unit_id);
    
    // 1. Cari permohonan yang OVERLAP (Disetujui)
    // Logika Overlap: StartA <= EndB AND StartB <= EndA
    $queryOverlap = PermohonanPraktik::where('unit_id', $unit_id)
        ->where('status', 'disetujui')
        ->where(function($q) use ($tgl_mulai, $tgl_selesai) {
            $q->where('tgl_mulai', '<=', $tgl_selesai)
              ->where('tgl_selesai', '>=', $tgl_mulai);
        });

    if ($unit->mode_kuota === 'gabungan') {
        $kuotaTerpakai = $queryOverlap->sum('jumlah_mahasiswa');
        $sisaKuota = $unit->kuota_maks - $kuotaTerpakai;
        
        return $sisaKuota >= $jumlah_diminta;
    } 
    
    if ($unit->mode_kuota === 'per_prodi') {
        $kuotaProdi = UnitProdiKuota::where('unit_id', $unit_id)
                                    ->where('prodi', $prodi)
                                    ->first()->kuota_maks ?? 0;
                                    
        $kuotaTerpakai = $queryOverlap->where('prodi', $prodi)->sum('jumlah_mahasiswa');
        $sisaKuota = $kuotaProdi - $kuotaTerpakai;
        
        return $sisaKuota >= $jumlah_diminta;
    }
    
    return false;
}
```

---

## 5. Middleware & Keamanan (Laravel)

*   **Role-Based Access Control (RBAC):** Buat custom middleware `CheckRole` atau gunakan Spatie Permission. Jangan sekadar menyembunyikan menu di UI (Blade `@if`), tapi lindungi route-nya.
    ```php
    Route::middleware(['auth', 'role:admin_diklat'])->group(function () {
        Route::get('/diklat/dashboard', AdminDiklatDashboard::class);
    });
    ```
*   **File Security:** Simpan file sertifikat dan surat persetujuan di `storage/app/private` (bukan public). Buat route khusus untuk memunculkan file (Stream Download/Response) yang sudah dilindungi middleware auth & kepemilikan.
*   **Rate Limiting:** Terapkan di route `/login` menggunakan `Route::middleware('throttle:5,1')` bawaan Laravel.
*   **Hash Nama File:** Saat upload, gunakan metode bawaan `store()` Laravel yang otomatis meng-generate nama file unik untuk mencegah overwriting dan eksekusi skrip.

---

## 6. Rencana Implementasi Bertahap (Sesuai PRD)

**Fase 1 (MVP)**
1.  Setup Laravel, Livewire, Tailwind, Flux UI.
2.  Desain dan eksekusi Migration database.
3.  Sistem Autentikasi dasar dan Middleware Role.
4.  **Modul 1:** CRUD Target, Upload Sertifikat (Livewire component), Verifikasi oleh Admin.
5.  **Modul 2:** Setup Data PT & MoU, Booking sistem dengan validasi overlap tanggal & logika kuota.
6.  Penyelesaian UI dengan komponen responsif (diutamakan mobile view untuk Mahasiswa & Admin PT).

**Catatan Integrasi Lanjutan:** Pastikan menggunakan service provider/cloud (seperti Railway, Forge, atau VPS + Nginx) dengan HTTPS (Let's Encrypt) sejak awal untuk menghindari isu cookie/CORS saat deploy ke domain `www.teknologi.administrasirs.com`.