@php /** @var \App\Livewire\Pages\Routes\Index $this */ @endphp
<div class="container relative min-h-screen pb-10">
    <!-- Decorative Background Blob -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-150 h-150 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-1/4 w-125 h-125 bg-purple-500/10 dark:bg-purple-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8">
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/50 pb-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-linear-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/30">
                            <x-heroicon-o-map class="w-6 h-6 text-white" />
                        </div>
                        Master Rute & Persinggahan
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Manajemen jalur perjalanan operasional bus, terminal asal/tujuan, dan rute persinggahan
                        (checkpoint).
                    </p>
                </div>
                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <button
                        class="btn btn-sm bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl transition-all">
                        <x-heroicon-o-arrow-path class="w-4 h-4" />
                        Sinkronisasi Jarak
                    </button>
                    <a wire:navigate href="{{ route('routes.create') }}"
                        class="btn btn-sm bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/30 rounded-xl transition-all hover:-translate-y-0.5 font-bold">
                        <x-heroicon-o-plus class="w-4 h-4" />
                        Tambah Rute Baru
                    </a>
                </div>
            </header>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-up"
            style="animation-delay: 0.1s">
            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-zinc-500 dark:text-zinc-400">
                    <x-heroicon-o-map class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Total Rute</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['total'] }}</p>
                </div>
            </div>

            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl text-indigo-500">
                    <x-heroicon-o-arrows-right-left class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Rata-Rata Jarak</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">
                        {{ number_format($this->stats['avg_distance'], 0) }} <span class="text-sm">KM</span></p>
                </div>
            </div>
        </div>

        <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm animate-fade-in-up"
            style="animation-delay: 0.2s">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between mb-6">
                <!-- Data Filters -->
                <div
                    class="flex flex-wrap gap-2 p-1 bg-zinc-100/80 dark:bg-zinc-800/80 backdrop-blur rounded-xl border border-zinc-200/50 dark:border-zinc-700/50 w-full md:w-auto overflow-x-auto">
                    <select wire:model.live="originFilter"
                        class="select select-sm border-0 bg-transparent focus:outline-none focus:ring-0 text-zinc-600 dark:text-zinc-300 font-medium">
                        <option value="">Semua Terminal Asal</option>
                        @foreach ($this->terminals as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                    <div class="w-px h-6 bg-zinc-300 dark:bg-zinc-700 my-auto hidden sm:block"></div>
                    <select wire:model.live="destFilter"
                        class="select select-sm border-0 bg-transparent focus:outline-none focus:ring-0 text-zinc-600 dark:text-zinc-300 font-medium whitespace-nowrap">
                        <option value="">Semua Terminal Tujuan</option>
                        @foreach ($this->terminals as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search -->
                <div class="relative w-full xl:w-80 group shrink-0">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <x-heroicon-o-magnifying-glass
                            class="w-4 h-4 text-zinc-400 group-focus-within:text-indigo-500 transition-colors" />
                    </div>
                    <input type="text" placeholder="Cari Kode atau Nama Rute..."
                        wire:model.live.debounce.300ms="search"
                        class="input input-sm w-full pl-11 bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20" />
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="table table-sm w-full">
                    <thead>
                        <tr
                            class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider text-[10px]">
                            <th class="py-4 rounded-tl-xl pl-4">Rute Operasional</th>
                            <th class="py-4">Asal Keberangkatan</th>
                            <th class="py-4">Tujuan Akhir</th>
                            <th class="py-4 text-center">Titik Transit</th>
                            <th class="py-4 text-right">Jarak (KM)</th>
                            <th class="py-4 rounded-tr-xl text-right pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @if (count($this->routes) > 0)
                            @foreach ($this->routes as $route)
                                <tr wire:key="route-{{ $route->id }}"
                                    class="border-b border-zinc-100 dark:border-zinc-800/50 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                    <td class="py-4 pl-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800 flex items-center justify-center shrink-0">
                                                <x-heroicon-s-map
                                                    class="w-5 h-5 text-indigo-500 dark:text-indigo-400" />
                                            </div>
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-bold text-zinc-900 dark:text-white">{{ $route->name }}</span>
                                                <div class="flex mt-1">
                                                    <span
                                                        class="inline-block px-2 py-0.5 rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-[10px] font-mono font-bold text-zinc-500 tracking-widest shadow-sm">
                                                        {{ $route->route_code }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></div>
                                            <span class="font-medium text-zinc-700 dark:text-zinc-300">
                                                {{ optional($route->origin)->name ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-red-500 shrink-0"></div>
                                            <span class="font-medium text-zinc-700 dark:text-zinc-300">
                                                {{ optional($route->destination)->name ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="py-4 text-center">
                                        <span
                                            class="inline-flex items-center justify-center min-w-[32px] h-6 px-2 rounded-full bg-zinc-100 dark:bg-zinc-800 text-xs font-bold text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                            {{ $route->stops_count }} <span
                                                class="font-normal text-[10px] ml-1">Titik</span>
                                        </span>
                                    </td>

                                    <td class="py-4 text-right">
                                        <span class="text-xs font-bold text-zinc-900 dark:text-white">
                                            {{ number_format($route->distance_km ?? 0, 0, ',', '.') }} KM
                                        </span>
                                    </td>

                                    <td class="py-4 text-right pr-4">
                                        <div class="flex items-center justify-end gap-1">
                                            <a wire:navigate href="{{ route('routes.edit', $route->id) }}"
                                                class="btn btn-ghost btn-xs btn-square hover:bg-zinc-100 dark:hover:bg-zinc-800 text-indigo-500 rounded-lg inline-flex items-center justify-center"
                                                title="Kelola Titik Pemberhentian & Edit">
                                                <x-heroicon-o-adjustments-vertical class="w-4 h-4" />
                                            </a>
                                            <button wire:click="confirmDeleteRoute({{ $route->id }})"
                                                class="btn btn-ghost btn-xs btn-square hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-red-500 rounded-lg"
                                                title="Hapus">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="py-12 text-center text-zinc-500 dark:text-zinc-400">
                                    <x-heroicon-o-map class="w-12 h-12 mx-auto mb-3 opacity-20" />
                                    <p class="font-medium text-lg">Tidak ada data rute ditemukan</p>
                                    <p class="text-xs mt-1">Tambahkan jalur operasional trayek baru terlebih dahulu.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $this->routes->links() }}
            </div>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal {{ $confirmingRouteDeletion ? 'modal-open' : '' }}" role="dialog">
        <div
            class="modal-box bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-2xl">
            <h3 class="font-bold text-lg text-red-600 flex items-center gap-2">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                Konfirmasi Penghapusan Rute
            </h3>
            <p class="py-4 text-zinc-600 dark:text-zinc-400">
                Apakah Anda yakin ingin menghapus data rute ini? Peringatan: <b>Menghapus Rute utama akan otomatis
                    menghapus seluruh jalur titik-titik (stops) yang terafiliasi di dalamnya.</b>
            </p>
            <div class="modal-action">
                <button wire:click="$set('confirmingRouteDeletion', false)"
                    class="btn btn-sm btn-ghost">Batal</button>
                <button wire:click="deleteRoute"
                    class="btn btn-sm bg-red-600 hover:bg-red-700 text-white border-0 shadow-lg shadow-red-600/30">
                    <span wire:loading.remove wire:target="deleteRoute">Ya, Hapus Semua!</span>
                    <span wire:loading wire:target="deleteRoute"
                        class="loading loading-spinner loading-xs hidden"></span>
                </button>
            </div>
        </div>
        <div class="modal-backdrop bg-zinc-900/40 backdrop-blur-sm"
            wire:click="$set('confirmingRouteDeletion', false)"></div>
    </div>
</div>
