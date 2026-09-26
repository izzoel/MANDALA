# Catatan Pembelajaran & Status Pengembangan Sistem MANDALA
**Tanggal:** 26 September 2026  
**Sistem:** MANDALA (Manajemen Diklat Akademik & Layanan Administrasi RS)  
**Framework:** Laravel 13, Livewire 4, Flux UI Pro, Tailwind CSS v4, MySQL  

---

## 1. Rangkuman Pencapaian Pengembangan

### A. Modul 1: Pelatihan & Sertifikat Pegawai Non-ASN (Diklat)
- **Target Pelatihan Pegawai (`/diklat/target`)**: Penetapan target wajib & fungsional tahunan oleh Admin Diklat, pemantauan progress pemenuhan melalui sertifikat.
- **Upload Sertifikat Pegawai (`/diklat/sertifikat`)**: Antarmuka unggah berkas PDF sertifikat oleh pegawai Non-ASN lengkap dengan riwayat status verifikasi.
- **Verifikasi Sertifikat (`/diklat/verifikasi`)**: Panel audit bagi Admin Diklat untuk menyetujui/menolak sertifikat dan menautkannya ke target pelatihan.

### B. Modul 2: Booking Praktik & Kolaborasi Institusi Pendidikan (Diklit)
- **Mitra Perguruan Tinggi & MoU (`/diklit/perguruan-tinggi`)**: Pengelolaan PT mitra, status keaktifan MoU, masa berlaku, dan validasi otomatis.
- **Unit Rumah Sakit & Kuota (`/diklit/unit`)**: Pengaturan unit RS dengan dukungan 2 mode kuota:
  1. `per_prodi`: Kuota dialokasikan spesifik untuk masing-masing program studi (misal: S1 Ners, D4 Anestesi, Dokter).
  2. `gabungan`: Kuota unit dibagi bersama untuk seluruh prodi yang masuk.
- **Booking & Permohonan Praktik (`/diklit/booking`)**:
  - Pengecekan kuota real-time via `BookingKuotaService` dengan logika overlap tanggal ($StartA \le EndB \land StartB \le EndA$).
  - Dual view mode: **Daftar Permohonan** dan **Matriks Keterisian Unit Bulanan** (indikator kapasitas & visual status).
  - Pilihan prodi dinamis otomatis menyesuaikan mode kuota unit yang dipilih.
  - Detail modal komprehensif (status MoU, nomor surat persetujuan RS, dokumen PDF, daftar mahasiswa & tim pembimbing).
  - Pembatalan pengajuan mandiri untuk permohonan berstatus `diajukan`.
- **Persetujuan & Penerbitan Surat RS (`/diklit/persetujuan`)**: Penerbitan nomor surat persetujuan dan catatan resmi oleh Admin Diklat.
- **Penunjukan Pembimbing (`/diklit/pembimbing`)**: Penugasan Clinical Instructor (CI) RS dan Dosen Pembimbing PT per mahasiswa.
- **Penilaian & Kriteria Kompetensi (`/diklit/penilaian` & `/diklit/kriteria`)**: Form evaluasi klinis interaktif berbobot persentase dengan kalkulasi nilai akhir otomatis.

### C. Modul 3: Manajemen Akun & Hak Akses (RBAC)
- **Integrasi Spatie Laravel Permission**:
  - Konfigurasi role & permission granular dengan 5 kluster kategori izin:
    1. *Manajemen Sistem & Pengguna* (`kelola-role`, `kelola-user`, `kelola-pengaturan-sistem`)
    2. *Modul Diklat Pegawai Non-ASN* (`kelola-target-pelatihan`, `upload-sertifikat`, `verifikasi-sertifikat`, `lihat-rekap-pelatihan`)
    3. *Modul Praktik Mahasiswa RS (Diklit)* (`kelola-pt-mou`, `kelola-unit-rs`, `ajukan-booking-praktik`, `persetujuan-booking`, `terbitkan-surat-rs`, `penunjukan-pembimbing`)
    4. *Modul Penilaian & Evaluasi Klinis* (`kelola-kriteria-nilai`, `input-penilaian-praktik`, `lihat-rekap-nilai`)
    5. *Laporan & Ekspor Dokumen* (`ekspor-laporan-diklat`, `ekspor-laporan-diklit`)
  - **Fitur Preset Template Cepat**: Tombol otomatis untuk menerapkan izin standar bagi *Super Admin (100%)*, *Admin Diklat RS*, *Admin PT*, *CI Lapangan RS*, *Dosen PT*, dan *Pegawai Non-ASN*.
  - Bypass `Gate::before` untuk role `super_admin`.
  - Halaman **Manajemen Pengguna (`/pengguna/user`)**: Khusus untuk `admin_diklat` dan `super_admin`.
  - Halaman **Manajemen Role & Permissions (`/pengguna/role`)**: Terproteksi ketat **HANYA untuk `super_admin`**.

---

## 2. Pembelajaran Teknis & Catatan Penting (Lessons Learned)

1. **Livewire Single-File Anonymous Components & Global Namespace (`use Exception`)**:
   - Di file anonymous Livewire (`.php` tanpa deklarasi `namespace`), hindari menulis `use Exception;` atau non-compound statements karena PHP akan mengeluarkan notice *"The use statement with non-compound name has no effect"* yang memicu `ErrorException` pada compiler Livewire.
   - **Solusi**: Tangkap error dengan `catch (\Throwable $e)` langsung tanpa perlu import non-compound statement.

2. **Type Binding pada Dropdown `<flux:select>` / `<select>`**:
   - Nilai default kosong pada dropdown mengirimkan empty string `""` atau string numeric `"1"`.
   - Menetapkan tipe strict `?int` pada properti Livewire yang terhubung ke `<flux:select>` akan menimbulkan `TypeError: Cannot assign string to property of type ?int`.
   - **Solusi**: Gunakan union type `public string|int|null $filterUnitId = null;` dan lakukan casting `(int)` saat diproses di kueri Eloquent.

3. **Konsistensi Penamaan Relasi Eloquent**:
   - Pada model `SuratPersetujuan`, disiapkan method relasi ganda `diterbitkanOleh(): BelongsTo` dan `penerbit(): BelongsTo` menuju model `User` untuk menjaga interoperabilitas kode lama dan query eager loading baru.

4. **Logika Pessimistic Locking & Transaksi Booking Kuota**:
   - Seluruh validasi MoU dan ketersediaan kuota dieksekusi di dalam `DB::transaction()` pada `BookingKuotaService` guna mencegah *race condition* atau *overbooking* kuota saat terjadi pengajuan simultan.

5. **Standar Ikon Antarmuka**:
   - Proyek menggunakan komponen ikon resmi **Flux UI Icon** (`<flux:icon name="..." />`). Dilarang menggunakan emoji karakter unicode dalam tampilan antarmuka maupun label metadata.

---

## 3. Kredensial Akun Pengujian (Seeder Database)

Semua akun menggunakan kata sandi default: `password`

| Role | Email Login | Hak Akses Utama |
| :--- | :--- | :--- |
| **Super Administrator** | `superadmin@mandala.test` | Akses penuh & Kelola Role Spatie (`/pengguna/role`) |
| **Admin Diklat RS** | `diklat@mandala.test` | Kelola Diklat, Verifikasi, Persetujuan Booking, Kelola Akun |
| **Admin Perguruan Tinggi** | `adminpt@mandala.test` | Cek Kuota & Ajukan Permohonan Booking Praktik |
| **Pegawai Non-ASN 1** | `andika@mandala.test` | Target Pelatihan & Upload Sertifikat ACLS |
| **Pegawai Non-ASN 2** | `siti@mandala.test` | Target Pelatihan & Upload Sertifikat Farmasi |
| **Pembimbing Lapangan (CI RS)** | `ci.lapangan@mandala.test` | Input Penilaian Praktik Lapangan |
| **Pembimbing Dosen (PT)** | `dosen@mandala.test` | Input Penilaian Akademik Mahasiswa |
| **Mahasiswa Praktikan** | `mahasiswa@mandala.test` | Pantau Jadwal Stase, Pembimbing & Hasil Penilaian |

---

## 4. Rekomendasi Rencana Lanjutan

1. **Export Laporan & Rekapitulasi (PDF / Excel)**:
   - Cetak Surat Persetujuan Praktik RS dalam format PDF resmi dengan kop surat rumah sakit.
   - Ekspor rekapitulasi penilaian kompetensi mahasiswa dan transkrip nilai stase klinik.
   - Ekspor logbook pelatihan pegawai non-ASN untuk kebutuhan akreditasi RS.
2. **Notifikasi & Real-time Alerts**:
   - Notifikasi email / in-app saat ada pengajuan booking baru bagi Admin Diklat.
   - Notifikasi penerbitan persetujuan ke Admin PT dan mahasiswa.
3. **Log Audit & Log Aktivitas**:
   - Pencatatan riwayat perubahan status booking dan penilaian.
4. **Pengujian Tambahan (Feature Test / Pest)**:
   - Automated tests untuk `BookingKuotaService` skenario overlap tanggal dan kapasitas kuota habis.
