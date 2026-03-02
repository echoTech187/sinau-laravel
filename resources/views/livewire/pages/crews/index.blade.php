@php
    /** @var \App\Livewire\Pages\Crews\Index $this */
@endphp
<div class="relative min-h-full">
    <!-- Decorative Background Blob -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div
            class="absolute top-0 right-0 w-150 h-150 bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-[100px]">
        </div>
        <div class="absolute bottom-0 left-1/4 w-125 h-125 bg-teal-500/10 dark:bg-teal-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8">
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/50 pb-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-linear-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/30">
                            <x-heroicon-o-users class="w-6 h-6 text-white" />
                        </div>
                        Master Data Kru
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Manajemen data kru operasional, pengemudi, awak kabin, dan status lisensi (SIM/SIPA).
                    </p>
                </div>
                <!-- Action Buttons -->
                <div
                    class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 mt-4 sm:mt-0 w-full sm:w-auto">
                    <button
                        class="btn btn-sm bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl transition-all">
                        <x-heroicon-o-arrow-path class="w-4 h-4" />
                        Synchronize
                    </button>
                    <a wire:navigate href="{{ route('crews.create') }}"
                        class="btn btn-sm bg-emerald-600 hover:bg-emerald-700 text-white border-0 shadow-lg shadow-emerald-600/30 rounded-xl transition-all hover:-translate-y-0.5 font-bold">
                        <x-heroicon-o-plus class="w-4 h-4" />
                        Tambah Kru Baru
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
                    <x-heroicon-o-user-group class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Total Kru</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['total'] }}</p>
                </div>
            </div>

            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl text-emerald-500">
                    <x-heroicon-o-check-circle class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Aktif Beroperasi</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['active'] }}</p>
                </div>
            </div>

            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-amber-50 dark:bg-amber-500/10 rounded-xl text-amber-500">
                    <x-heroicon-o-clock class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Cuti / Standby</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['on_leave'] }}</p>
                </div>
            </div>

            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-red-50 dark:bg-red-500/10 rounded-xl text-red-500">
                    <x-heroicon-o-no-symbol class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Nonaktif / Suspend</p>
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
                    <select wire:model.live="positionFilter"
                        class="select select-sm border-0 bg-white dark:bg-zinc-800 focus:outline-none focus:ring-0 text-zinc-600 dark:text-zinc-300 font-medium">
                        <option value="" class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-white">Semua
                            Posisi</option>
                        @foreach ($this->crewPositions as $cp)
                            <option wire:key="cp-{{ $cp->id }}" value="{{ $cp->id }}"
                                class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-white">{{ $cp->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="w-px h-6 bg-zinc-300 dark:bg-zinc-700 my-auto"></div>
                    <select wire:model.live="statusFilter"
                        class="select select-sm border-0 bg-white dark:bg-zinc-800 focus:outline-none focus:ring-0 text-zinc-600 dark:text-zinc-300 font-medium">
                        <option value="" class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-white">Semua
                            Status</option>
                        <option value="active" class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-white">Active
                        </option>
                        <option value="on_leave" class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-white">
                            Cuti/Standby</option>
                        <option value="suspended" class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-white">
                            Suspended</option>
                        <option value="inactive" class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-white">
                            Inactive</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="relative w-full md:w-80 group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <x-heroicon-o-magnifying-glass
                            class="w-4 h-4 text-zinc-400 group-focus-within:text-emerald-500 transition-colors" />
                    </div>
                    <input type="text" placeholder="Cari NIK, Nama, No. HP..."
                        wire:model.live.debounce.300ms="search"
                        class="input input-sm w-full pl-11 bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-emerald-500/20" />
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="table table-sm w-full">
                    <thead>
                        <tr
                            class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider text-[10px]">
                            <th class="py-4 rounded-tl-xl pl-4">NIK & Nama</th>
                            <th class="py-4">Posisi</th>
                            <th class="py-4">Kontak</th>
                            <th class="py-4">Lisensi & Exp.</th>
                            <th class="py-4">Status</th>
                            <th class="py-4 rounded-tr-xl text-right pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @if (count($this->crews) > 0)
                            @foreach ($this->crews as $crew)
                                <tr wire:key="crew-{{ $crew->id }}"
                                    class="border-b border-zinc-100 dark:border-zinc-800/50 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                    <td class="py-3 pl-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center shrink-0">
                                                <x-heroicon-o-user class="w-5 h-5 text-zinc-400" />
                                            </div>
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-bold text-zinc-900 dark:text-white">{{ $crew->name }}</span>
                                                <div class="flex items-center gap-1.5 mt-0.5">
                                                    <span
                                                        class="inline-block px-1.5 py-0.5 rounded border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-[10px] font-mono font-bold text-zinc-600 dark:text-zinc-400 tracking-widest">
                                                        {{ $crew->employee_number }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-3">
                                        <div
                                            class="flex items-center gap-1.5 text-xs font-bold text-zinc-700 dark:text-zinc-300">
                                            <span class="size-2 rounded-full bg-emerald-500"></span>
                                            {{ optional($crew->position)->name ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="py-3 align-middle text-xs text-zinc-600 dark:text-zinc-400 font-mono">
                                        <div class="flex items-center gap-1.5">
                                            <x-heroicon-o-phone class="w-3.5 h-3.5" />
                                            {{ $crew->phone_number }}
                                        </div>
                                    </td>

                                    <td class="py-3">
                                        @php
                                            $licensePast =
                                                $crew->license_expired_at &&
                                                \Carbon\Carbon::parse($crew->license_expired_at)->isPast();
                                        @endphp
                                        <div class="flex flex-col gap-1 text-[10px] font-medium">
                                            <span
                                                class="text-zinc-900 dark:text-white font-bold">{{ $crew->license_number ?? 'Belum ada lisensi' }}</span>
                                            @if ($crew->license_expired_at)
                                                <span
                                                    class="{{ $licensePast ? 'text-red-500 font-bold flex items-center gap-1' : 'text-zinc-500 flex items-center gap-1' }}">
                                                    @if ($licensePast)
                                                        <x-heroicon-s-exclamation-circle class="w-3 h-3" />
                                                    @endif
                                                    Exp:
                                                    {{ \Carbon\Carbon::parse($crew->license_expired_at)->format('d M Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="py-3">
                                        @php
                                            $statusClass = match ($crew->status->value ?? 'null') {
                                                'active'
                                                    => 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
                                                'on_leave'
                                                    => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
                                                'suspended',
                                                'inactive'
                                                    => 'bg-red-50 text-red-600 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:/20',
                                                default => 'bg-zinc-100 text-zinc-500 border-zinc-200',
                                            };
                                            $statusFormat = ucfirst(
                                                str_replace('_', ' ', $crew->status->value ?? 'Unknown'),
                                            );
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border uppercase tracking-wider {{ $statusClass }}">
                                            {{ $statusFormat }}
                                        </span>
                                    </td>

                                    <td class="py-3 text-right pr-4">
                                        <div class="flex items-center justify-end gap-1">
                                            <a wire:navigate href="{{ route('crews.edit', $crew->id) }}"
                                                class="btn btn-ghost btn-xs btn-square hover:bg-zinc-100 dark:hover:bg-zinc-800 text-emerald-500 rounded-lg inline-flex items-center justify-center"
                                                title="Edit Data">
                                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                                            </a>
                                            <button wire:click="confirmDeleteCrew({{ $crew->id }})"
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
                                    <x-heroicon-o-users class="w-12 h-12 mx-auto mb-3 opacity-20" />
                                    <p class="font-medium text-lg">Tidak ada data kru ditemukan</p>
                                    <p class="text-xs mt-1">Ganti filter pencarian atau tambahkan kru baru.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $this->crews->links() }}
            </div>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal {{ $confirmingCrewDeletion ? 'modal-open' : '' }}" role="dialog">
        <div
            class="modal-box bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-2xl">
            <h3 class="font-bold text-lg text-red-600 flex items-center gap-2">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                Konfirmasi Hapus Kru
            </h3>
            <p class="py-4 text-zinc-600 dark:text-zinc-400">
                Apakah Anda yakin ingin menghapus data kru ini? Data yang dihapus tidak dapat dikembalikan secara
                permanen jika sudah terhubung dengan sistem penjadwalan.
            </p>
            <div class="modal-action">
                <button wire:click="$set('confirmingCrewDeletion', false)" class="btn btn-sm btn-ghost">Batal</button>
                <button wire:click="deleteCrew"
                    class="btn btn-sm bg-red-600 hover:bg-red-700 text-white border-0 shadow-lg shadow-red-600/30">
                    <span wire:loading.remove wire:target="deleteCrew">Ya, Hapus!</span>
                    <span wire:loading wire:target="deleteCrew"
                        class="loading loading-spinner loading-xs hidden"></span>
                </button>
            </div>
        </div>
        <div class="modal-backdrop bg-zinc-900/40 backdrop-blur-sm"
            wire:click="$set('confirmingCrewDeletion', false)"></div>
    </div>
</div>
