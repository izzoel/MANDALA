<!DOCTYPE html>
<html class="scroll-smooth antialiased" lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Kebijakan Privasi (Privacy Policy) - MANDALA RS</title>
  <meta name="description" content="Kebijakan Privasi Sistem Informasi Terpadu MANDALA Rumah Sakit dan Kepatuhan Penggunaan Data Google API.">
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">

  <!-- Google Fonts: Plus Jakarta Sans & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
            body: ['Inter', 'sans-serif'],
          },
          colors: {
            brand: {
              50: '#ecfdf5',
              100: '#d1fae5',
              200: '#a7f3d0',
              300: '#6ee7b7',
              400: '#34d399',
              500: '#10b981',
              600: '#059669',
              700: '#047857',
              800: '#065f46',
              900: '#064e3b',
              950: '#022c22',
            },
          }
        },
      },
    }
  </script>
</head>

<body class="bg-slate-50 text-slate-800 font-sans selection:bg-brand-100 selection:text-brand-900 min-h-screen flex flex-col">

  <!-- HEADER / NAV -->
  <header class="sticky top-0 w-full z-40 bg-white/90 backdrop-blur-md border-b border-slate-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
      <a href="{{ url('/') }}" class="flex items-center gap-3 group">
        <img src="{{ asset('mandala.svg') }}" alt="MANDALA Logo" class="size-10 object-contain drop-shadow-sm group-hover:scale-105 transition-transform">
        <div class="flex flex-col">
          <span class="text-lg font-extrabold text-slate-900 tracking-tight">MANDALA</span>
          <span class="text-[11px] font-medium text-slate-500">Tata Kelola Diklat & Praktik RS</span>
        </div>
      </a>

      <div class="flex items-center gap-4 text-sm font-medium">
        <a href="{{ url('/') }}" class="text-slate-600 hover:text-brand-600 transition-colors flex items-center gap-1.5">
          <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
          </svg>
          Kembali ke Beranda
        </a>
      </div>
    </div>
  </header>

  <!-- CONTENT -->
  <main class="flex-1 py-12 lg:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Card Container -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-10 space-y-8">
        
        <!-- Header Document -->
        <div class="border-b border-slate-200 pb-6 space-y-2">
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-50 text-brand-700 text-xs font-bold uppercase tracking-wider">
            Dokumen Resmi
          </div>
          <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Kebijakan Privasi (Privacy Policy)
          </h1>
          <p class="text-sm text-slate-500">
            Terakhir diperbarui: 30 September 2026 &bull; Berlaku untuk seluruh pengguna aplikasi MANDALA
          </p>
        </div>

        <!-- Section 1 -->
        <section class="space-y-3">
          <h2 class="text-xl font-bold text-slate-900">1. Pendahuluan</h2>
          <p class="text-slate-600 text-sm font-body leading-relaxed">
            Sistem Informasi <strong>MANDALA</strong> (Manajemen Diklat Akademik & Layanan Administrasi Rumah Sakit) berkomitmen untuk melindungi dan menghormati privasi seluruh pengguna, termasuk Tenaga Kesehatan Rumah Sakit, Peserta Pelatihan (Diklat), Administrator Perguruan Tinggi Mitra, Pembimbing/Clinical Instructor (CI), serta Mahasiswa Praktik Klinik.
          </p>
          <p class="text-slate-600 text-sm font-body leading-relaxed">
            Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, memproses, menyimpan, dan melindungi informasi data pribadi Anda saat menggunakan layanan kami.
          </p>
        </section>

        <!-- Section 2 -->
        <section class="space-y-3">
          <h2 class="text-xl font-bold text-slate-900">2. Data yang Kami Kumpulkan</h2>
          <p class="text-slate-600 text-sm font-body leading-relaxed">
            Untuk menunjang operasional tata kelola pendidikan dan pelatihan, sistem mengumpulkan data yang mencakup:
          </p>
          <ul class="list-disc list-inside space-y-2 text-sm text-slate-600 pl-2">
            <li><strong>Identitas Pengguna:</strong> Nama lengkap, alamat email, Nomor Induk Pegawai (NIP/NIK), Nomor Induk Mahasiswa (NIM), Nomor Induk Dosen Nasional (NIDN), serta unit kerja/program studi.</li>
            <li><strong>Dokumen Administrasi:</strong> Salinan berkas sertifikat pelatihan kompetensi (PDF), Nota Kesepahaman (MoU) kerjasama perguruan tinggi, dan surat resmi permohonan praktik mahasiswa.</li>
            <li><strong>Data Akademik & Klinis:</strong> Jadwal stase, alokasi kuota unit rumah sakit, penunjukan pembimbing lapangan, dan lembar evaluasi penilaian kompetensi klinis terbobot.</li>
          </ul>
        </section>

        <!-- Section 3: Google API Services User Data Policy Compliance -->
        <section class="space-y-4 rounded-xl bg-slate-50 border border-slate-200 p-5 sm:p-6">
          <div class="flex items-center gap-2.5 text-brand-700 font-bold text-base">
            <svg class="size-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-2.18-4.5H12a3 3 0 0 0-3 3v.18A3.003 3.003 0 0 0 6.18 12H6a3 3 0 0 0-3 3v.18A3.003 3.003 0 0 0 .18 18H0v2h24v-2h-.18a3.003 3.003 0 0 0-2.82-2.82V15a3 3 0 0 0-3-3h-.18A3.003 3.003 0 0 0 15 9.18V9a3 3 0 0 0-3-3Z" />
            </svg>
            <h3>3. Kebijakan Penggunaan Data Pengguna Google API (Google Drive Integration)</h3>
          </div>
          <p class="text-slate-600 text-sm font-body leading-relaxed">
            Aplikasi MANDALA menggunakan <strong>Google Drive API</strong> untuk memfasilitasi penyimpanan awan yang aman bagi dokumen sertifikat pelatihan rumah sakit, arsip MoU institusi mitra, dan surat izin praktik.
          </p>
          <div class="space-y-2 text-xs text-slate-700 font-medium">
            <p><strong>Kepatuhan Ketentuan Terbatas (Limited Use):</strong></p>
            <ul class="list-disc list-inside space-y-1.5 pl-2">
              <li>MANDALA hanya meminta izin akses Google Drive untuk membuat, mengunggah, membaca, dan mengarsipkan file dokumen yang secara eksplisit diunggah oleh pengguna dalam aplikasi.</li>
              <li>Aplikasi <strong>TIDAK PERNAH</strong> mentransfer atau menjual data pengguna Google kepada pihak ketiga mana pun.</li>
              <li>Aplikasi <strong>TIDAK PERNAH</strong> menggunakan data yang diperoleh dari Google API untuk keperluan periklanan (*advertising*), profiling komersial, atau pelatihan model kecerdasan buatan (*AI/ML model training*).</li>
              <li>Akses Google Drive diproses melalui kredensial aman (OAuth 2.0 / Service Account) yang terenkripsi dan disimpan di server rumah sakit.</li>
            </ul>
          </div>
        </section>

        <!-- Section 4 -->
        <section class="space-y-3">
          <h2 class="text-xl font-bold text-slate-900">4. Tujuan Penggunaan Informasi</h2>
          <p class="text-slate-600 text-sm font-body leading-relaxed">
            Informasi yang dikumpulkan digunakan secara eksklusif untuk:
          </p>
          <ul class="list-disc list-inside space-y-1.5 text-sm text-slate-600 pl-2">
            <li>Memvalidasi keabsahan dokumen sertifikat pelatihan fungsional tenaga kesehatan.</li>
            <li>Mengatur ketersediaan kuota unit rumah sakit dan mencegah benturan jadwal (*collision prevention*).</li>
            <li>Menerbitkan surat persetujuan izin praktik klinik resmi bagi mahasiswa perguruan tinggi mitra.</li>
            <li>Menyediakan rekapitulasi penilaian dan evaluasi bimbingan klinis oleh CI Rumah Sakit dan Dosen Pembimbing.</li>
          </ul>
        </section>

        <!-- Section 5 -->
        <section class="space-y-3">
          <h2 class="text-xl font-bold text-slate-900">5. Keamanan & Penyimpanan Data</h2>
          <p class="text-slate-600 text-sm font-body leading-relaxed">
            Kami menerapkan standar keamanan teknis dan organisasi yang ketat, meliputi enkripsi kata sandi (Bcrypt), otentikasi berbasis peran (Role-Based Access Control / RBAC), dukungan Passkey FIDO2 / Two-Factor Authentication (2FA), serta penyimpanan berkas dokumen fisik pada penyimpanan terisolasi yang aman.
          </p>
        </section>

        <!-- Section 6 -->
        <section class="space-y-3">
          <h2 class="text-xl font-bold text-slate-900">6. Hak Pengguna</h2>
          <p class="text-slate-600 text-sm font-body leading-relaxed">
            Pengguna berhak untuk melihat, memperbarui, atau meminta koreksi atas data profil pribadi yang tersimpan dalam sistem dengan menghubungi Administrator Diklat Rumah Sakit.
          </p>
        </section>

        <!-- Section 7 -->
        <section class="space-y-3 border-t border-slate-200 pt-6">
          <h2 class="text-xl font-bold text-slate-900">7. Kontak dan Layanan Bantuan</h2>
          <p class="text-slate-600 text-sm font-body leading-relaxed">
            Apabila Anda memiliki pertanyaan seputar Kebijakan Privasi ini atau pengelolaan data pada sistem MANDALA, silakan hubungi tim pengelola melalui:
          </p>
          <div class="rounded-xl bg-brand-50 border border-brand-200 p-4 text-xs text-brand-900 space-y-1">
            <p><strong>Unit Pendidikan & Pelatihan (Diklat) Rumah Sakit</strong></p>
            <p>Sistem Informasi Terpadu MANDALA &bull; Tapin, Kalimantan Selatan</p>
            <p>Email: <a href="mailto:diklat@tapinkab.go.id" class="underline font-semibold">diklat@tapinkab.go.id</a></p>
          </div>
        </section>

      </div>

    </div>
  </main>

  <!-- FOOTER -->
  <footer class="bg-white border-t border-slate-200 py-8 text-center text-xs text-slate-500">
    <div class="max-w-4xl mx-auto px-4 space-y-2">
      <div class="flex justify-center gap-6 font-medium">
        <a href="{{ route('privacy.policy') }}" class="text-brand-600 hover:underline font-bold">Kebijakan Privasi</a>
        <a href="{{ route('terms.service') }}" class="text-slate-600 hover:underline">Syarat & Ketentuan</a>
        <a href="{{ url('/') }}" class="text-slate-600 hover:underline">Beranda MANDALA</a>
      </div>
      <p>&copy; 2026 MANDALA. Hak Cipta Dilindungi.</p>
    </div>
  </footer>

</body>

</html>
