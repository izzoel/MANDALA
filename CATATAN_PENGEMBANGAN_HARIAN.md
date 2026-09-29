# Catatan Pembelajaran & Status Pengembangan Sistem MANDALA

**Tanggal:** 29 September 2026  
**Sistem:** MANDALA (Manajemen Diklat Akademik & Layanan Administrasi RS)  
**Framework:** Laravel 13, Livewire 4, Flux UI Pro, Tailwind CSS v4, MySQL  
**Status Pengembangan:** **100% SELESAI (SELURUH MODUL TUNTAS & TERINTEGRASI)**

---

## 1. Rangkuman Pencapaian & Penyelesaian Seluruh Modul

### A. Modul 1: Pelatihan & Sertifikat Pegawai / Peserta Diklat (Diklat)

- **Target Pelatihan Pegawai (`/diklat/target`)**: Penetapan target wajib & fungsional tahunan oleh Admin Diklat, pemantauan progress pemenuhan melalui sertifikat.
- **Upload Sertifikat Pegawai (`/diklat/sertifikat`)**: Antarmuka unggah berkas PDF sertifikat oleh peserta diklat lengkap dengan riwayat status verifikasi.
- **Verifikasi Sertifikat (`/diklat/verifikasi`)**: Panel audit bagi Admin Diklat untuk menyetujui/menolak sertifikat dan menautkannya ke target pelatihan.

### B. Modul 2: Booking Praktik & Kolaborasi Institusi Pendidikan (Diklit)

- **Mitra Perguruan Tinggi & MoU (`/diklit/perguruan-tinggi`)**:
  - Pengelolaan PT mitra, status keaktifan MoU, masa berlaku, dan validasi otomatis kedaluwarsa (`is_mou_expired` & auto-update `status_mou = false`).
  - **Otomatisasi Akun Admin PT**: Pembuatan akun instan otomatis (`[nama PT]@mandala.test` / password `password`) saat Admin Diklat menambahkan MoU baru, terhubung langsung via foreign key `perguruan_tinggis.user_id`.
  - Integrasi komponen pemilihan tanggal modern `<flux:date-picker />`.
- **Unit Rumah Sakit & Kuota (`/diklit/unit`)**: Pengaturan unit RS dengan dukungan 2 mode kuota:
  1. `per_prodi`: Kuota dialokasikan spesifik untuk masing-masing program studi (misal: S1 Ners, D4 Anestesi, Dokter).
  2. `gabungan`: Kuota unit dibagi bersama untuk seluruh prodi yang masuk.
- **Booking & Permohonan Praktik (`/diklit/booking`)**:
  - Pengecekan kuota real-time via `BookingKuotaService` dengan logika overlap tanggal ($StartA \le EndB \land StartB \le EndA$).
  - Tab Switcher menggunakan `<flux:tabs wire:model.live="viewMode">`: **Daftar Permohonan** dan **Matriks Keterisian Unit Bulanan**.
  - **Konsolidasi Modal Persetujuan & Penerbitan Surat RS**: Modal review permohonan, penolakan, serta penerbitan nomor surat persetujuan resmi dipusatkan langsung ke dalam modal detail booking di `/diklit/booking`.
  - Rute `/diklit/persetujuan` otomatis dialihkan (301 redirect) dan menu "Persetujuan & Surat" dihilangkan dari sidebar navigasi.
- **Penunjukan Pembimbing (`/diklit/pembimbing`)**: Penugasan Clinical Instructor (CI) RS dan Dosen Pembimbing PT per mahasiswa.
- **Penilaian & Kriteria Kompetensi (`/diklit/penilaian` & `/diklit/kriteria`)**: Form evaluasi klinis interaktif berbobot persentase dengan kalkulasi nilai akhir otomatis.

### C. Modul 3: Manajemen Akun & Hak Akses (RBAC)

- **Integrasi Spatie Laravel Permission**:
  - Konfigurasi role & permission granular dengan 5 kluster kategori izin:
    1. _Manajemen Sistem & Pengguna_ (`kelola-role`, `kelola-user`, `kelola-akun-mahasiswa`, `kelola-pengaturan-sistem`)
    2. _Modul Diklat Pegawai_ (`kelola-target-pelatihan`, `upload-sertifikat`, `verifikasi-sertifikat`, `lihat-rekap-pelatihan`)
    3. _Modul Praktik Mahasiswa RS (Diklit)_ (`kelola-pt-mou`, `kelola-unit-rs`, `ajukan-booking-praktik`, `persetujuan-booking`, `terbitkan-surat-rs`, `penunjukan-pembimbing`)
    4. _Modul Penilaian & Evaluasi Klinis_ (`kelola-kriteria-nilai`, `input-penilaian-praktik`, `lihat-rekap-nilai`)
    5. _Laporan & Ekspor Dokumen_ (`ekspor-laporan-diklat`, `ekspor-laporan-diklit`)
  - **Fitur Preset Template Cepat**: Tombol otomatis untuk menerapkan izin standar bagi _Super Admin (100%)_, _Admin Diklat RS_, _Admin PT_, _CI Lapangan RS_, _Dosen PT_, dan _peserta diklat_.
  - **Pendelegasian Akun Mahasiswa untuk Admin PT**: Melalui permission `kelola-akun-mahasiswa`, Admin PT dapat mengakses `/pengguna/user` untuk mengelola akun mahasiswa dari perguruan tingginya secara aman dan terisolasi.
  - Halaman **Manajemen Pengguna (`/pengguna/user`)**: Akses penuh untuk `super_admin` & `admin_diklat`, akses terisolasi untuk `admin_pt`.
  - Halaman **Manajemen Role & Permissions (`/pengguna/role`)**: Terproteksi ketat **HANYA untuk `super_admin`**.

### D. Identitas Visual & Branding Sistem

- Penggantian logo placeholder dengan aset vektor resmi [`mandala.svg`](file:///Applications/MAMP/htdocs/mandala/public/mandala.svg) pada seluruh komponen:
  - Komponen Blade `<x-app-logo-icon>` dan `<x-app-logo>`.
  - Header navigasi & footer pada [Landing Page](file:///Applications/MAMP/htdocs/mandala/resources/views/landing.blade.php).
  - Favicon resmi aplikasi pada `public/favicon.svg` dan `partials/head.blade.php`.

---

## 2. Pembelajaran Teknis & Catatan Penting (Lessons Learned)

1. **Livewire Single-File Anonymous Components & Global Namespace (`use Exception`)**:
   - Di file anonymous Livewire (`.php` tanpa deklarasi `namespace`), hindari menulis `use Exception;` atau non-compound statements karena PHP akan mengeluarkan notice _"The use statement with non-compound name has no effect"_ yang memicu `ErrorException` pada compiler Livewire.
   - **Solusi**: Tangkap error dengan `catch (\Throwable $e)` langsung tanpa perlu import non-compound statement.

2. **Type Binding pada Dropdown `<flux:select>` / `<select>`**:
   - Nilai default kosong pada dropdown mengirimkan empty string `""` atau string numeric `"1"`.
   - Menetapkan tipe strict `?int` pada properti Livewire yang terhubung ke `<flux:select>` akan menimbulkan `TypeError: Cannot assign string to property of type ?int`.
   - **Solusi**: Gunakan union type `public string|int|null $filterUnitId = null;` dan lakukan casting `(int)` saat diproses di kueri Eloquent.

3. **Konsistensi Penamaan Relasi Eloquent**:
   - Pada model `SuratPersetujuan`, disiapkan method relasi ganda `diterbitkanOleh(): BelongsTo` dan `penerbit(): BelongsTo` menuju model `User` untuk menjaga interoperabilitas kode lama dan query eager loading baru.

4. **Logika Pessimistic Locking & Transaksi Booking Kuota**:
   - Seluruh validasi MoU dan ketersediaan kuota dieksekusi di dalam `DB::transaction()` pada `BookingKuotaService` guna mencegah _race condition_ atau _overbooking_ kuota saat terjadi pengajuan simultan.

5. **Integrasi `<flux:date-picker>`**:
   - Format standar yang digunakan adalah `Y-m-d`. Komponen Flux Date Picker mendukung binding langsung ke properti Livewire `string` atau Carbon `Carbon\Carbon`.

---

## 3. Kredensial Akun Pengujian (Seeder Database)

Semua akun menggunakan kata sandi default: `password`

| Role                            | Email Login                | Hak Akses Utama                                                    |
| :------------------------------ | :------------------------- | :----------------------------------------------------------------- |
| **Super Administrator**         | `superadmin@mandala.test`  | Akses penuh & Kelola Role Spatie (`/pengguna/role`)                |
| **Admin Diklat RS**             | `admin@mandala.test`       | Kelola Diklat, Verifikasi, Persetujuan Booking, Kelola Akun Global |
| **Admin Perguruan Tinggi**      | `adminpt@mandala.test`     | Cek Kuota, Ajukan Booking Praktik, Kelola Akun Mahasiswa PT        |
| **Peserta Diklat 1 (Dokter)**   | `andika@mandala.test`      | Target Pelatihan & Upload Sertifikat ACLS                          |
| **Peserta Diklat 2 (Farmasi)**  | `siti@mandala.test`        | Target Pelatihan & Upload Sertifikat Farmasi                       |
| **Pembimbing Lapangan (CI RS)** | `ci.lapangan@mandala.test` | Input Penilaian Praktik Lapangan                                   |
| **Pembimbing Dosen (PT)**       | `dosen@mandala.test`       | Input Penilaian Akademik Mahasiswa                                 |
| **Mahasiswa Praktikan**         | `mahasiswa@mandala.test`   | Pantau Jadwal Stase, Pembimbing & Hasil Penilaian                  |

---

## 4. Kesimpulan & Penyerahan

Seluruh modul pada sistem **MANDALA** telah berhasil dibangun, diuji, dan disempurnakan sesuai spesifikasi teknis dan kebutuhan operasional tata kelola Diklat & Diklit Rumah Sakit.
