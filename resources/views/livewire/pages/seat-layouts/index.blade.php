@php /** @var \App\Livewire\Pages\SeatLayouts\Index $this */ @endphp
<div class="container relative min-h-screen pb-20">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-125 h-125 bg-purple-500/5 rounded-full blur-[100px]"></div>
    </div>

    <div class="relative z-10 space-y-8">
        <header
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-6 animate-fade-in-up">
            <div>
                <h1
                    class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3 text-pretty">
                    <div
                        class="p-2.5 rounded-2xl bg-linear-to-br from-purple-500 to-indigo-600 shadow-lg shadow-purple-500/20 text-white">
                        <x-heroicon-o-squares-2x2 class="w-6 h-6" />
                    </div>
                    Konfigurasi Layout Kursi
                </h1>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Pengaturan denah kursi bus untuk berbagai jenis
                    karoseri dan kelas layanan.</p>
            </div>
            <div class="flex items-center gap-3">
                <a wire:navigate href="{{ route('seat-layouts.create') }}"
                    class="btn btn-md bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/20 rounded-2xl transition-all hover:-translate-y-0.5 font-bold">
                    <x-heroicon-o-plus class="w-5 h-5" />
                    Buat Layout Baru
                </a>
            </div>
        </header>

        <div class="relative w-full md:w-96 animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-zinc-400">
                <x-heroicon-o-magnifying-glass class="w-4 h-4" />
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama layout..."
                class="input input-sm w-full pl-11 bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 shadow-sm" />
        </div>

        <!-- Grid Layout Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 animate-fade-in-up"
            style="animation-delay: 0.2s">
            @if (count($this->seatLayouts) > 0)
                @foreach ($this->seatLayouts as $layout)
                    <div wire:key="layout-{{ $layout->id }}"
                        class="group bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm hover:shadow-xl hover:shadow-indigo-500/10 transition-all hover:-translate-y-1 overflow-hidden relative">
                        <div class="flex flex-col h-full">
                            <div class="flex items-start justify-between mb-4">
                                <div
                                    class="p-2.5 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 group-hover:bg-indigo-500 group-hover:text-white transition-all">
                                    <x-heroicon-o-view-columns class="w-6 h-6" />
                                </div>
                                <div class="flex flex-col items-end">
                                    <span
                                        class="text-[10px] font-black uppercase text-zinc-400 tracking-widest leading-none">Total
                                        Kursi</span>
                                    <span
                                        class="text-xl font-black text-indigo-600 dark:text-indigo-400 mt-1 leading-none">
                                        {{ collect($layout->layout_mapping)->where('type', 'seat')->count() }}
                                    </span>
                                </div>
                            </div>

                            <h3
                                class="text-lg font-black text-zinc-800 dark:text-zinc-200 group-hover:text-indigo-600 transition-colors uppercase tracking-tight">
                                {{ $layout->name }}</h3>
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[.2em] mt-1">
                                {{ $layout->grid_rows }} Rows × {{ $layout->grid_columns }} Columns</p>

                            <!-- Mini Preview (Simulated) -->
                            <div
                                class="mt-6 flex flex-wrap gap-1 justify-center p-3 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-100 dark:border-zinc-800 overflow-hidden max-h-24">
                                @foreach (array_slice($layout->layout_mapping, 0, 15) as $seat)
                                    <div
                                        class="size-3 rounded shadow-sm {{ $seat['type'] === 'seat' ? 'bg-indigo-500' : 'bg-zinc-200 dark:bg-zinc-800' }}">
                                    </div>
                                @endforeach
                                @if (count($layout->layout_mapping) > 15)
                                    <div
                                        class="size-3 flex items-center justify-center text-[7px] font-black text-zinc-400">
                                        +</div>
                                @endif
                            </div>

                            <div class="mt-auto pt-6 flex items-center justify-between">
                                <span class="text-[10px] font-bold text-zinc-500 uppercase flex items-center gap-1.5">
                                    <x-heroicon-o-truck class="w-3.5 h-3.5 opacity-40 text-pretty" />
                                    {{ $layout->buses_count }} Unit Armada
                                </span>
                                <div
                                    class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a wire:navigate href="{{ route('seat-layouts.edit', $layout->id) }}"
                                        class="btn btn-ghost btn-square btn-xs rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-indigo-500">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </a>
                                    <button wire:click="confirmDeleteLayout({{ $layout->id }})"
                                        class="btn btn-ghost btn-square btn-xs rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-red-500">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-span-full py-20 text-center text-zinc-500">
                    <x-heroicon-o-squares-2x2 class="w-12 h-12 mx-auto mb-3 opacity-20" />
                    <p class="font-medium text-lg">Layout belum tersedia</p>
                    <p class="text-sm">Silakan buat konfigurasi layout kursi baru.</p>
                </div>
            @endif
        </div>

        @if ($this->seatLayouts->hasPages())
            <div class="mt-8">
                {{ $this->seatLayouts->links() }}
            </div>
        @endif
    </div>

    <div class="modal {{ $confirmingLayoutDeletion ? 'modal-open' : '' }}" role="dialog">
        <div
            class="modal-box max-w-sm bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-8">
            <h3 class="font-bold text-lg text-red-600">Hapus Layout Kursi?</h3>
            <p class="py-4 text-zinc-600 dark:text-zinc-400 text-sm">Tindakan ini permanen. Jika layout sudah digunakan
                armada, penghapusan mungkin gagal.</p>
            <div class="modal-action">
                <button wire:click="$set('confirmingLayoutDeletion', false)"
                    class="btn btn-ghost uppercase font-bold tracking-tight">Batal</button>
                <button wire:click="deleteLayout"
                    class="btn bg-red-600 hover:bg-red-700 text-white border-0 shadow-lg shadow-red-600/30 rounded-xl px-6 font-black uppercase tracking-widest text-[10px]">Ya,
                    Hapus!</button>
            </div>
        </div>
        <div class="modal-backdrop bg-zinc-900/60" wire:click="$set('confirmingLayoutDeletion', false)"></div>
    </div>
</div>
