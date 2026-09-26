<div class="space-y-8">
  <section class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
    <div class="relative">
      <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
        placeholder="{{ __('Cari berdasarkan nama, gedung, atau laboran...') }}" />
    </div>

    <div class="grid gap-7 md:grid-cols-2 xl:grid-cols-3 mt-8">
      @forelse ($this->integrasis as $aplikasi)

        <article wire:key="aplikasi-{{ $aplikasi->id }}"
          @if ($editingIntegrasiId === $aplikasi->id) wire:click.outside="saveIntegrasi({{ $aplikasi->id }})" @endif
          @if ($editingIntegrasiId !== $aplikasi->id) @click="window.open('{{ route('sso.ticket', ['nama' => strtolower($aplikasi->nama)]) }}', '_blank')" @endif
          class="group overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl dark:border-zinc-700 dark:bg-zinc-900 {{ $editingIntegrasiId !== $aplikasi->id ? 'cursor-pointer' : '' }}">
          <div class="relative h-44 overflow-hidden rounded-t-3xl">

            <div class="absolute inset-0 bg-cover bg-center"
              style="background-image:linear-gradient(180deg, rgba(8, 47, 73, .18), rgba(8, 47, 73, .62)), url('{{ asset('storage/' . $aplikasi->gambar) }}');">
            </div>

            <div
              class="absolute inset-0 opacity-25 bg-[linear-gradient(90deg,rgba(255,255,255,.22)_1px,transparent_1px),linear-gradient(rgba(255,255,255,.18)_1px,transparent_1px)] bg-size-[28px_28px]">
            </div>

            <div class="absolute right-4 top-4">
              @if ($editingIntegrasiId === $aplikasi->id)
                <flux:select wire:model.live="editFakultas.{{ $aplikasi->id }}" variant="listbox" size="xs"
                  class="w-36">

                  @foreach ($this->fakultas as $fakultas)
                    @if ($fakultas !== 'Semua')
                      <flux:select.option value="{{ $fakultas }}">
                        {{ $fakultas }}
                      </flux:select.option>
                    @endif
                  @endforeach

                </flux:select>
              @else
                <span
                  class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1.5 text-[7px] font-bold uppercase tracking-wide text-zinc-700 shadow-sm">
                  <span class="size-2 rounded-full bg-emerald-500"></span>
                  {{ $aplikasi->fakultas }}
                </span>
              @endif
            </div>

            <div class="absolute bottom-4 right-4" x-data>
              @if ($editingIntegrasiId === $aplikasi->id)
                <input x-ref="upload" wire:model.live="editGambar.{{ $aplikasi->id }}" type="file" accept="image/*"
                  class="hidden">

                <flux:button icon="pencil-square" size="xs" x-on:click="$refs.upload.click()" />
              @endif
            </div>

          </div>
          {{-- <flux:link href="{{ route('sso.ticket', ['nama' => strtolower($aplikasi->nama)]) }}">
            {{ $aplikasi->nama }}
          </flux:link> --}}
          <div class="p-5">
            @if ($editingIntegrasiId === $aplikasi->id)
              <input wire:model.live="editNama.{{ $aplikasi->id }}"
                wire:keydown.enter="saveIntegrasi({{ $aplikasi->id }})" type="text" x-data x-init="$nextTick(() => $el.focus())"
                class="mb-3 inline-flex w-full rounded-md border border-sky-200 bg-sky-50 px-2 py-1 text-md font-bold uppercase tracking-wide text-zinc-900 outline-none ring-2 ring-sky-100 transition focus:border-sky-400 dark:border-sky-800 dark:bg-sky-400/10 dark:text-sky-300 dark:ring-sky-950" />
            @else
              <span
                class="mb-4 inline-flex rounded-md bg-sky-50 px-1 py-1 text-sm font-bold uppercase tracking-wide dark:bg-sky-400/10 dark:text-sky-300">
                {{ $aplikasi->nama }}
              </span>
            @endif
            <div>
              @if ($editingIntegrasiId === $aplikasi->id)
                <flux:textarea wire:model.live="editDeskripsi.{{ $aplikasi->id }}" rows="6" resize="none"
                  class="pb-0" />
              @else
                {{ $aplikasi->deskripsi ?? __('Tidak ada deskripsi.') }}
              @endif

            </div>

            {{-- <div class="mt-10">
              <div class="flex items-center gap-1.5 text-sm text-zinc-600 dark:text-zinc-400">
                <flux:icon.map-pin class="size-4" />
                @if ($editingIntegrasiId === $aplikasi->id)
                  <flux:select wire:model.live="editGedung.{{ $aplikasi->id }}" variant="listbox" size="xs">
                    @foreach ($gedungOptions as $gedung)
                      @if ($gedung !== 'Semua')
                        <flux:select.option value="{{ $gedung }}">{{ $gedung }}</flux:select.option>
                      @endif
                    @endforeach
                  </flux:select>
                @else
                  <span>{{ $aplikasi->gedung }}</span>
                @endif
              </div>
            </div> --}}

            {{-- <div class="mt-2 border-t border-zinc-100 pt-4 dark:border-zinc-800">
              <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                  <div>
                    @if ($editingIntegrasiId === $aplikasi->id)
                      <flux:select wire:model.live="editLaboran.{{ $aplikasi->id }}" variant="listbox" size="xs"
                        searchable>
                        @foreach ($this->laborans as $laboran)
                          <flux:select.option value="{{ $laboran->id }}">{{ $laboran->nama }}</flux:select.option>
                        @endforeach
                      </flux:select>
                    @else
                      <div class="text-sm font-bold text-zinc-900 dark:text-white">
                        {{ $aplikasi->laboran->nama }}
                      </div>
                    @endif
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Laboran') }}</div>
                  </div>
                </div>

                <button wire:click="editIntegrasi({{ $aplikasi->id }})" type="button"
                  aria-label="{{ __('Pengaturan aplikasi') }}" class="inline-flex text-current">
                  <flux:icon.cog-6-tooth />
                </button>

              </div>
            </div> --}}
          </div>
        </article>

      @empty
        <div
          class="col-span-full rounded-3xl border border-dashed border-zinc-300 bg-white p-10 text-center dark:border-zinc-700 dark:bg-zinc-900">
          <flux:icon.magnifying-glass class="mx-auto size-10 text-zinc-300 dark:text-zinc-600" />
          <div class="mt-3 text-sm font-semibold text-zinc-900 dark:text-white">
            {{ __('Tidak ada aplikasi ditemukan.') }}</div>
          <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('Coba ubah kata kunci atau filter fakultas.') }}</div>
        </div>
      @endforelse
    </div>

  </section>
</div>
