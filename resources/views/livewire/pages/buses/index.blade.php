@php
    /** @var \App\Livewire\Pages\Buses\Index $this */
@endphp
<div class="relative min-h-full">
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
                            class="p-2.5 rounded-2xl bg-linear-to-br from-indigo-500 to-blue-600 shadow-lg shadow-indigo-500/30">
                            <x-heroicon-o-truck class="w-6 h-6 text-white" />
                        </div>
                        Master Armada Bus
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Manajemen data fisik bus, nomor lambung, spesifikasi sasis, dan status kelayakan jalan
                        (KIR/STNK).
                    </p>
                </div>
                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <button
                        class="btn btn-sm bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl transition-all">
                        <x-heroicon-o-arrow-path class="w-4 h-4" />
                        Synchronize
                    </button>
                    <a wire:navigate href="{{ route('buses.create') }}"
                        class="btn btn-sm bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/30 rounded-xl transition-all hover:-translate-y-0.5 font-bold">
                        <x-heroicon-o-plus class="w-4 h-4" />
                        Tambah Bus Baru
                    </a>
                </div>
            </header>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-up"
            style="animation-delay: 0.1s">
            <!-- Stats Cards with Glassmorphism -->
            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-zinc-500 dark:text-zinc-400">
                    <x-heroicon-o-sparkles class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Total Armada</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['total'] }}</p>
                </div>
            </div>

            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-green-50 dark:bg-green-500/10 rounded-xl text-green-500">
                    <x-heroicon-o-check-circle class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Aktif & Siap Jalan</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['active'] }}</p>
                </div>
            </div>

            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-amber-50 dark:bg-amber-500/10 rounded-xl text-amber-500">
                    <x-heroicon-o-wrench-screwdriver class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Maintenance (P2H)</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['maintenance'] }}</p>
                </div>
            </div>

            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-red-50 dark:bg-red-500/10 rounded-xl text-red-500">
                    <x-heroicon-o-no-symbol class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Nonaktif / Rusak</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['inactive'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm animate-fade-in-up"
            style="animation-delay: 0.2s">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between xl:justify-end mb-6">
                <!-- Data Filters -->
                <div
                    class="flex gap-2 p-1 bg-zinc-100/80 dark:bg-zinc-800/80 backdrop-blur rounded-xl border border-zinc-200/50 dark:border-zinc-700/50 w-full md:w-auto overflow-x-auto">
                    <select wire:model.live="classFilter"
                        class="select select-sm border-0 bg-transparent focus:outline-none focus:ring-0 text-zinc-600 dark:text-zinc-300 font-medium">
                        <option value="">Semua Kelas</option>
                        @foreach ($this->busClasses as $bc)
                            <option value="{{ $bc->id }}">{{ $bc->name }}</option>
                        @endforeach
                    </select>
                    <div class="w-px h-6 bg-zinc-300 dark:bg-zinc-700 my-auto"></div>
                    <select wire:model.live="statusFilter"
                        class="select select-sm border-0 bg-transparent focus:outline-none focus:ring-0 text-zinc-600 dark:text-zinc-300 font-medium">
                        <option value="">Semua Status</option>
                        <option value="active">Active</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="relative w-full md:w-80 group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <x-heroicon-o-magnifying-glass
                            class="w-4 h-4 text-zinc-400 group-focus-within:text-indigo-500 transition-colors" />
                    </div>
                    <input type="text" placeholder="Cari Nopol, Lambung, Nama..."
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
                            <th class="py-4 rounded-tl-xl pl-4">No. Lambung</th>
                            <th class="py-4">Info Kendaraan</th>
                            <th class="py-4">Kelas & Kapasitas</th>
                            <th class="py-4">Legalitas (KIR/STNK)</th>
                            <th class="py-4">Status</th>
                            <th class="py-4 rounded-tr-xl text-right pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @if (count($this->buses) > 0)
                            @foreach ($this->buses as $bus)
                                <tr wire:key="bus-{{ $bus->id }}"
                                    class="border-b border-zinc-100 dark:border-zinc-800/50 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                    <!-- Lambung -->
                                    <td class="py-3 pl-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="font-bold text-zinc-900 dark:text-white">{{ $bus->fleet_code }}</span>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span
                                                    class="inline-block px-1.5 py-0.5 rounded border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-[10px] font-mono font-bold text-zinc-600 dark:text-zinc-400 tracking-widest">
                                                    {{ $bus->plate_number }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Kendaraan -->
                                    <td class="py-3">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="size-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0 border border-zinc-200 dark:border-zinc-700">
                                                <x-heroicon-o-truck class="w-4 h-4 text-zinc-500" />
                                            </div>
                                            <div>
                                                <p class="font-bold text-zinc-900 dark:text-white text-xs">
                                                    {{ $bus->name ?? 'Tanpa Nama' }}</p>
                                                <p class="text-[10px] text-zinc-500 mt-0.5">{{ $bus->chassis_brand }}
                                                    {{ $bus->chassis_type }} • {{ $bus->body_maker }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Kelas & Layout -->
                                    <td class="py-3 align-middle">
                                        <div class="flex flex-col gap-1">
                                            <div
                                                class="flex items-center gap-1.5 text-xs font-bold text-zinc-700 dark:text-zinc-300">
                                                <span class="size-2 rounded-full bg-indigo-500"></span>
                                                {{ optional($bus->busClass)->name ?? '-' }}
                                            </div>
                                            <div class="text-[10px] text-zinc-500 flex items-center gap-1">
                                                <x-heroicon-o-users class="w-3 h-3" />
                                                {{ $bus->total_seats }} Kursi • {{ optional($bus->seatLayout)->name }}
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Expiry (STNK/KIR) -->
                                    <td class="py-3">
                                        @php
                                            $stnkPast =
                                                $bus->stnk_expired_at &&
                                                \Carbon\Carbon::parse($bus->stnk_expired_at)->isPast();
                                            $kirPast =
                                                $bus->kir_expired_at &&
                                                \Carbon\Carbon::parse($bus->kir_expired_at)->isPast();
                                        @endphp
                                        <div class="flex flex-col gap-1 text-[10px] font-medium">
                                            <span
                                                class="{{ $stnkPast ? 'text-red-500 font-bold' : 'text-zinc-600 dark:text-zinc-400' }}">
                                                STNK:
                                                {{ $bus->stnk_expired_at ? \Carbon\Carbon::parse($bus->stnk_expired_at)->format('d M Y') : '-' }}
                                            </span>
                                            <span
                                                class="{{ $kirPast ? 'text-red-500 font-bold' : 'text-zinc-600 dark:text-zinc-400' }}">
                                                KIR:
                                                {{ $bus->kir_expired_at ? \Carbon\Carbon::parse($bus->kir_expired_at)->format('d M Y') : '-' }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-3">
                                        @php
                                            $statusClass = match ($bus->status->value ?? 'null') {
                                                'active'
                                                    => 'bg-green-50 text-green-600 border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20',
                                                'maintenance'
                                                    => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
                                                'inactive'
                                                    => 'bg-zinc-50 text-zinc-600 border-zinc-200 dark:bg-zinc-500/10 dark:text-zinc-400 dark:border-zinc-500/20',
                                                default => 'bg-zinc-100 text-zinc-500 border-zinc-200',
                                            };
                                            $statusFormat = ucfirst($bus->status->value ?? 'Unknown');
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border uppercase tracking-wider {{ $statusClass }}">
                                            {{ $statusFormat }}
                                        </span>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-3 text-right pr-4">
                                        <div class="flex items-center justify-end gap-1">
                                            <a wire:navigate href="{{ route('buses.edit', $bus->id) }}"
                                                class="btn btn-ghost btn-xs btn-square hover:bg-zinc-100 dark:hover:bg-zinc-800 text-indigo-500 rounded-lg inline-flex items-center justify-center"
                                                title="Edit Data">
                                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                                            </a>
                                            <button wire:click="confirmDeleteBus({{ $bus->id }})"
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
                                    <x-heroicon-o-truck class="w-12 h-12 mx-auto mb-3 opacity-20" />
                                    <p class="font-medium text-lg">Tidak ada armada ditemukan</p>
                                    <p class="text-xs mt-1">Ganti filter pencarian atau tambahkan armada baru.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $this->buses->links() }}
            </div>
        </div>
    </div>



    <!-- Confirm Delete Modal -->
    <div class="modal {{ $confirmingBusDeletion ? 'modal-open' : '' }}" role="dialog">
        <div
            class="modal-box bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-2xl">
            <h3 class="font-bold text-lg text-red-600 flex items-center gap-2">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                Konfirmasi Hapus Armada
            </h3>
            <p class="py-4 text-zinc-600 dark:text-zinc-400">
                Apakah Anda yakin ingin menghapus data bus ini? Data yang dihapus tidak dapat dikembalikan secara
                permanen jika sudah terhubung dengan data lain (Soft Deletes akan diaplikasikan jika tersedia).
            </p>
            <div class="modal-action">
                <button wire:click="$set('confirmingBusDeletion', false)" class="btn btn-sm btn-ghost">Batal</button>
                <button wire:click="deleteBus"
                    class="btn btn-sm bg-red-600 hover:bg-red-700 text-white border-0 shadow-lg shadow-red-600/30">
                    <span wire:loading.remove wire:target="deleteBus">Ya, Hapus!</span>
                    <span wire:loading wire:target="deleteBus"
                        class="loading loading-spinner loading-xs hidden"></span>
                </button>
            </div>
        </div>
        <div class="modal-backdrop bg-zinc-900/40 backdrop-blur-sm" wire:click="$set('confirmingBusDeletion', false)">
        </div>
    </div>
</div>
