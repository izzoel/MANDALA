# MANDALA (Manajemen Diklat Akademik & Layanan Administrasi)

**MANDALA** adalah sistem informasi manajemen terintegrasi untuk tata kelola pendidikan dan pelatihan (**Diklat**), kegiatan akademik & penelitian (**Diklit**), pengelolaan kuota unit/instalasi rumah sakit, nota kesepahaman (MoU) perguruan tinggi mitra, perizinan praktik klinik mahasiswa, serta evaluasi kompetensi klinis di lingkungan rumah sakit.

---

## 🚀 Fitur & Modul Sistem

### 1. Modul Diklat (Pelatihan Pegawai Non-ASN)
- **Target Pelatihan:** Penugasan dan pemantauan target pelatihan wajib / fungsional tenaga kesehatan dan staf non-ASN.
- **Upload & Berkas Sertifikat:** Pengunggahan mandiri sertifikat pelatihan (PDF/JPG/PNG max 5MB) dengan penautan otomatis ke target pelatihan.
- **Verifikasi Sertifikat:** Antrean verifikasi keabsahan dokumen oleh Admin Diklat RS dengan review berkas & form keputusan (*Approve/Reject*).

### 2. Modul Diklit (Booking Unit RS & Praktik Mahasiswa)
- **Manajemen Perguruan Tinggi & MoU:** Pencatatan data institusi mitra dan gatekeeper masa berlaku MoU.
- **Unit RS & Alokasi Kuota:** Konfigurasi unit ruangan (IGD, ICU, Bedah, Farmasi, Lab, dsb.) dengan mode kuota **Gabungan** atau **Per-Program Studi**.
- **Kalender Booking & Permohonan Praktik:** Pengajuan izin praktik oleh Admin PT dengan *real-time quota check* dan pencegahan *overlap collision*.
- **Persetujuan & Surat Izin RS:** Review permohonan masuk dan penerbitan nomor surat persetujuan resmi oleh Tim Diklat RS.
- **Penunjukan Pembimbing:** Penetapan *Clinical Instructor* (CI) Lapangan RS dan Dosen Pembimbing Institusi.
- **Penilaian Praktik Mahasiswa:** Evaluasi kompetensi klinis dinamis berbasis kriteria penilaian terbobot (skor otomatis 0–100).
- **Master Kriteria Evaluasi:** Pengaturan aspek kompetensi dan persentase pembobotan penilaian (total 100%).

### 3. Modul Manajemen Pengguna & Hak Akses (RBAC)
- **Kelola Akun Multi-Peran:** Penambahan dan pembaruan akun untuk 7 peran pengguna dengan form ekstensi profil dinamis (Pegawai Non-ASN, Mahasiswa, CI Lapangan, Dosen Pembimbing, Admin PT, Admin Diklat, Super Admin).
- **Kontrol Status & Keamanan:** Pengaktifan/penonaktifan akun, reset kata sandi, dan proteksi hak akses berbasis peran (*Role-Based Access Control*). Khusus diakses oleh **Super Administrator** dan **Admin Diklat RS**.

---

## 👥 Hak Akses & Peran Pengguna (RBAC)

| Peran (Role) | Kode Role | Hak Akses Utama |
| :--- | :--- | :--- |
| **Super Administrator** | `super_admin` | Akses penuh seluruh modul dan konfigurasi sistem |
| **Admin Diklat RS** | `admin_diklat` | Kelola target, verifikasi sertifikat, review booking, terbitkan surat, atur kuota unit & kriteria nilai |
| **Admin Perguruan Tinggi** | `admin_pt` | Cek ketersediaan kuota unit & ajukan permohonan praktik mahasiswa |
| **Pegawai Non-ASN** | `pegawai_non_asn` | Pantau target pelatihan & unggah berkas sertifikat |
| **Pembimbing Lapangan (CI RS)** | `pembimbing_lapangan` | Bimbing mahasiswa di unit & input evaluasi/nilai kompetensi |
| **Pembimbing Dosen (PT)** | `pembimbing_dosen` | Pantau mahasiswa bimbingan & input penilaian akademik |
| **Mahasiswa Praktikan** | `mahasiswa` | Lihat jadwal stase praktik, pembimbing, dan status permohonan |

---

## 🛠️ Tech Stack

- **Backend Framework:** [Laravel 13](https://laravel.com) (PHP 8.3+)
- **Frontend Reaktif:** [Livewire 4](https://livewire.laravel.com) (Single-File Components `⚡`)
- **UI Toolkit:** [Flux UI Pro](https://fluxui.dev)
- **Styling:** [Tailwind CSS v4](https://tailwindcss.com) + `@tailwindcss/vite`
- **Database:** MySQL 8.x
- **Keamanan:** RBAC Middleware (`CheckRole`), Laravel Fortify, WebAuthn Passkeys, 2FA TOTP

---

## 📊 Skema Database & Relasi (ERD)

```mermaid
erDiagram
    users ||--o| pegawai_non_asns : "profil"
    users ||--o| mahasiswas : "profil"
    users ||--o| pembimbing_lapangans : "profil"
    users ||--o| pembimbing_dosens : "profil"

    perguruan_tinggis ||--o{ mahasiswas : "memiliki"
    perguruan_tinggis ||--o{ pembimbing_dosens : "memiliki"
    perguruan_tinggis ||--o{ permohonan_praktiks : "mengajukan"

    units ||--o{ unit_prodi_kuotas : "alokasi kuota"
    units ||--o{ permohonan_praktiks : "tujuan praktik"
    units ||--o{ pembimbing_lapangans : "penugasan"

    pegawai_non_asns ||--o{ sertifikats : "memiliki"
    pegawai_non_asns ||--o{ target_pelatihans : "ditugaskan"
    sertifikats ||--o| target_pelatihans : "pemenuhan"

    permohonan_praktiks ||--o| surat_persetujuans : "diterbitkan"
    permohonan_praktiks ||--o{ penunjukan_pembimbings : "memiliki"
    mahasiswas ||--o{ penunjukan_pembimbings : "ditunjuk"

    mahasiswas ||--o{ penilaian_praktiks : "dinilai"
    kriteria_penilaians ||--o{ detail_penilaians : "aspek"
    penilaian_praktiks ||--o{ detail_penilaians : "memuat skor"
```

---

## 🔐 Akun Uji Coba Default (Seeder)

Semua akun default menggunakan password: `password`

| Peran | Email Login |
| :--- | :--- |
| **Super Admin** | `superadmin@mandala.test` |
| **Admin Diklat RS** | `diklat@mandala.test` |
| **Admin Perguruan Tinggi** | `adminpt@mandala.test` |
| **Pegawai Non-ASN 1 (Dokter)** | `andika@mandala.test` |
| **Pegawai Non-ASN 2 (Farmasi)** | `siti@mandala.test` |
| **Pembimbing Lapangan (CI RS)** | `ci.lapangan@mandala.test` |
| **Pembimbing Dosen (PT)** | `dosen@mandala.test` |
| **Mahasiswa Praktikan** | `mahasiswa@mandala.test` |

---

## 💻 Panduan Instalasi & Menjalankan

```bash
# 1. Konfigurasi Environment & DB MySQL
cp .env.example .env
php artisan key:generate

# 2. Migrasi Database & Seeder Data Uji Coba
php artisan migrate:fresh --seed

# 3. Build Asset Frontend
npm run build

# 4. Jalankan Aplikasi
composer run dev
```

---

## 📄 Lisensi & Pengembang

Dikembangkan oleh **[Zetware](https://zetware.id)** © 2026. Hak cipta dilindungi undang-undang.