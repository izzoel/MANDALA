<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  @include('partials.head')
</head>

<body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-900 dark:text-zinc-100 relative">
  <!-- Theme Toggle Button -->
  <div class="absolute top-4 right-4 z-50">
    <flux:button x-data x-on:click="$flux.appearance = $flux.appearance === 'dark' ? 'light' : 'dark'" variant="subtle" size="sm" class="rounded-full !size-9 p-0 flex items-center justify-center cursor-pointer text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white" title="Ganti Tema">
      <flux:icon name="moon" class="size-4 hidden dark:block" />
      <flux:icon name="sun" class="size-4 block dark:hidden" />
    </flux:button>
  </div>

  <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
    <div class="flex w-full max-w-sm flex-col gap-2">
      <a href="{{ route('landing') }}" class="flex flex-col items-center gap-2 font-medium group" wire:navigate>
        <span class="flex h-12 w-12 mb-1 items-center justify-center rounded-md group-hover:scale-105 transition-transform duration-200">
          <x-app-logo-icon class="size-12 drop-shadow-md" />
        </span>
        <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
      </a>
      <div class="flex flex-col gap-6">
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
