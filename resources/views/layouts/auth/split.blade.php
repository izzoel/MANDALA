<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  @include('partials.head')
</head>

<body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-900 dark:text-zinc-100 relative">
  <!-- Theme Toggle Button -->
  <div class="absolute top-4 right-4 z-50">
    <flux:button x-data x-on:click="$flux.appearance = $flux.appearance === 'dark' ? 'light' : 'dark'" variant="subtle" size="sm" class="rounded-full !size-9 p-0 flex items-center justify-center cursor-pointer text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white" title="Ganti Tema">
      <flux:icon name="moon" class="size-4 hidden dark:block" />
      <flux:icon name="sun" class="size-4 block dark:hidden" />
    </flux:button>
  </div>

  <div
    class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
    <div class="relative hidden h-full flex-col p-10 text-white lg:flex border-e border-zinc-200 bg-zinc-900 dark:border-zinc-800">
      <div class="absolute inset-0 bg-gradient-to-br from-zinc-900 via-zinc-950 to-zinc-900"></div>
      <a href="{{ route('landing') }}" class="relative z-20 flex items-center gap-3 text-lg font-bold" wire:navigate>
        <x-app-logo-icon class="size-9 drop-shadow-md" />
        {{ config('app.name', 'MANDALA') }}
      </a>

      @php
        [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
      @endphp

      <div class="relative z-20 mt-auto">
        <blockquote class="space-y-2">
          <flux:heading size="lg">&ldquo;{{ trim($message) }}&rdquo;</flux:heading>
          <footer>
            <flux:heading>{{ trim($author) }}</flux:heading>
          </footer>
        </blockquote>
      </div>
    </div>
    <div class="w-full lg:p-8">
      <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
        <a href="{{ route('landing') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden"
          wire:navigate>
          <x-app-logo-icon class="size-12 drop-shadow-md" />
          <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
        </a>
        {{ $slot }}
      </div>
    </div>
  </div>

  @persist('toast')
    <flux:toast.group>
      <flux:toast />
    </flux:toast.group>
  @endpersist

  @fluxScripts
</body>

</html>
