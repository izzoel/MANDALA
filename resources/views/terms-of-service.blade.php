<!DOCTYPE html>
<html class="scroll-smooth antialiased" lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Syarat dan Ketentuan Layanan (Terms of Service) - MANDALA RS</title>
  <meta name="description" content="Syarat dan Ketentuan Penggunaan Sistem Informasi Terpadu MANDALA Rumah Sakit.">
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
            Ketentuan Penggunaan
          </div>
          <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Syarat &amp; Ketentuan Layanan (Terms of Service)
          </h1>
          <p class="text-sm text-slate-500">
            Terakhir diperbarui: 30 September 2026 &bull; Berlaku untuk seluruh pengguna aplikasi MANDALA
          </p>
        </div>

        <!-- Section 1 -->
        <section class="space-y-3">
          <h2 class="text-xl font-bold text-slate-900">1. Ketentuan Umum</h2>
          <p class="text-slate-600 text-sm font-body leading-relaxed">
            Selamat datang di Sistem Informasi <strong>MANDALA</strong> (Manajemen Diklat Akademik & Layanan Administrasi Rumah Sakit). Dengan mengakses atau menggunakan aplikasi ini, Anda menyatakan telah membaca, memahami, dan menyetujui untuk terikat oleh Syarat dan Ketentuan Layanan ini.
          </p>
          <p class="text-slate-600 text-sm font-body leading-relaxed">
            Jika Anda tidak menyetujui ketentuan ini, Anda tidak diperkenankan untuk mengakses atau menggunakan layanan dalam sistem MANDALA.
          </p>
        </section>

        <!-- Section 2 -->
        <section class="space-y-3">
          <h2 class="text-xl font-bold text-slate-900">2. Akun Pengguna &amp; Keamanan Kredensial</h2>
          <ul class="list-disc list-inside space-y-2 text-sm text-slate-600 pl-2">
            <li>Pengguna bertanggung jawab penuh atas kerahasiaan nama pengguna (*username*), kata sandi (*password*), kunci keamanan (Passkey), dan seluruh aktivitas yang terjadi dalam akunnya.</li>
            <li>Akun pengguna bersifat personal dan tidak boleh dialihkan atau dipinjamkan kepada pihak lain tanpa izin tertulis dari pihak Rumah Sakit.</li>
            <li>Pengguna wajib segera memberitahukan Administrator Sistem apabila menemukan indikasi penggunaan akun tanpa izin atau pelanggaran keamanan lainnya.</li>
          </ul>
        </section>

        <!-- Section 3 -->
        <section class="space-y-3">
          <h2 class="text-xl font-bold text-slate-900">3. Ketentuan Pengajuan Praktik Klinik &amp; Nota Kesepahaman (MoU)</h2>
          <ul class="list-disc list-inside space-y-2 text-sm text-slate-600 pl-2">
            <li><strong>Masa Berlaku MoU:</strong> Perguruan Tinggi mitra hanya dapat mengajukan permohonan booking praktik klinik jika memiliki dokumen Nota Kesepahaman (MoU) yang masih aktif dan sah.</li>
            <li><strong>Alokasi Kuota Ruangan:</strong> Setiap pengajuan praktik tunduk pada ketersediaan kuota unit rumah sakit yang diverifikasi secara real-time. Sistem berhak menolak permohonan yang melebihi kapasitas maksimal ruangan.</li>
            <li><strong>Surat Izin Resmi:</strong> Mahasiswa hanya diizinkan memulai kegiatan stase klinis setelah Tim Diklat Rumah Sakit menerbitkan Surat Persetujuan resmi.</li>
          </ul>
        </section>

        <!-- Section 4 -->
        <section class="space-y-3">
          <h2 class="text-xl font-bold text-slate-900">4. Tata Tertib &amp; Keabsahan Dokumen</h2>
          <ul class="list-disc list-inside space-y-2 text-sm text-slate-600 pl-2">
            <li>Seluruh berkas yang diunggah (Sertifikat Pelatihan, Berkas MoU, Surat Permohonan) harus merupakan dokumen asli, sah, dan dapat dipertanggungjawabkan secara hukum.</li>
            <li>Pemalsuan dokumen atau pengunggahan berkas yang melanggar hukum akan mengakibatkan pembatalan izin praktik dan dapat diproses sesuai peraturan perundang-undangan yang berlaku.</li>
            <li>Mahasiswa dan pembimbing wajib mematuhi seluruh Standar Operasional Prosedur (SOP), etika profesi kesehatan, dan tata tertib keselamatan kerja rumah sakit (K3RS).</li>
          </ul>
        </section>

        <!-- Section 5 -->
        <section class="space-y-3">
          <h2 class="text-xl font-bold text-slate-900">5. Penilaian &amp; Evaluasi Kompetensi</h2>
          <p class="text-slate-600 text-sm font-body leading-relaxed">
            Penilaian kompetensi klinis dilakukan oleh Clinical Instructor (CI) Rumah Sakit dan Dosen Pembimbing berdasarkan kriteria evaluasi terbobot resmi. Nilai akhir yang telah divalidasi bersifat final dan menjadi arsip rekam jejak akademik resmi.
          </p>
        </section>

        <!-- Section 6 -->
        <section class="space-y-3">
          <h2 class="text-xl font-bold text-slate-900">6. Batasan Tanggung Jawab</h2>
          <p class="text-slate-600 text-sm font-body leading-relaxed">
            Pengelola sistem MANDALA berupaya menjaga ketersediaan layanan 24/7 dan integritas data secara maksimal. Namun demikian, pengelola tidak bertanggung jawab atas kerugian tidak langsung yang timbul akibat gangguan jaringan internet eksternal, bencana alam, atau kelalaian pengguna dalam menjaga kerahasiaan akun.
          </p>
        </section>

        <!-- Section 7 -->
        <section class="space-y-3 border-t border-slate-200 pt-6">
          <h2 class="text-xl font-bold text-slate-900">7. Perubahan Ketentuan</h2>
          <p class="text-slate-600 text-sm font-body leading-relaxed">
            Pengelola berhak memperbarui Syarat dan Ketentuan ini sewaktu-waktu sesuai dengan perkembangan kebijakan rumah sakit atau regulasi pemerintah. Pengguna disarankan untuk memeriksa halaman ini secara berkala.
          </p>
        </section>

      </div>

    </div>
  </main>

  <!-- FOOTER -->
  <footer class="bg-white border-t border-slate-200 py-8 text-center text-xs text-slate-500">
    <div class="max-w-4xl mx-auto px-4 space-y-2">
      <div class="flex justify-center gap-6 font-medium">
        <a href="{{ route('privacy.policy') }}" class="text-slate-600 hover:underline">Kebijakan Privasi</a>
        <a href="{{ route('terms.service') }}" class="text-brand-600 hover:underline font-bold">Syarat & Ketentuan</a>
        <a href="{{ url('/') }}" class="text-slate-600 hover:underline">Beranda MANDALA</a>
      </div>
      <p>&copy; 2026 MANDALA. Hak Cipta Dilindungi.</p>
    </div>
  </footer>

</body>

</html>
