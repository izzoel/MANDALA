<?php

namespace Database\Seeders;

use App\Models\DetailPenilaian;
use App\Models\KriteriaPenilaian;
use App\Models\Mahasiswa;
use App\Models\PembimbingDosen;
use App\Models\PembimbingLapangan;
use App\Models\PenilaianPraktik;
use App\Models\PenunjukanPembimbing;
use App\Models\PerguruanTinggi;
use App\Models\PermohonanPraktik;
use App\Models\Peserta;
use App\Models\Sertifikat;
use App\Models\SuratPersetujuan;
use App\Models\TargetPelatihan;
use App\Models\Unit;
use App\Models\UnitProdiKuota;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DiklatRsSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Spatie Roles & Permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Manajemen Sistem & Pengguna
            'kelola-role',
            'kelola-user',
            'kelola-pengaturan-sistem',

            // Modul Diklat Pegawai
            'kelola-target-pelatihan',
            'upload-sertifikat',
            'arsip-sertifikat',
            'lihat-rekap-pelatihan',

            // Modul Praktik & Booking RS (Diklit)
            'kelola-pt-mou',
            'kelola-unit-rs',
            'ajukan-booking-praktik',
            'persetujuan-booking',
            'terbitkan-surat-rs',
            'penunjukan-pembimbing',

            // Modul Penilaian Klinis
            'kelola-kriteria-nilai',
            'input-penilaian-praktik',
            'lihat-rekap-nilai',

            // Laporan & Dokumen
            'ekspor-laporan-diklat',
            'ekspor-laporan-diklit',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $roleAdminDiklat = Role::firstOrCreate(['name' => 'admin_diklat', 'guard_name' => 'web']);
        $roleAdminPt = Role::firstOrCreate(['name' => 'admin_pt', 'guard_name' => 'web']);
        $rolePesertaDiklat = Role::firstOrCreate(['name' => 'peserta_diklat', 'guard_name' => 'web']);
        $rolePembimbingLapangan = Role::firstOrCreate(['name' => 'pembimbing_lapangan', 'guard_name' => 'web']);
        $rolePembimbingDosen = Role::firstOrCreate(['name' => 'pembimbing_dosen', 'guard_name' => 'web']);
        $roleMahasiswa = Role::firstOrCreate(['name' => 'mahasiswa', 'guard_name' => 'web']);

        // Assign Permissions to Roles
        $roleSuperAdmin->syncPermissions($permissions);

        $roleAdminDiklat->syncPermissions([
            'kelola-user',
            'kelola-target-pelatihan',
            'upload-sertifikat',
            'arsip-sertifikat',
            'lihat-rekap-pelatihan',
            'kelola-pt-mou',
            'kelola-unit-rs',
            'persetujuan-booking',
            'terbitkan-surat-rs',
            'penunjukan-pembimbing',
            'kelola-kriteria-nilai',
            'lihat-rekap-nilai',
            'ekspor-laporan-diklat',
            'ekspor-laporan-diklit',
        ]);

        $roleAdminPt->syncPermissions([
            'ajukan-booking-praktik',
            'lihat-rekap-nilai',
            'ekspor-laporan-diklit',
        ]);

        $rolePesertaDiklat->syncPermissions([
            'upload-sertifikat',
            'lihat-rekap-pelatihan',
        ]);

        $rolePembimbingLapangan->syncPermissions([
            'input-penilaian-praktik',
            'lihat-rekap-nilai',
            'penunjukan-pembimbing',
        ]);

        $rolePembimbingDosen->syncPermissions([
            'input-penilaian-praktik',
            'lihat-rekap-nilai',
        ]);

        $roleMahasiswa->syncPermissions([
            'lihat-rekap-nilai',
        ]);

        // 1. Users Akun Utama
        $superAdmin = User::create([
            'name' => 'Super Administrator RS',
            'email' => 'superadmin@mandala.test',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'status' => 'aktif',
        ]);
        $superAdmin->assignRole($roleSuperAdmin);

        $adminDiklat = User::create([
            'name' => 'Ns. Ratna Dewi, S.Kep (Admin Diklat)',
            'email' => 'diklat@mandala.test',
            'password' => Hash::make('password'),
            'role' => 'admin_diklat',
            'status' => 'aktif',
        ]);
        $adminDiklat->assignRole($roleAdminDiklat);

        $adminPt = User::create([
            'name' => 'Budi Santoso, M.Kom (Admin PT)',
            'email' => 'adminpt@mandala.test',
            'password' => Hash::make('password'),
            'role' => 'admin_pt',
            'status' => 'aktif',
        ]);
        $adminPt->assignRole($roleAdminPt);

        $userPegawai1 = User::create([
            // 'uuid' => Str::uuid(),
            'name' => 'dr. Andika Pratama',
            'email' => 'andika@mandala.test',
            'password' => Hash::make('password'),
            'role' => 'peserta_diklat',
            'status' => 'aktif',
        ]);
        $userPegawai1->assignRole($rolePesertaDiklat);

        $userPegawai2 = User::create([
            // 'uuid' => Str::uuid(),
            'name' => 'Siti Nurhaliza, A.Md.Farm',
            'email' => 'siti@mandala.test',
            'password' => Hash::make('password'),
            'role' => 'peserta_diklat',
            'status' => 'aktif',
        ]);
        $userPegawai2->assignRole($rolePesertaDiklat);

        $userPembimbingLapangan = User::create([
            'name' => 'Ns. Bambang Hidayat, S.Tr.Kep (CI Lapangan)',
            'email' => 'ci.lapangan@mandala.test',
            'password' => Hash::make('password'),
            'role' => 'pembimbing_lapangan',
            'status' => 'aktif',
        ]);
        $userPembimbingLapangan->assignRole($rolePembimbingLapangan);

        $userPembimbingDosen = User::create([
            'name' => 'Dr. drg. Maya Anggraini, M.Kes (Dosen Pembimbing)',
            'email' => 'dosen@mandala.test',
            'password' => Hash::make('password'),
            'role' => 'pembimbing_dosen',
            'status' => 'aktif',
        ]);
        $userPembimbingDosen->assignRole($rolePembimbingDosen);

        $userMahasiswa1 = User::create([
            'name' => 'Ahmad Fauzi (Mahasiswa)',
            'email' => 'mahasiswa@mandala.test',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ]);
        $userMahasiswa1->assignRole($roleMahasiswa);

        $userMahasiswa2 = User::create([
            'name' => 'Dinda Putri Rahmawati',
            'email' => 'dinda@mandala.test',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ]);
        $userMahasiswa2->assignRole($roleMahasiswa);

        // 2. Perguruan Tinggi Mitra (MoU)
        $pt1 = PerguruanTinggi::create([
            'nama_pt' => 'Universitas Indonesia - Fakultas Ilmu Keperawatan',
            'status_mou' => true,
            'tgl_mulai_mou' => '2025-01-01',
            'tgl_akhir_mou' => '2027-12-31',
            'kontak' => '021-7864125',
            'email_pt' => 'diklit@ui.ac.id',
        ]);

        $pt2 = PerguruanTinggi::create([
            'nama_pt' => 'Poltekkes Kemenkes Jakarta III - Jurusan Kebidanan',
            'status_mou' => true,
            'tgl_mulai_mou' => '2024-06-01',
            'tgl_akhir_mou' => '2026-12-31',
            'kontak' => '021-8497869',
            'email_pt' => 'akademik@poltekkesjkt3.ac.id',
        ]);

        $ptExpired = PerguruanTinggi::create([
            'nama_pt' => 'STIKes Mitra Medika Nusantara',
            'status_mou' => false,
            'tgl_mulai_mou' => '2023-01-01',
            'tgl_akhir_mou' => '2025-01-01', // Expired
            'kontak' => '021-5551234',
            'email_pt' => 'info@stikesmitra.ac.id',
        ]);

        // 3. Unit Rumah Sakit & Kuota
        $unitIgd = Unit::create([
            'nama_unit' => 'Instalasi Gawat Darurat (IGD & Trauma)',
            'mode_kuota' => 'per_prodi',
            'kuota_maks' => 12,
            'deskripsi' => 'Pelayanan triase, resusitasi, tindakan gawat darurat bedah dan non-bedah.',
        ]);

        UnitProdiKuota::create(['unit_id' => $unitIgd->id, 'prodi' => 'S1 Keperawatan / Ners', 'kuota_maks' => 6]);
        UnitProdiKuota::create(['unit_id' => $unitIgd->id, 'prodi' => 'D4 Keperawatan Anestesiologi', 'kuota_maks' => 3]);
        UnitProdiKuota::create(['unit_id' => $unitIgd->id, 'prodi' => 'Profesi Dokter', 'kuota_maks' => 3]);

        $unitIcu = Unit::create([
            'nama_unit' => 'Intensive Care Unit (ICU / ICCU)',
            'mode_kuota' => 'gabungan',
            'kuota_maks' => 8,
            'deskripsi' => 'Perawatan intensif dan monitoring hemodinamik pasien kritis.',
        ]);

        $unitBedah = Unit::create([
            'nama_unit' => 'Instalasi Bedah Sentral (IBS)',
            'mode_kuota' => 'per_prodi',
            'kuota_maks' => 10,
            'deskripsi' => 'Kamar operasi terpadu bedah umum, ortopedi, kebidanan, dan THT.',
        ]);

        UnitProdiKuota::create(['unit_id' => $unitBedah->id, 'prodi' => 'S1 Keperawatan / Ners', 'kuota_maks' => 5]);
        UnitProdiKuota::create(['unit_id' => $unitBedah->id, 'prodi' => 'D4 Keperawatan Anestesiologi', 'kuota_maks' => 3]);
        UnitProdiKuota::create(['unit_id' => $unitBedah->id, 'prodi' => 'D3 Kebidanan', 'kuota_maks' => 2]);

        $unitFarmasi = Unit::create([
            'nama_unit' => 'Instalasi Farmasi & Depo Obat RS',
            'mode_kuota' => 'gabungan',
            'kuota_maks' => 15,
            'deskripsi' => 'Pelayanan farmasi rawat jalan, rawat inap, sterilisasi, dan depo farmasi sentral.',
        ]);

        $unitLab = Unit::create([
            'nama_unit' => 'Laboratorium Patologi Klinik & BDRS',
            'mode_kuota' => 'gabungan',
            'kuota_maks' => 10,
            'deskripsi' => 'Pemeriksaan hematologi, kimia darah, mikrobiologi, dan bank darah rumah sakit.',
        ]);

        // 4. Profil peserta diklat
        $pegawai1 = Peserta::create([
            'uuid' => Str::uuid(),
            'user_id' => $userPegawai1->id,
            'nama' => 'dr. Andika Pratama',
            'no_pegawai' => 'PEG-NONASN-2024-001',
            'unit_kerja' => 'Instalasi Gawat Darurat',
            'jabatan' => 'Dokter Umum Jaga IGD',
            'tgl_mulai_kerja' => '2024-02-01',
        ]);

        $pegawai2 = Peserta::create([
            'uuid' => Str::uuid(),
            'user_id' => $userPegawai2->id,
            'nama' => 'Siti Nurhaliza, A.Md.Farm',
            'no_pegawai' => 'PEG-NONASN-2023-042',
            'unit_kerja' => 'Instalasi Farmasi',
            'jabatan' => 'Asisten Apoteker Pelaksana',
            'tgl_mulai_kerja' => '2023-08-15',
        ]);

        // 5. Pembimbing Lapangan & Dosen
        $ciLapangan = PembimbingLapangan::create([
            'user_id' => $userPembimbingLapangan->id,
            'nama' => 'Ns. Bambang Hidayat, S.Tr.Kep',
            'unit_id' => $unitIgd->id,
            'kontak' => '081298765432',
            'nip_nik' => '198504122010011003',
        ]);

        $ciDosen = PembimbingDosen::create([
            'user_id' => $userPembimbingDosen->id,
            'pt_id' => $pt1->id,
            'nama' => 'Dr. drg. Maya Anggraini, M.Kes',
            'kontak' => '081311223344',
            'nidn' => '0314057801',
        ]);

        // 6. Mahasiswa
        $mhs1 = Mahasiswa::create([
            'user_id' => $userMahasiswa1->id,
            'pt_id' => $pt1->id,
            'nama' => 'Ahmad Fauzi',
            'nim' => '2106789012',
            'prodi' => 'S1 Keperawatan / Ners',
            'id_pembimbing_lapangan' => $ciLapangan->id,
            'id_pembimbing_dosen' => $ciDosen->id,
        ]);

        $mhs2 = Mahasiswa::create([
            'user_id' => $userMahasiswa2->id,
            'pt_id' => $pt1->id,
            'nama' => 'Dinda Putri Rahmawati',
            'nim' => '2106789045',
            'prodi' => 'S1 Keperawatan / Ners',
            'id_pembimbing_lapangan' => $ciLapangan->id,
            'id_pembimbing_dosen' => $ciDosen->id,
        ]);

        // 7. Modul 1: Sertifikat & Target Pelatihan peserta diklat
        $sertifikat1 = Sertifikat::create([
            'pegawai_id' => $pegawai1->id,
            'nama_pelatihan' => 'Advanced Cardiac Life Support (ACLS) PERKI',
            'penyelenggara' => 'Perhimpunan Dokter Spesialis Kardiovaskular Indonesia (PERKI)',
            'tgl_pelaksanaan' => '2026-03-10',
            'no_sertifikat' => 'ACLS-PERKI/2026/0892',
            'file_path' => 'sertifikat/sample_acls_dr_andika.pdf',
            'status_verifikasi' => 'disetujui',
            'catatan_verifikator' => 'Sertifikat terverifikasi keasliannya dan masih berlaku 3 tahun.',
            'verified_by' => $adminDiklat->id,
            'verified_at' => now(),
        ]);

        Sertifikat::create([
            'pegawai_id' => $pegawai2->id,
            'nama_pelatihan' => 'Pelatihan Penanganan B3 & Limbah Medis B3 Rumah Sakit',
            'penyelenggara' => 'Pusat Diklat Kesehatan Kemenkes RI',
            'tgl_pelaksanaan' => '2026-08-14',
            'no_sertifikat' => 'B3-RS/KEMENKES/2026/114',
            'file_path' => 'sertifikat/sample_b3_siti.pdf',
            'status_verifikasi' => 'pending',
            'catatan_verifikator' => null,
            'verified_by' => null,
            'verified_at' => null,
        ]);

        TargetPelatihan::create([
            'pegawai_id' => $pegawai1->id,
            'nama_target' => 'Pelatihan ACLS / ATLS Wajib IGD',
            'kategori' => 'Wajib',
            'periode' => '2026',
            'tenggat' => '2026-06-30',
            'status' => 'selesai',
            'sertifikat_pemenuhan_id' => $sertifikat1->id,
            'created_by' => $adminDiklat->id,
        ]);

        TargetPelatihan::create([
            'pegawai_id' => $pegawai1->id,
            'nama_target' => 'Peningkatan Mutu & Keselamatan Pasien (PMKP)',
            'kategori' => 'Wajib',
            'periode' => '2026',
            'tenggat' => '2026-11-30',
            'status' => 'proses',
            'sertifikat_pemenuhan_id' => null,
            'created_by' => $adminDiklat->id,
        ]);

        TargetPelatihan::create([
            'pegawai_id' => $pegawai2->id,
            'nama_target' => 'Pelatihan Manajemen Farmasi Klinik & Farmakovigilans',
            'kategori' => 'Fungsional',
            'periode' => '2026',
            'tenggat' => '2026-10-31',
            'status' => 'belum',
            'sertifikat_pemenuhan_id' => null,
            'created_by' => $adminDiklat->id,
        ]);

        // 8. Modul 2: Permohonan Praktik Mahasiswa & Surat Persetujuan
        $permohonan1 = PermohonanPraktik::create([
            'pt_id' => $pt1->id,
            'unit_id' => $unitIgd->id,
            'prodi' => 'S1 Keperawatan / Ners',
            'jumlah_mahasiswa' => 4,
            'tgl_mulai' => '2026-10-01',
            'tgl_selesai' => '2026-10-31',
            'file_surat_permohonan' => 'surat_permohonan/permohonan_ui_igd_okt2026.pdf',
            'status' => 'disetujui',
            'catatan_diklat' => 'Disetujui untuk stase Keperawatan Gawat Darurat (KGD).',
        ]);

        SuratPersetujuan::create([
            'permohonan_id' => $permohonan1->id,
            'nomor_surat' => '420/DIKLAT-RS/IX/2026',
            'file_pdf' => 'surat_persetujuan/surat_persetujuan_420_2026.pdf',
            'diterbitkan_oleh' => $adminDiklat->id,
            'tgl_terbit' => '2026-09-15',
            'catatan' => 'Mahasiswa wajib mengikuti orientasi K3RS dan PPI pada hari pertama.',
        ]);

        PenunjukanPembimbing::create([
            'permohonan_id' => $permohonan1->id,
            'mahasiswa_id' => $mhs1->id,
            'pembimbing_lapangan_id' => $ciLapangan->id,
            'pembimbing_dosen_id' => $ciDosen->id,
        ]);

        PenunjukanPembimbing::create([
            'permohonan_id' => $permohonan1->id,
            'mahasiswa_id' => $mhs2->id,
            'pembimbing_lapangan_id' => $ciLapangan->id,
            'pembimbing_dosen_id' => $ciDosen->id,
        ]);

        PermohonanPraktik::create([
            'pt_id' => $pt2->id,
            'unit_id' => $unitBedah->id,
            'prodi' => 'D3 Kebidanan',
            'jumlah_mahasiswa' => 2,
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2026-11-28',
            'file_surat_permohonan' => 'surat_permohonan/permohonan_poltekkes_nov2026.pdf',
            'status' => 'diajukan',
            'catatan_diklat' => null,
        ]);

        // 9. Kriteria Penilaian Praktik
        $kriteria1 = KriteriaPenilaian::create([
            'nama_kriteria' => 'Kedisiplinan, Sikap Profesional & Etika Medis',
            'bobot' => 20.00,
            'urutan' => 1,
            'aktif' => true,
        ]);

        $kriteria2 = KriteriaPenilaian::create([
            'nama_kriteria' => 'Keterampilan Klinis & Penerapan Prosedur SOP',
            'bobot' => 35.00,
            'urutan' => 2,
            'aktif' => true,
        ]);

        $kriteria3 = KriteriaPenilaian::create([
            'nama_kriteria' => 'Komunikasi Terapeutik & Kerjasama Tim Antar-Profesi',
            'bobot' => 20.00,
            'urutan' => 3,
            'aktif' => true,
        ]);

        $kriteria4 = KriteriaPenilaian::create([
            'nama_kriteria' => 'Kelengkapan Laporan Kasus & Pengisian Logbook Klinis',
            'bobot' => 25.00,
            'urutan' => 4,
            'aktif' => true,
        ]);

        // 10. Penilaian Praktik Mahasiswa
        $penilaian1 = PenilaianPraktik::create([
            'mahasiswa_id' => $mhs1->id,
            'permohonan_id' => $permohonan1->id,
            'penilai_id' => $userPembimbingLapangan->id,
            'peran_penilai' => 'lapangan',
            'tgl_isi' => '2026-09-20',
            'nilai_akhir' => 88.50,
            'catatan_umum' => 'Mahasiswa sangat aktif, tanggap dalam penanganan kasus kegawatdaruratan, dan mematuhi prinsip sterilisasi.',
        ]);

        DetailPenilaian::create(['penilaian_id' => $penilaian1->id, 'kriteria_id' => $kriteria1->id, 'nilai' => 90.00, 'catatan' => 'Tepat waktu, pakaian rapi']);
        DetailPenilaian::create(['penilaian_id' => $penilaian1->id, 'kriteria_id' => $kriteria2->id, 'nilai' => 88.00, 'catatan' => 'Pemasangan infus dan EKG sangat baik']);
        DetailPenilaian::create(['penilaian_id' => $penilaian1->id, 'kriteria_id' => $kriteria3->id, 'nilai' => 85.00, 'catatan' => 'Komunikasi dengan keluarga pasien ramah dan jelas']);
        DetailPenilaian::create(['penilaian_id' => $penilaian1->id, 'kriteria_id' => $kriteria4->id, 'nilai' => 92.00, 'catatan' => 'Logbook terisi lengkap setiap shift']);
    }
}
