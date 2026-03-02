@php
    /** @var \App\Livewire\Pages\Locations\Index $this */
@endphp
<div class="relative min-h-full">
    <!-- Decorative Background Blob -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-125 h-125 bg-indigo-500/5 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 left-0 w-125 h-125 bg-purple-500/5 rounded-full blur-[100px]"></div>
    </div>

    <div class="relative z-10 space-y-6">
        <header
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/50 pb-4 animate-fade-in-up">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                    <div
                        class="p-2.5 rounded-2xl bg-linear-to-br from-indigo-500 to-blue-600 shadow-lg shadow-indigo-500/20 text-white">
                        <x-heroicon-o-map-pin class="w-6 h-6" />
                    </div>
                    Master Titik Lokasi
                </h1>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Manajemen titik pemberhentian, agen, pool, dan
                    checkpoint operasional.</p>
            </div>
            <!-- Action Buttons -->
            <div
                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 mt-4 sm:mt-0 w-full sm:w-auto">
                <a wire:navigate href="{{ route('locations.create') }}"
                    class="btn btn-sm bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/30 rounded-xl transition-all hover:-translate-y-0.5 font-bold">
                    <x-heroicon-o-plus class="w-4 h-4" />
                    Tambah Lokasi
                </a>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 animate-fade-in-up" style="animation-delay: 0.1s">
            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 flex items-center gap-4">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-indigo-500">
                    <x-heroicon-o-building-office class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Total Titik</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['total'] }}</p>
                </div>
            </div>
            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 flex items-center gap-4">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-emerald-500">
                    <x-heroicon-o-map class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Cakupan Kota</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['cities'] }}</p>
                </div>
            </div>
            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 flex items-center gap-4">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-amber-500">
                    <x-heroicon-o-wrench-screwdriver class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Fasilitas Bengkel</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['with_maintenance'] }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between animate-fade-in-up"
            style="animation-delay: 0.2s">
            <div class="relative w-full md:w-96 group">
                <div
                    class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-indigo-500 transition-colors">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama lokasi atau kota..."
                    class="input input-md w-full pl-12 bg-white/50 dark:bg-zinc-800/50 border-zinc-200 dark:border-zinc-700/50 rounded-2xl focus:ring-2 focus:ring-indigo-500/20" />
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl overflow-hidden shadow-sm animate-fade-in-up"
            style="animation-delay: 0.3s">
            <div class="overflow-x-auto">
                <table class="table table-sm w-full">
                    <thead>
                        <tr class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">Nama
                                Lokasi</th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">Kota &
                                Provinsi</th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">Alamat
                            </th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                                Kategori / Peran</th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                                Fasilitas</th>
                            <th
                                class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] text-right">
                                AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 text-sm">
                        @if (count($this->locations) > 0)
                            @foreach ($this->locations as $location)
                                <tr wire:key="location-{{ $location->id }}"
                                    class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors border-b border-zinc-100 dark:border-zinc-800/50 last:border-0 group">
                                    <td class="py-4 pl-6">
                                        <div class="flex flex-col">
                                            <span
                                                class="font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600 transition-colors">{{ $location->name }}</span>
                                            <span
                                                class="text-[10px] text-zinc-400 font-mono mt-0.5 tracking-widest">{{ $location->qr_code_gate ?? 'NO-GATE-QR' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="font-medium text-zinc-800 dark:text-zinc-200">{{ $location->city }}</span>
                                            <span
                                                class="text-[10px] text-zinc-500 mt-0.5">{{ $location->province }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 max-w-xs truncate text-[11px] text-zinc-500 leading-relaxed italic">
                                        {{ $location->address }}
                                    </td>
                                    <td class="py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @if (count($location->roles) > 0)
                                                @foreach ($location->roles as $role)
                                                    <span wire:key="loc-role-{{ $location->id }}-{{ $role->id }}"
                                                        class="px-2 py-0.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-[9px] font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-tighter">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-[10px] text-zinc-400">Umum</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        @if ($location->has_maintenance_facility)
                                            <div
                                                class="flex items-center gap-1.5 text-amber-600 dark:text-amber-400 text-[10px] font-bold uppercase tracking-tight">
                                                <div class="size-1.5 rounded-full bg-amber-500"></div>
                                                Maintenance Ready
                                            </div>
                                        @else
                                            <span class="text-[10px] text-zinc-400">Standar</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
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
                                <td colspan="6" class="py-20 text-center text-zinc-500">
                                    <div
                                        class="p-4 rounded-full bg-zinc-50 dark:bg-zinc-900/50 size-16 flex items-center justify-center mx-auto mb-4 border border-zinc-100 dark:border-zinc-800">
                                        <x-heroicon-o-map-pin class="w-8 h-8 opacity-20" />
                                    </div>
                                    <p class="font-medium text-lg text-zinc-800 dark:text-white">Belum Ada Titik Lokasi
                                    </p>
                                    <p class="text-sm mt-1">Ganti filter atau tambahkan lokasi operasional baru.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if ($this->locations->hasPages())
                <div class="p-6 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/10">
                    {{ $this->locations->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal {{ $confirmingLocationDeletion ? 'modal-open' : '' }}" role="dialog">
        <div
            class="modal-box bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-2xl">
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
                    class="btn btn-sm btn-ghost lowercase font-bold tracking-tight">Batal</button>
                <button wire:click="deleteLocation"
                    class="btn btn-sm bg-red-600 hover:bg-red-700 text-white border-0 shadow-lg shadow-red-600/30 rounded-xl px-6 font-black uppercase tracking-widest text-[10px]">
                    Ya, Hapus!
                </button>
            </div>
        </div>
        <div class="modal-backdrop bg-zinc-900/60 backdrop-blur-sm"
            wire:click="$set('confirmingLocationDeletion', false)"></div>
    </div>
</div>
