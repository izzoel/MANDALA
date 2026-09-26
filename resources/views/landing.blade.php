<!DOCTYPE html>
<html class="light" lang="id" style="">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link
    href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&amp;family=Inter:wght@300;400;500;600&amp;display=swap"
    rel="stylesheet">
  <link
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet">
  <link
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet">
  <style>
    .hex-pattern {
      background-color: #ffffff;
      background-image: url("https://www.transparenttextures.com/patterns/carbon-fibre.png");
      background-repeat: repeat;
      opacity: 0.03;
    }

    .radial-glow {
      background: radial-gradient(circle at 80% 20%, rgba(167, 243, 208, 0.15) 0%, rgba(255, 255, 255, 0) 50%);
    }

    .glass-header {
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }
  </style>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          "colors": {
            "on-tertiary-fixed-variant": "#842225",
            "focus-surface": "#F0FDF4",
            "on-error-container": "#93000a",
            "accent-mint": "#A7F3D0",
            "secondary": "#006c4b",
            "on-tertiary": "#ffffff",
            "border-light": "#E2E8F0",
            "inverse-primary": "#4edea3",
            "text-secondary": "#475569",
            "badge-bg": "#E6F4EA",
            "surface-container-low": "#f2f3ff",
            "on-tertiary-container": "#711419",
            "tertiary": "#a43a3a",
            "secondary-container": "#64f9bc",
            "surface-variant": "#dae2fd",
            "secondary-fixed-dim": "#45dfa4",
            "on-secondary-container": "#00714e",
            "tertiary-container": "#fc7c78",
            "badge-text": "#137333",
            "error": "#ba1a1a",
            "on-surface-variant": "#3c4a42",
            "outline-variant": "#bbcabf",
            "surface-tint": "#006c49",
            "primary-container": "#10b981",
            "focus-text": "#15803D",
            "surface-bright": "#faf8ff",
            "on-secondary-fixed": "#002114",
            "on-primary-fixed-variant": "#005236",
            "on-tertiary-fixed": "#410005",
            "tertiary-fixed-dim": "#ffb3af",
            "on-primary-container": "#00422b",
            "surface-container-high": "#e2e7ff",
            "primary": "#006c49",
            "primary-fixed-dim": "#4edea3",
            "inverse-surface": "#283044",
            "surface-secondary": "#F8F9FA",
            "on-primary-fixed": "#002113",
            "surface-container-highest": "#dae2fd",
            "on-secondary-fixed-variant": "#005137",
            "tertiary-fixed": "#ffdad7",
            "surface-container": "#eaedff",
            "error-container": "#ffdad6",
            "outline": "#6c7a71",
            "on-surface": "#131b2e",
            "surface-dim": "#d2d9f4",
            "on-surface": "#131b2e",
            "on-primary": "#ffffff",
            "on-error": "#ffffff",
            "surface-main": "#FFFFFF",
            "primary-fixed": "#6ffbbe",
            "inverse-on-surface": "#eef0ff",
            "hover-neutral": "#F1F5F9",
            "surface": "#faf8ff",
            "background": "#faf8ff",
            "surface-container-lowest": "#ffffff",
            "on-secondary": "#ffffff",
            "on-background": "#131b2e",
            "secondary-fixed": "#68fcbf"
          },
          "borderRadius": {
            "DEFAULT": "0.25rem",
            "lg": "0.5rem",
            "xl": "0.75rem",
            "full": "9999px"
          },
          "spacing": {
            "grid-margin": "24px",
            "grid-gutter": "16px",
            "panel-padding": "2rem",
            "card-padding": "1.5rem",
            "section-gap": "4rem"
          },
          "fontFamily": {
            "display-hero": ["Plus Jakarta Sans"],
            "label-tiny": ["Inter"],
            "subheading-label": ["Plus Jakarta Sans"],
            "body-main": ["Inter"],
            "headline-stat": ["Plus Jakarta Sans"]
          },
          "fontSize": {
            "display-hero": ["48pt", {
              "lineHeight": "1.2",
              "fontWeight": "300"
            }],
            "label-tiny": ["10pt", {
              "lineHeight": "1.4",
              "fontWeight": "300"
            }],
            "subheading-label": ["12pt", {
              "lineHeight": "1.5",
              "fontWeight": "500"
            }],
            "body-main": ["11pt", {
              "lineHeight": "1.6",
              "fontWeight": "400"
            }],
            "headline-stat": ["28pt", {
              "lineHeight": "1.2",
              "fontWeight": "600"
            }]
          }
        },
      },
    }
  </script>
</head>

<body
  class="bg-surface-main text-on-surface font-body-main selection:bg-accent-mint selection:text-primary flex flex-col min-h-screen">
  <!-- Header Section -->
  <header class="fixed top-0 w-full z-50 bg-white/70 dark:bg-on-surface/70 backdrop-blur-md">
    <nav class="flex justify-between items-center px-grid-margin py-4 max-w-7xl mx-auto">
      <div class="flex items-center gap-3">
        <img
          src="https://lh3.googleusercontent.com/aida-public/AB6AXuB4Gv8GOc6TrAEUP6akqQp6ISYc65f8rtt8okMYFevLHpFGL0tdVotzIwRpDeHXI3kvEb68o34uAx_fF0FXpgRaBWDO5lJNKc-t42R7mmSetCgTUzQK2Dw-afKgHxPQ3R2D_9BD6V6ON81FAVEFCgdvH2MXZs8JPRoJi1CZDDBtGkP49pPo6wwXJ7gp51TeFgrlSC529862N69lpJ21gBApehwpYIfWIM4M2OQMsTxAms9EPdYQAeLvQZ5Wc2pBn9RlFPaRGVaiHz7S"
          alt="MANDALA Logo" class="h-10 w-auto object-contain">
      </div>

      <div class="flex items-center gap-4">

        <a href=" {{ route('login') }}">
          <button
            class="px-6 py-2 border border-border-light rounded-full text-text-secondary font-body-main hover:bg-hover-neutral transition-all">Masuk</button>
        </a>
      </div>
    </nav>
  </header>
  <main class="relative pt-32 pb-section-gap overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute inset-0 hex-pattern pointer-events-none"></div>
    <div class="absolute inset-0 radial-glow pointer-events-none"></div>
    <!-- Subtle Vector Lines -->
    <svg class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none" preserveAspectRatio="none"
      viewBox="0 0 100 100">
      <path d="M0 20 L20 25 L40 15 L60 30 L80 10 L100 25" fill="none" stroke="#10B981" stroke-width="0.1"></path>
      <path d="M0 80 L30 70 L50 85 L80 60 L100 75" fill="none" stroke="#10B981" stroke-width="0.1"></path>
    </svg>
    <div class="max-w-7xl mx-auto px-grid-margin grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
      <!-- Hero Left -->
      <div class="lg:col-span-6 space-y-8">
        <div
          class="inline-flex items-center px-4 py-1.5 bg-badge-bg text-badge-text rounded-full border border-accent-mint/30 shadow-sm">
          <span class="font-subheading-label text-[10pt] uppercase tracking-wider font-semibold">Manajemen Diklat Akademik & Layanan Administrasi</span>
        </div>
        <h1 class="font-display-hero text-display-hero text-on-surface tracking-tight">
          <span class="font-display-hero text-on-surface font-bold">MANDALA</span>
        </h1>
        <p class="font-body-main text-text-secondary text-lg max-w-lg leading-relaxed">
          Kelola pengguna dan aset laboratorium dalam satu dashboard yang cepat dan mudah. Singkronisasi data dan
          manajemen administrasi dengan cepat.
        </p>
        <div class="flex flex-wrap gap-4 pt-4">
          <a href=" {{ route('login') }}">
            <button
              class="px-8 py-4 bg-primary-container text-white font-semibold rounded-full shadow-lg shadow-primary-container/20 hover:scale-[1.02] active:scale-95 transition-all">
              Mulai Masuk
            </button>
          </a>
          <button
            class="px-8 py-4 bg-white border border-border-light text-text-secondary font-semibold rounded-full hover:bg-hover-neutral transition-all">
            Lihat Ringkasan
          </button>
        </div>
      </div>
      <!-- Information Panel Right -->
      <div class="lg:col-span-6">
        <div
          class="bg-surface-secondary p-panel-padding rounded-[16px] shadow-xl shadow-on-surface/5 border border-white relative overflow-hidden">
          <!-- Stats Grid -->
          <div class="grid grid-cols-2 gap-grid-gutter mb-8"><!-- Card 1 -->
            <div
              class="bg-white p-card-padding rounded-xl shadow-sm border border-border-light hover:translate-y-[-2px] transition-transform"
              style="border-color: rgb(226, 232, 240);">
              <div class="font-headline-stat text-headline-stat text-[#0F172A]">24</div>
              <div class="font-label-tiny text-[#475569] uppercase tracking-widest mt-1">Laboratorium</div>
            </div>
            <!-- Card 2 -->
            <div
              class="bg-white p-card-padding rounded-xl shadow-sm border border-border-light hover:translate-y-[-2px] transition-transform">
              <div class="font-headline-stat text-headline-stat text-[#0F172A]">15</div>
              <div class="font-label-tiny text-[#475569] uppercase tracking-widest mt-1"><span
                  style="letter-spacing: 1.6px;" class="">Aset dikelola</span></div>
            </div>
            <!-- Card 3 -->
            <div
              class="bg-white p-card-padding rounded-xl shadow-sm border border-border-light hover:translate-y-[-2px] transition-transform"
              style="border-color: rgb(226, 232, 240);">
              <div class="font-headline-stat text-headline-stat text-[#0F172A]">8</div>
              <div class="font-label-tiny text-[#475569] uppercase tracking-widest mt-1">Laporan Administrasi</div>
            </div>
            <!-- Card 4 -->
            <div
              class="bg-white p-card-padding rounded-xl shadow-sm border border-border-light hover:translate-y-[-2px] transition-transform"
              style="border-color: rgb(226, 232, 240);">
              <div class="font-headline-stat text-headline-stat text-[#0F172A]">856</div>
              <div class="font-label-tiny text-[#475569] uppercase tracking-widest mt-1">PENGGUNA TERDAFTAR</div>
            </div>
            <!-- Card 5 -->

            <!-- Card 6 -->
          </div>
          <!-- Operational Focus Box -->
          <div class="bg-focus-surface p-card-padding rounded-xl border border-accent-mint/30">
            <div class="flex items-center gap-2 mb-2">
              <span class="material-symbols-outlined text-focus-text">analytics</span>
              <h3 class="font-subheading-label text-focus-text font-bold">Aplikasi Terintegrasi</h3>
              <img
                src="https://lh3.googleusercontent.com/aida/AP1WRLsvtUCbIkolnDhnADXKxVX25Cxswu8a21zKntPOFOCrs-lyLN1Tgrrqpk3Z5O4SKq8e25plOY4bOkSS02MEu2idqtsna23JrYiO7O-vDKoMfKMFP-59FnZwWwLV0QLFHTun8_5NuteT7mlv-zvCzqwQZbrYylZEbbPE4_OS1gzb2H2RaGS-86EBMX9uqJEltFylcZkIK3OAZNZe_tYMDq2hz8o2IqW4K1znZe67eaa6sUhVvwyYcBKJnVuso8ZikqUTj_YSNt3mmQ"
                alt="PRISMA Logo" class="h-5 w-auto ml-auto grayscale opacity-80">
            </div>
            <p class="font-body-main text-text-secondary text-sm leading-relaxed">MANDALA terhubung dengan PRISMA untuk
              sinkronisasi data pengguna, layanan, organisasi, dan autentikasi secara aman.</p>
          </div>
          <!-- Subtle Decorative Glow in Panel -->
          <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-primary-container/10 blur-3xl rounded-full"></div>
        </div>
      </div>
    </div>
  </main>
  <!-- Content Sections for visual balance -->

  <div class="absolute inset-0 -z-10">
    <img src="{{ asset('background.png') }}" alt="Background MANDALA" class="h-full w-full object-cover">
  </div>

  <!-- Footer Section -->
  <footer class="w-full bg-surface-secondary border-t border-border-light mt-auto py-4">
    <div class="flex flex-col md:flex-row justify-between items-center px-grid-margin max-w-7xl mx-auto gap-8">
      <div class="flex flex-col gap-2 items-center md:items-start">
        <span class="font-display-hero text-subheading-label text-on-surface font-bold">MANDALA</span>
        <p class="font-label-tiny text-text-secondary text-center md:text-left">
          © 2026 zetware.id</p>
      </div>
      <div class="flex gap-8">
        <a class="font-label-tiny text-text-secondary hover:text-primary transition-all" href="#">Privasi</a>
        <a class="font-label-tiny text-text-secondary hover:text-primary transition-all" href="#">Syarat &amp;
          Ketentuan</a>
        <a class="font-label-tiny text-text-secondary hover:text-primary transition-all" href="#">Pusat
          Bantuan</a>
      </div>
    </div>
  </footer>
  <script>
    // Micro-interaction for hover effects on stat cards
    document.querySelectorAll('.bg-white.p-card-padding').forEach(card => {
      card.addEventListener('mouseenter', () => {
        card.style.borderColor = '#10B981';
      });
      card.addEventListener('mouseleave', () => {
        card.style.borderColor = '#E2E8F0';
      });
    });

    // Simple scroll effect for header
    window.addEventListener('scroll', () => {
      const header = document.querySelector('header');
      if (window.scrollY > 20) {
        header.classList.add('shadow-sm');
        header.classList.replace('bg-white/70', 'bg-white/90');
      } else {
        header.classList.remove('shadow-sm');
        header.classList.replace('bg-white/90', 'bg-white/70');
      }
    });
  </script>

</body>

</html>
