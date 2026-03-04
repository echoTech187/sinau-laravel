@php
    /** @var \App\Livewire\Pages\Locations\Index $this */
@endphp
<div class="relative min-h-full">
    <!-- Decorative Background Blob -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-125 h-125 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-0 w-125 h-125 bg-purple-500/10 dark:bg-purple-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8">
        <div class="animate-fade-in-up">
            <x-page-header title="Master Titik Lokasi"
                description="Manajemen titik pemberhentian, agen, pool, dan checkpoint operasional."
                icon="heroicon-o-map-pin" iconGradient="from-indigo-500 to-blue-600" iconShadow="shadow-indigo-500/20">
                <a wire:navigate href="{{ route('locations.create') }}"
                    class="btn btn-sm sm:btn-md bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/30 rounded-2xl transition-all hover:-translate-y-0.5 font-bold">
                    <x-heroicon-o-plus class="w-5 h-5" />
                    Tambah Lokasi
                </a>
            </x-page-header>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-up"
            style="animation-delay: 0.1s">
            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-zinc-500 dark:text-zinc-400">
                    <x-heroicon-o-queue-list class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Total Titik</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['total'] }}</p>
                </div>
            </div>

            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl text-emerald-500">
                    <x-heroicon-o-map class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Cakupan Kota</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['cities'] }}</p>
                </div>
            </div>

            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-amber-50 dark:bg-amber-500/10 rounded-xl text-amber-500">
                    <x-heroicon-o-wrench-screwdriver class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Fasilitas Bengkel</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['with_maintenance'] }}
                    </p>
                </div>
            </div>

            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl text-indigo-500">
                    <x-heroicon-o-map-pin class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Provinsi</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ count($this->provinces) }}</p>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm animate-fade-in-up"
            style="animation-delay: 0.2s">

            <!-- Filter & Search Row -->
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between xl:justify-end mb-6">
                <!-- Filters -->
                <div
                    class="flex gap-2 p-1 bg-zinc-100/80 dark:bg-zinc-800/80 backdrop-blur rounded-xl border border-zinc-200/50 dark:border-zinc-700/50 w-full md:w-auto overflow-x-auto">
                    <select wire:model.live="provinceFilter"
                        class="select select-sm border-0 bg-white dark:bg-zinc-800 focus:outline-none focus:ring-0 text-zinc-600 dark:text-zinc-300 font-medium whitespace-nowrap">
                        <option value="">Semua Provinsi</option>
                        @foreach ($this->provinces as $prov)
                            <option value="{{ $prov }}">{{ $prov }}</option>
                        @endforeach
                    </select>
                    <div class="w-px h-6 bg-zinc-300 dark:bg-zinc-700 my-auto"></div>
                    <select wire:model.live="maintenance"
                        class="select select-sm border-0 bg-white dark:bg-zinc-800 focus:outline-none focus:ring-0 text-zinc-600 dark:text-zinc-300 font-medium whitespace-nowrap">
                        <option value="">Semua Fasilitas</option>
                        <option value="1">Dengan Bengkel</option>
                        <option value="0">Tanpa Bengkel</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="relative w-full md:w-80 group shrink-0">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <x-heroicon-o-magnifying-glass
                            class="w-4 h-4 text-zinc-400 group-focus-within:text-indigo-500 transition-colors" />
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama lokasi, kota..."
                        class="input input-sm w-full pl-11 bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20" />
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="table table-sm w-full">
                    <thead>
                        <tr
                            class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider text-[10px]">
                            <th class="py-4 rounded-tl-xl pl-4">Nama Lokasi</th>
                            <th class="py-4">Kota &amp; Provinsi</th>
                            <th class="py-4">Alamat</th>
                            <th class="py-4">Kategori / Peran</th>
                            <th class="py-4 text-center">Fasilitas</th>
                            <th class="py-4 rounded-tr-xl text-right pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @if (count($this->locations) > 0)
                            @foreach ($this->locations as $location)
                                <tr wire:key="location-{{ $location->id }}"
                                    class="border-b border-zinc-100 dark:border-zinc-800/50 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors group">
                                    <td class="py-3 pl-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600 transition-colors">{{ $location->name }}</span>
                                            <span
                                                class="text-[10px] text-zinc-400 font-mono mt-0.5 tracking-widest">{{ $location->qr_code_gate ?? 'NO-GATE-QR' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="flex flex-col">
                                            <span
                                                class="font-medium text-zinc-800 dark:text-zinc-200">{{ $location->city }}</span>
                                            <span
                                                class="text-[10px] text-zinc-500 mt-0.5">{{ $location->province }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 max-w-xs truncate text-[11px] text-zinc-500 leading-relaxed italic">
                                        {{ $location->address }}
                                    </td>
                                    <td class="py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse ($location->roles as $role)
                                                <span wire:key="loc-role-{{ $location->id }}-{{ $role->id }}"
                                                    class="px-2 py-0.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-[9px] font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-tighter">
                                                    {{ $role->name }}
                                                </span>
                                            @empty
                                                <span class="text-[10px] text-zinc-400">Umum</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        @if ($location->has_maintenance_facility)
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">
                                                <div class="size-1.5 rounded-full bg-amber-500"></div>
                                                Bengkel
                                            </span>
                                        @else
                                            <span class="text-[10px] text-zinc-400">Standar</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right pr-4">
                                        <div class="flex items-center justify-end gap-1">
                                            <a wire:navigate href="{{ route('locations.edit', $location->id) }}"
                                                class="btn btn-ghost btn-xs btn-square hover:bg-zinc-100 dark:hover:bg-zinc-800 text-indigo-500 rounded-lg inline-flex items-center justify-center"
                                                title="Edit Data">
                                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                                            </a>
                                            <button wire:click="confirmDeleteLocation({{ $location->id }})"
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
                                    <x-heroicon-o-map-pin class="w-12 h-12 mx-auto mb-3 opacity-20" />
                                    <p class="font-medium text-lg">Tidak ada data lokasi ditemukan</p>
                                    <p class="text-xs mt-1">Ganti filter pencarian atau tambahkan lokasi baru.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $this->locations->links() }}
            </div>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal {{ $confirmingLocationDeletion ? 'modal-open' : '' }}" role="dialog">
        <div
            class="modal-box max-w-sm bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-2xl">
            <h3 class="font-bold text-lg text-red-600 flex items-center gap-2">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                Hapus Titik Lokasi?
            </h3>
            <p class="py-4 text-zinc-600 dark:text-zinc-400 text-sm leading-relaxed">
                Apakah Anda yakin ingin menghapus lokasi ini? Data yang terhubung dengan rute dan jadwal mungkin akan
                terpengaruh.
            </p>
            <div class="modal-action">
                <button wire:click="$set('confirmingLocationDeletion', false)"
                    class="btn btn-sm btn-ghost">Batal</button>
                <button wire:click="deleteLocation"
                    class="btn btn-sm bg-red-600 hover:bg-red-700 text-white border-0 shadow-lg shadow-red-600/30">
                    <span wire:loading.remove wire:target="deleteLocation">Ya, Hapus!</span>
                    <span wire:loading wire:target="deleteLocation"
                        class="loading loading-spinner loading-xs hidden"></span>
                </button>
            </div>
        </div>
        <div class="modal-backdrop bg-zinc-900/40 backdrop-blur-sm"
            wire:click="$set('confirmingLocationDeletion', false)"></div>
    </div>
</div>
