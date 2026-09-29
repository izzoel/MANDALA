<!DOCTYPE html>
<html class="scroll-smooth antialiased" lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>MANDALA - Manajemen Diklat Akademik & Layanan Administrasi Rumah Sakit</title>
  <meta name="description" content="Sistem Informasi Terpadu Tata Kelola Pendidikan & Pelatihan (Diklat), Booking Unit Praktik Klinik Mahasiswa (Diklit), dan Evaluasi Kompetensi Rumah Sakit.">
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

  <!-- Google Fonts: Plus Jakarta Sans & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Tailwind CSS CDN with configuration -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script>
    tailwind.config = {
      darkMode: "class",
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
            hospital: {
              navy: '#0b192c',
              slate: '#1e293b',
              surface: '#f8fafc',
              border: '#e2e8f0',
              accent: '#0284c7',
            }
          },
          animation: {
            'float-slow': 'float 6s ease-in-out infinite',
            'pulse-subtle': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
          },
          keyframes: {
            float: {
              '0%, 100%': { transform: 'translateY(0px)' },
              '50%': { transform: 'translateY(-10px)' },
            }
          }
        },
      },
    }
  </script>

  <style>
    .mesh-gradient {
      background-color: #f8fafc;
      background-image: 
        radial-gradient(at 15% 15%, rgba(16, 185, 129, 0.12) 0px, transparent 50%),
        radial-gradient(at 85% 20%, rgba(2, 132, 199, 0.10) 0px, transparent 50%),
        radial-gradient(at 50% 80%, rgba(5, 150, 105, 0.08) 0px, transparent 50%),
        radial-gradient(at 90% 85%, rgba(20, 184, 166, 0.12) 0px, transparent 50%);
    }

    .grid-pattern {
      background-size: 32px 32px;
      background-image: linear-gradient(to right, rgba(226, 232, 240, 0.6) 1px, transparent 1px),
                        linear-gradient(to bottom, rgba(226, 232, 240, 0.6) 1px, transparent 1px);
    }

    .glass-card {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.7);
    }

    .glass-nav {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border-bottom: 1px solid rgba(226, 232, 240, 0.8);
    }
  </style>
</head>

<body class="bg-slate-50 text-slate-800 font-sans selection:bg-brand-100 selection:text-brand-900 min-h-screen flex flex-col">

  <!-- TOP NOTIFICATION BANNER -->
  <div class="bg-gradient-to-r from-brand-900 via-hospital-navy to-brand-900 text-white text-xs font-medium py-2 px-4 text-center relative z-50">
    <div class="max-w-7xl mx-auto flex items-center justify-center gap-2">
      <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-brand-500/20 text-brand-300 border border-brand-400/30">
        Versi 2.4
      </span>
      <span>Portal Layanan Pendidikan, Pelatihan &amp; Izin Praktik Mahasiswa Rumah Sakit Resmi Dibuka</span>
    </div>
  </div>

  <!-- NAVIGATION BAR -->
  <header class="sticky top-0 w-full z-40 glass-nav transition-all duration-300" id="main-header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
      <!-- Logo Brand -->
      <a href="{{ url('/') }}" class="flex items-center gap-3.5 group">
        <div class="size-11 rounded-xl bg-gradient-to-tr from-brand-700 to-brand-500 flex items-center justify-center shadow-lg shadow-brand-600/20 ring-1 ring-white/60 group-hover:scale-105 transition-transform duration-200">
          <svg class="size-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
          </svg>
        </div>
        <div class="flex flex-col">
          <span class="text-xl font-extrabold tracking-tight text-slate-900 flex items-center gap-1.5">
            MANDALA
            <span class="text-[10px] uppercase font-bold tracking-widest px-1.5 py-0.5 rounded bg-brand-100 text-brand-800 border border-brand-200">RS</span>
          </span>
          <span class="text-[11px] font-medium text-slate-500 tracking-tight leading-none">Diklat &amp; Akademik Terpadu</span>
        </div>
      </a>

      <!-- Desktop Nav Links -->
      <nav class="hidden md:flex items-center gap-8 font-medium text-sm text-slate-600">
        <a href="#fitur" class="hover:text-brand-600 transition-colors">Modul Sistem</a>
        <a href="#alur-praktik" class="hover:text-brand-600 transition-colors">Alur Praktik Mahasiswa</a>
        <a href="#unit-kuota" class="hover:text-brand-600 transition-colors">Unit &amp; Kuota</a>
        <a href="#peran" class="hover:text-brand-600 transition-colors">Akses Peran</a>
        <a href="#faq" class="hover:text-brand-600 transition-colors">Bantuan</a>
      </nav>

      <!-- Auth Actions -->
      <div class="flex items-center gap-3">
        @auth
          <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 text-white font-semibold text-sm shadow-md shadow-brand-600/20 hover:from-brand-700 hover:to-brand-600 hover:shadow-lg transition-all duration-200">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
            </svg>
            Buka Dashboard
          </a>
        @else
          <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 text-white font-semibold text-sm shadow-md hover:bg-slate-800 transition-all duration-200 hover:scale-[1.02] active:scale-98">
            <svg class="size-4 text-brand-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
            </svg>
            Masuk Sistem
          </a>
        @endauth
      </div>
    </div>
  </header>

  <!-- MAIN HERO SECTION -->
  <main class="relative mesh-gradient flex-1">
    <!-- Background Grid Texture -->
    <div class="absolute inset-0 grid-pattern opacity-40 pointer-events-none"></div>

    <section class="relative pt-12 pb-24 lg:pt-20 lg:pb-32 overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
          
          <!-- Hero Left Column -->
          <div class="lg:col-span-7 space-y-8 text-center lg:text-left">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-white/90 border border-brand-200/80 shadow-sm text-xs font-semibold text-brand-800">
              <span class="flex size-2 rounded-full bg-brand-500 animate-pulse"></span>
              Sistem Tata Kelola Diklat &amp; Praktik Klinik Rumah Sakit
            </div>

            <!-- Headline -->
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
              Kelola Pendidikan, Pelatihan &amp; 
              <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 via-teal-600 to-hospital-accent">
                Praktik Klinik Rumah Sakit
              </span>
              dalam Satu Platform
            </h1>

            <!-- Subtitle -->
            <p class="text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 font-body leading-relaxed">
              MANDALA mengintegrasikan pemantauan target sertifikat tenaga kesehatan, alokasi kuota unit cerdas anti-tabrakan jadwal, perizinan stase mahasiswa perguruan tinggi, serta evaluasi kompetensi klinis terbobot.
            </p>

            <!-- Actions Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
              <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 text-white font-bold text-base shadow-xl shadow-brand-600/25 hover:from-brand-700 hover:to-brand-600 hover:shadow-2xl hover:scale-[1.02] active:scale-98 transition-all">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
                Masuk ke Portal Diklat
              </a>
              <a href="#alur-praktik" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold text-base shadow-sm hover:bg-slate-50 hover:border-slate-300 transition-all">
                <svg class="size-5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Pelajari Alur Praktik
              </a>
            </div>

            <!-- Key Trust Metric Badges -->
            <div class="pt-6 border-t border-slate-200/80 grid grid-cols-3 gap-4 max-w-lg mx-auto lg:mx-0">
              <div>
                <div class="text-2xl lg:text-3xl font-extrabold text-slate-900">100%</div>
                <div class="text-xs text-slate-500 font-medium">Validasi Overlap Kuota</div>
              </div>
              <div>
                <div class="text-2xl lg:text-3xl font-extrabold text-brand-600">Dual Mode</div>
                <div class="text-xs text-slate-500 font-medium">Kuota Gabungan / Prodi</div>
              </div>
              <div>
                <div class="text-2xl lg:text-3xl font-extrabold text-hospital-accent">RBAC</div>
                <div class="text-xs text-slate-500 font-medium">Spatie + Passkey 2FA</div>
              </div>
            </div>

          </div>

          <!-- Hero Right Column: Interactive UI Mockup -->
          <div class="lg:col-span-5 relative">
            
            <!-- Glow background effect -->
            <div class="absolute -top-12 -right-12 size-72 bg-brand-400/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-12 -left-12 size-72 bg-hospital-accent/20 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Main Interactive Card -->
            <div class="relative glass-card rounded-2xl shadow-2xl shadow-slate-900/10 border border-white p-6 space-y-5 animate-float-slow">
              
              <!-- Card Header -->
              <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                  <div class="size-9 rounded-lg bg-brand-50 border border-brand-200/60 flex items-center justify-center text-brand-600">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-sm font-bold text-slate-900">Live Keterisian Unit RS</h3>
                    <p class="text-[11px] text-slate-500">Pembaruan kapasitas real-time</p>
                  </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                  <span class="size-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                  Sistem Aktif
                </span>
              </div>

              <!-- Unit Occupancy Progress Items -->
              <div class="space-y-3.5">
                <!-- Unit 1 -->
                <div class="bg-slate-50/80 rounded-xl p-3 border border-slate-100 space-y-2">
                  <div class="flex justify-between items-center text-xs">
                    <span class="font-bold text-slate-800 flex items-center gap-1.5">
                      <span class="size-2 rounded-full bg-emerald-500"></span>
                      Instalasi Gawat Darurat (IGD)
                    </span>
                    <span class="font-semibold text-slate-600">8 / 10 Mahasiswa</span>
                  </div>
                  <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width: 80%"></div>
                  </div>
                  <div class="flex justify-between items-center text-[10px] text-slate-400">
                    <span>Mode: Gabungan</span>
                    <span class="text-emerald-600 font-semibold">Tersisa 2 Slot</span>
                  </div>
                </div>

                <!-- Unit 2 -->
                <div class="bg-slate-50/80 rounded-xl p-3 border border-slate-100 space-y-2">
                  <div class="flex justify-between items-center text-xs">
                    <span class="font-bold text-slate-800 flex items-center gap-1.5">
                      <span class="size-2 rounded-full bg-blue-500"></span>
                      Intensive Care Unit (ICU)
                    </span>
                    <span class="font-semibold text-slate-600">4 / 6 Mahasiswa</span>
                  </div>
                  <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full" style="width: 66%"></div>
                  </div>
                  <div class="flex justify-between items-center text-[10px] text-slate-400">
                    <span>Mode: Per-Prodi (Ners &amp; Dokter)</span>
                    <span class="text-blue-600 font-semibold">Tersisa 2 Slot</span>
                  </div>
                </div>

                <!-- Unit 3 -->
                <div class="bg-slate-50/80 rounded-xl p-3 border border-slate-100 space-y-2">
                  <div class="flex justify-between items-center text-xs">
                    <span class="font-bold text-slate-800 flex items-center gap-1.5">
                      <span class="size-2 rounded-full bg-indigo-500"></span>
                      Instalasi Farmasi Klinik
                    </span>
                    <span class="font-semibold text-slate-600">5 / 5 Mahasiswa</span>
                  </div>
                  <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-500 rounded-full" style="width: 100%"></div>
                  </div>
                  <div class="flex justify-between items-center text-[10px] text-slate-400">
                    <span>Mode: Gabungan</span>
                    <span class="text-amber-600 font-semibold">Penuh (Stase Berjalan)</span>
                  </div>
                </div>
              </div>

              <!-- Floating Micro Card 1: Sertifikat Diklat Terverifikasi -->
              <div class="bg-gradient-to-br from-brand-900 to-slate-900 rounded-xl p-3.5 text-white flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-3">
                  <div class="size-8 rounded-lg bg-brand-500/20 text-brand-300 flex items-center justify-center">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>
                  </div>
                  <div>
                    <div class="text-xs font-bold">Verifikasi Sertifikat ACLS</div>
                    <div class="text-[10px] text-brand-300">Pelatihan Pegawai Non-ASN</div>
                  </div>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 font-semibold">
                  Disetujui
                </span>
              </div>

            </div>

          </div>

        </div>
      </div>
    </section>

    <!-- SECTION: 3 PILAR FITUR UTAMA -->
    <section id="fitur" class="py-20 bg-white border-y border-slate-200/80">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-50 text-brand-700 text-xs font-bold uppercase tracking-wider">
            Arsitektur Fungsional
          </div>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Tiga Modul Utama Penunjang Mutu Rumah Sakit
          </h2>
          <p class="text-slate-600 font-body text-base">
            Dibangun dengan standar tata kelola rumah sakit modern untuk menjamin keabsahan dokumen, tertib administrasi, dan kepatuhan akreditasi.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          
          <!-- Card 1: Modul Diklat -->
          <div class="rounded-2xl bg-slate-50 border border-slate-200/80 p-8 hover:shadow-xl hover:border-brand-300 hover:bg-white transition-all duration-300 group flex flex-col justify-between">
            <div class="space-y-5">
              <div class="size-14 rounded-2xl bg-brand-600 text-white flex items-center justify-center shadow-lg shadow-brand-600/20 group-hover:scale-110 transition-transform">
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                </svg>
              </div>
              <h3 class="text-xl font-bold text-slate-900 group-hover:text-brand-700 transition-colors">
                1. Pelatihan &amp; Sertifikasi Pegawai (Diklat)
              </h3>
              <p class="text-slate-600 text-sm font-body leading-relaxed">
                Penugasan pelatihan fungsional tahunan bagi tenaga kesehatan dan staf non-ASN. Unggah portofolio sertifikat mandiri dengan verifikasi berjenjang oleh Tim Diklat RS.
              </p>
              <ul class="space-y-2 text-xs text-slate-600 font-medium pt-2">
                <li class="flex items-center gap-2">
                  <svg class="size-4 text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                  Pelatihan Wajib &amp; Fungsional
                </li>
                <li class="flex items-center gap-2">
                  <svg class="size-4 text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                  Unggah Berkas PDF Mandiri (Max 5MB)
                </li>
                <li class="flex items-center gap-2">
                  <svg class="size-4 text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                  Audit &amp; Verifikasi Keabsahan Dokumen
                </li>
              </ul>
            </div>
          </div>

          <!-- Card 2: Modul Diklit & Booking -->
          <div class="rounded-2xl bg-slate-50 border border-slate-200/80 p-8 hover:shadow-xl hover:border-hospital-accent hover:bg-white transition-all duration-300 group flex flex-col justify-between">
            <div class="space-y-5">
              <div class="size-14 rounded-2xl bg-hospital-accent text-white flex items-center justify-center shadow-lg shadow-sky-600/20 group-hover:scale-110 transition-transform">
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                </svg>
              </div>
              <h3 class="text-xl font-bold text-slate-900 group-hover:text-hospital-accent transition-colors">
                2. Booking Unit Praktik &amp; MoU (Diklit)
              </h3>
              <p class="text-slate-600 text-sm font-body leading-relaxed">
                Portal pengajuan praktik mahasiswa perguruan tinggi mitra dengan validasi masa berlaku MoU dan alokasi kuota unit cerdas anti-tabrakan jadwal.
              </p>
              <ul class="space-y-2 text-xs text-slate-600 font-medium pt-2">
                <li class="flex items-center gap-2">
                  <svg class="size-4 text-hospital-accent" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                  Validasi Masa Aktif MoU Kampus
                </li>
                <li class="flex items-center gap-2">
                  <svg class="size-4 text-hospital-accent" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                  Pengecekan Kuota Overlap Real-time
                </li>
                <li class="flex items-center gap-2">
                  <svg class="size-4 text-hospital-accent" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                  Penerbitan Surat Persetujuan Resmi RS
                </li>
              </ul>
            </div>
          </div>

          <!-- Card 3: Evaluasi & Penilaian -->
          <div class="rounded-2xl bg-slate-50 border border-slate-200/80 p-8 hover:shadow-xl hover:border-teal-400 hover:bg-white transition-all duration-300 group flex flex-col justify-between">
            <div class="space-y-5">
              <div class="size-14 rounded-2xl bg-teal-600 text-white flex items-center justify-center shadow-lg shadow-teal-600/20 group-hover:scale-110 transition-transform">
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.09 1.976 1.053 1.976 2.188V18.75a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18.75V6.108c0-1.135.845-2.098 1.976-2.188.374-.03.748-.057 1.124-.08C7.033 3.668 7.888 3 8.9 3h1.3" />
                </svg>
              </div>
              <h3 class="text-xl font-bold text-slate-900 group-hover:text-teal-700 transition-colors">
                3. Pembimbing &amp; Evaluasi Klinis Terbobot
              </h3>
              <p class="text-slate-600 text-sm font-body leading-relaxed">
                Penunjukan Clinical Instructor (CI) RS dan Dosen Pembimbing untuk memantau mahasiswa serta input nilai kompetensi klinis berbobot persentase.
              </p>
              <ul class="space-y-2 text-xs text-slate-600 font-medium pt-2">
                <li class="flex items-center gap-2">
                  <svg class="size-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                  Penetapan CI Lapangan &amp; Dosen PT
                </li>
                <li class="flex items-center gap-2">
                  <svg class="size-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                  Master Kriteria Evaluasi Dinamis (100%)
                </li>
                <li class="flex items-center gap-2">
                  <svg class="size-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                  Kalkulasi Skor Otomatis 0–100
                </li>
              </ul>
            </div>
          </div>

        </div>

      </div>
    </section>

    <!-- SECTION: ALUR PERIZINAN PRAKTIK MAHASISWA -->
    <section id="alur-praktik" class="py-20 relative overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-200/80 text-slate-800 text-xs font-bold uppercase tracking-wider">
            Standard Operating Procedure
          </div>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Alur Pengajuan Praktik Klinik Mahasiswa
          </h2>
          <p class="text-slate-600 font-body text-base">
            Proses permohonan stase transparan, terstruktur, dan tervalidasi otomatis dari hulu ke hilir.
          </p>
        </div>

        <!-- 4 Step Flow -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 relative">
          
          <!-- Step 1 -->
          <div class="glass-card rounded-2xl p-6 relative border border-slate-200/80 space-y-4 hover:shadow-lg transition-all">
            <div class="size-10 rounded-full bg-brand-100 text-brand-700 font-extrabold flex items-center justify-center text-base border border-brand-200">
              1
            </div>
            <h4 class="text-base font-bold text-slate-900">Pilih Unit &amp; Cek Kuota</h4>
            <p class="text-xs text-slate-600 font-body leading-relaxed">
              Admin PT memilih ruangan RS dan memasukkan rentang tanggal stase. Sistem otomatis memvalidasi keaktifan MoU &amp; sisa kapasitas kuota unit.
            </p>
          </div>

          <!-- Step 2 -->
          <div class="glass-card rounded-2xl p-6 relative border border-slate-200/80 space-y-4 hover:shadow-lg transition-all">
            <div class="size-10 rounded-full bg-brand-100 text-brand-700 font-extrabold flex items-center justify-center text-base border border-brand-200">
              2
            </div>
            <h4 class="text-base font-bold text-slate-900">Upload Surat Permohonan</h4>
            <p class="text-xs text-slate-600 font-body leading-relaxed">
              Admin PT mengunggah surat resmi dari institusi beserta daftar mahasiswa praktikan yang akan diterjunkan ke rumah sakit.
            </p>
          </div>

          <!-- Step 3 -->
          <div class="glass-card rounded-2xl p-6 relative border border-slate-200/80 space-y-4 hover:shadow-lg transition-all">
            <div class="size-10 rounded-full bg-brand-100 text-brand-700 font-extrabold flex items-center justify-center text-base border border-brand-200">
              3
            </div>
            <h4 class="text-base font-bold text-slate-900">Persetujuan &amp; Surat Izin RS</h4>
            <p class="text-xs text-slate-600 font-body leading-relaxed">
              Tim Diklat RS meninjau permohonan, menerbitkan nomor surat izin persetujuan praktik resmi, dan mencatat jadwal final.
            </p>
          </div>

          <!-- Step 4 -->
          <div class="glass-card rounded-2xl p-6 relative border border-slate-200/80 space-y-4 hover:shadow-lg transition-all">
            <div class="size-10 rounded-full bg-brand-100 text-brand-700 font-extrabold flex items-center justify-center text-base border border-brand-200">
              4
            </div>
            <h4 class="text-base font-bold text-slate-900">Bimbingan &amp; Penilaian</h4>
            <p class="text-xs text-slate-600 font-body leading-relaxed">
              CI Lapangan RS &amp; Dosen PT membimbing mahasiswa di ruangan dan menginput nilai evaluasi kompetensi klinis berbasis skor terbobot.
            </p>
          </div>

        </div>

      </div>
    </section>

    <!-- SECTION: UNIT RS & FASILITAS PRAKTIK -->
    <section id="unit-kuota" class="py-20 bg-slate-900 text-white relative overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-500/20 text-brand-300 text-xs font-bold uppercase tracking-wider border border-brand-400/30">
            Fasilitas Stase Klinis
          </div>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
            Unit &amp; Instalasi Rumah Sakit Siap Praktik
          </h2>
          <p class="text-slate-300 font-body text-base">
            Mendukung berbagai program studi: Profesi Dokter, Ners, Kebidanan, Farmasi Klinik, Radiologi, Analis Kesehatan, dan Manajemen Informasi Kesehatan.
          </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-center">
          
          <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 space-y-3 hover:border-brand-400 hover:bg-slate-800 transition-all">
            <div class="size-12 mx-auto rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center">
              <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </div>
            <h5 class="text-sm font-bold text-white">IGD 24 Jam</h5>
            <p class="text-[11px] text-slate-400">Emergency Care</p>
          </div>

          <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 space-y-3 hover:border-brand-400 hover:bg-slate-800 transition-all">
            <div class="size-12 mx-auto rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center">
              <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>
            </div>
            <h5 class="text-sm font-bold text-white">ICU &amp; HCU</h5>
            <p class="text-[11px] text-slate-400">Intensive Care</p>
          </div>

          <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 space-y-3 hover:border-brand-400 hover:bg-slate-800 transition-all">
            <div class="size-12 mx-auto rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
              <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 1-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/></svg>
            </div>
            <h5 class="text-sm font-bold text-white">Bedah Sentral</h5>
            <p class="text-[11px] text-slate-400">Operating Theater</p>
          </div>

          <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 space-y-3 hover:border-brand-400 hover:bg-slate-800 transition-all">
            <div class="size-12 mx-auto rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center">
              <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
            </div>
            <h5 class="text-sm font-bold text-white">Farmasi Klinik</h5>
            <p class="text-[11px] text-slate-400">Clinical Pharmacy</p>
          </div>

          <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 space-y-3 hover:border-brand-400 hover:bg-slate-800 transition-all">
            <div class="size-12 mx-auto rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center">
              <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            </div>
            <h5 class="text-sm font-bold text-white">Laboratorium</h5>
            <p class="text-[11px] text-slate-400">Patologi &amp; Kimia</p>
          </div>

          <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 space-y-3 hover:border-brand-400 hover:bg-slate-800 transition-all">
            <div class="size-12 mx-auto rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center">
              <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
            </div>
            <h5 class="text-sm font-bold text-white">Rawat Inap</h5>
            <p class="text-[11px] text-slate-400">Interna &amp; Bedah</p>
          </div>

        </div>

      </div>
    </section>

    <!-- SECTION: ROLE-BASED ACCESS (RBAC) -->
    <section id="peran" class="py-20 bg-white border-b border-slate-200/80">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-50 text-brand-700 text-xs font-bold uppercase tracking-wider">
            Hak Akses &amp; Keamanan
          </div>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Antarmuka Khusus untuk Setiap Pemangku Kepentingan
          </h2>
          <p class="text-slate-600 font-body text-base">
            Sistem Role-Based Access Control (RBAC) granular dengan privilege spesifik untuk menjaga privasi data dan efisiensi alur kerja.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          
          <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-6 space-y-3">
            <div class="size-10 rounded-xl bg-brand-100 text-brand-700 font-bold flex items-center justify-center">
              AD
            </div>
            <h4 class="text-base font-bold text-slate-900">Admin Diklat RS</h4>
            <p class="text-xs text-slate-600 font-body leading-relaxed">
              Verifikasi sertifikat, kelola kuota unit, persetujuan booking izin praktik, penerbitan surat, dan master kriteria nilai.
            </p>
          </div>

          <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-6 space-y-3">
            <div class="size-10 rounded-xl bg-sky-100 text-sky-700 font-bold flex items-center justify-center">
              PT
            </div>
            <h4 class="text-base font-bold text-slate-900">Admin Perguruan Tinggi</h4>
            <p class="text-xs text-slate-600 font-body leading-relaxed">
              Cek matriks keterisian unit, pantau status MoU kampus, ajukan permohonan stase mahasiswa, dan unduh surat izin RS.
            </p>
          </div>

          <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-6 space-y-3">
            <div class="size-10 rounded-xl bg-teal-100 text-teal-700 font-bold flex items-center justify-center">
              CI
            </div>
            <h4 class="text-base font-bold text-slate-900">Clinical Instructor (CI RS)</h4>
            <p class="text-xs text-slate-600 font-body leading-relaxed">
              Bimbingan stase langsung di ruangan RS, pemantauan logbook, dan input form penilaian kompetensi klinis mahasiswa.
            </p>
          </div>

          <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-6 space-y-3">
            <div class="size-10 rounded-xl bg-purple-100 text-purple-700 font-bold flex items-center justify-center">
              PG
            </div>
            <h4 class="text-base font-bold text-slate-900">Peserta Diklat / Pegawai</h4>
            <p class="text-xs text-slate-600 font-body leading-relaxed">
              Pantau pemenuhan kewajiban pelatihan tahunan, unggah berkas sertifikat mandiri, dan unduh transkrip pelatihan.
            </p>
          </div>

        </div>

      </div>
    </section>

    <!-- SECTION: FAQ -->
    <section id="faq" class="py-20 bg-slate-50">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        <div class="text-center space-y-4">
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-200/80 text-slate-800 text-xs font-bold uppercase tracking-wider">
            Pertanyaan Umum
          </div>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Frequently Asked Questions (FAQ)
          </h2>
        </div>

        <div class="space-y-4">
          
          <details class="group bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm [&_summary::-webkit-details-marker]:hidden">
            <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-900 text-base">
              Bagaimana cara perguruan tinggi mengajukan praktik mahasiswa?
              <span class="ml-4 flex-shrink-0 size-6 rounded-full bg-slate-100 flex items-center justify-center group-open:rotate-180 transition-transform">
                <svg class="size-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
              </span>
            </summary>
            <p class="mt-4 text-sm text-slate-600 font-body leading-relaxed">
              Admin Perguruan Tinggi yang telah memiliki akun dan MoU aktif dapat masuk ke menu <strong>Diklit &gt; Booking Praktik</strong>, memilih unit ruangan serta tanggal stase yang diinginkan, kemudian mengunggah surat permohonan resmi dan daftar nama mahasiswa.
            </p>
          </details>

          <details class="group bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm [&_summary::-webkit-details-marker]:hidden">
            <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-900 text-base">
              Apa yang terjadi jika tanggal stase bertabrakan dengan kuota unit yang penuh?
              <span class="ml-4 flex-shrink-0 size-6 rounded-full bg-slate-100 flex items-center justify-center group-open:rotate-180 transition-transform">
                <svg class="size-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
              </span>
            </summary>
            <p class="mt-4 text-sm text-slate-600 font-body leading-relaxed">
              Sistem MANDALA dilengkapi engine <code>BookingKuotaService</code> dengan algoritma overlap checking. Jika kuota unit pada rentang tanggal tersebut telah penuh oleh pengajuan yang disetujui sebelumnya, sistem akan memberikan notifikasi otomatis dan mencegah pengajuan ganda (collision prevention).
            </p>
          </details>

          <details class="group bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm [&_summary::-webkit-details-marker]:hidden">
            <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-900 text-base">
              Bagaimana staf/pegawai mengunggah sertifikat pelatihan?
              <span class="ml-4 flex-shrink-0 size-6 rounded-full bg-slate-100 flex items-center justify-center group-open:rotate-180 transition-transform">
                <svg class="size-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
              </span>
            </summary>
            <p class="mt-4 text-sm text-slate-600 font-body leading-relaxed">
              Pegawai masuk ke menu <strong>Diklat &gt; Sertifikat</strong>, memilih pelatihan yang sesuai, lalu mengunggah file sertifikat (format PDF/JPG/PNG maksimal 5MB). Berkas akan masuk ke antrean verifikasi Admin Diklat RS.
            </p>
          </details>

        </div>

      </div>
    </section>

    <!-- SECTION: CALL TO ACTION -->
    <section class="py-16 bg-gradient-to-r from-brand-900 via-hospital-navy to-brand-900 text-white relative overflow-hidden">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6 relative z-10">
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
          Mulai Digitalisasi Tata Kelola Diklat &amp; Praktik Rumah Sakit
        </h2>
        <p class="text-brand-100 text-base max-w-2xl mx-auto font-body">
          Akses portal MANDALA sekarang untuk kemudahan pengajuan izin stase, pemantauan kuota ruangan, dan sertifikasi pegawai.
        </p>
        <div class="pt-4 flex flex-wrap justify-center gap-4">
          <a href="{{ route('login') }}" class="px-8 py-4 rounded-xl bg-brand-500 text-slate-900 font-bold text-base shadow-xl hover:bg-brand-400 hover:scale-105 transition-all">
            Masuk ke Akun Anda
          </a>
        </div>
      </div>
    </section>

  </main>

  <!-- FOOTER -->
  <footer class="bg-white border-t border-slate-200 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
        
        <!-- Col 1: Brand Info -->
        <div class="md:col-span-2 space-y-4">
          <div class="flex items-center gap-3">
            <div class="size-9 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold">
              M
            </div>
            <span class="text-lg font-extrabold text-slate-900">MANDALA</span>
          </div>
          <p class="text-xs text-slate-500 font-body leading-relaxed max-w-sm">
            Manajemen Diklat Akademik &amp; Layanan Administrasi Rumah Sakit. Sistem terintegrasi tata kelola pelatihan SDM kesehatan dan izin stase mahasiswa perguruan tinggi.
          </p>
          <div class="text-xs text-slate-400 font-medium">
            &copy; 2026 Zetware. Hak cipta dilindungi undang-undang.
          </div>
        </div>

        <!-- Col 2: Modul Cepat -->
        <div class="space-y-3">
          <h5 class="text-xs font-bold uppercase tracking-wider text-slate-900">Modul Layanan</h5>
          <ul class="space-y-2 text-xs text-slate-600 font-medium">
            <li><a href="{{ route('login') }}" class="hover:text-brand-600 transition-colors">Pelatihan Pegawai</a></li>
            <li><a href="{{ route('login') }}" class="hover:text-brand-600 transition-colors">Verifikasi Sertifikat Diklat</a></li>
            <li><a href="{{ route('login') }}" class="hover:text-brand-600 transition-colors">Booking Praktik Mahasiswa</a></li>
            <li><a href="{{ route('login') }}" class="hover:text-brand-600 transition-colors">Evaluasi &amp; Penilaian Klinis</a></li>
          </ul>
        </div>

        <!-- Col 3: Bantuan & Kebijakan -->
        <div class="space-y-3">
          <h5 class="text-xs font-bold uppercase tracking-wider text-slate-900">Tautan Resmi</h5>
          <ul class="space-y-2 text-xs text-slate-600 font-medium">
            <li><a href="#alur-praktik" class="hover:text-brand-600 transition-colors">Panduan Alur Permohonan</a></li>
            <li><a href="#faq" class="hover:text-brand-600 transition-colors">Pusat Bantuan &amp; FAQ</a></li>
            <li><a href="#" class="hover:text-brand-600 transition-colors">Kebijakan Privasi</a></li>
            <li><a href="#" class="hover:text-brand-600 transition-colors">Syarat &amp; Ketentuan</a></li>
          </ul>
        </div>

      </div>
    </div>
  </footer>

  <script>
    // Header shadow on scroll
    window.addEventListener('scroll', () => {
      const header = document.getElementById('main-header');
      if (window.scrollY > 20) {
        header.classList.add('shadow-md');
      } else {
        header.classList.remove('shadow-md');
      }
    });
  </script>

</body>

</html>
