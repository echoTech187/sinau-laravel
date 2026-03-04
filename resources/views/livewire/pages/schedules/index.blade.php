@php
    /** @var \App\Livewire\Pages\Schedules\Index $this */
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
            <x-page-header title="Penjadwalan Keberangkatan"
                description="Kelola jam operasional armada, penugasan kru, dan pantau status perjalanan secara real-time."
                icon="heroicon-o-calendar-days" iconGradient="from-indigo-500 to-purple-600"
                iconShadow="shadow-indigo-500/30">
                <a wire:navigate href="{{ route('schedules.create') }}"
                    class="btn btn-sm sm:btn-md bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/30 rounded-2xl transition-all hover:-translate-y-0.5 font-bold">
                    <x-heroicon-o-plus-circle class="w-5 h-5" />
                    Terbitkan Jadwal
                </a>
            </x-page-header>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-up"
            style="animation-delay: 0.1s">
            <div
                class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 p-5 rounded-3xl shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                        <x-heroicon-o-document-text class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-widest">Total Trip</p>
                        <h3 class="text-2xl font-bold text-zinc-900 dark:text-white">
                            {{ number_format($this->stats['total']) }}</h3>
                    </div>
                </div>
            </div>

            <div
                class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 p-5 rounded-3xl shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-2xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400">
                        <x-heroicon-o-clock class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-widest">Akan Datang</p>
                        <h3 class="text-2xl font-bold text-zinc-900 dark:text-white text-blue-600 dark:text-blue-400">
                            {{ number_format($this->stats['scheduled']) }}</h3>
                    </div>
                </div>
            </div>

            <div
                class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 p-5 rounded-3xl shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-2xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400">
                        <x-heroicon-o-map class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-widest">Di Perjalanan</p>
                        <h3 class="text-2xl font-bold text-zinc-900 dark:text-white text-amber-600 dark:text-amber-400">
                            {{ number_format($this->stats['on_the_way']) }}</h3>
                    </div>
                </div>
            </div>

            <div
                class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 p-5 rounded-3xl shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div
                        class="p-3 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                        <x-heroicon-o-check-circle class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-widest">Telah Sampai</p>
                        <h3
                            class="text-2xl font-bold text-zinc-900 dark:text-white text-emerald-600 dark:text-emerald-400">
                            {{ number_format($this->stats['arrived']) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toolbar & Filter Area -->
        <div class="animate-fade-in-up" style="animation-delay: 0.2s">
            <div
                class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-4 shadow-sm space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="relative flex-1">
                        <x-heroicon-o-magnifying-glass
                            class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400" />
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Cari Rute, atau Kode..."
                            class="w-full pl-12 pr-4 py-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all shadow-inner" />
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-zinc-400 uppercase">Status:</span>
                            <select wire:model.live="filter_status"
                                class="select select-sm select-bordered bg-white dark:bg-zinc-900 rounded-xl border-zinc-200 dark:border-zinc-800 text-xs shadow-sm">
                                <option value="" class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-white">
                                    Semua Status</option>
                                <option value="scheduled"
                                    class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-white">Scheduled</option>
                                <option value="boarding"
                                    class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-white">Boarding</option>
                                <option value="on_the_way"
                                    class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-white">On The Way</option>
                                <option value="arrived" class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-white">
                                    Arrived</option>
                                <option value="canceled"
                                    class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-white">Canceled</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-zinc-400 uppercase">Periode:</span>
                            <input type="date" wire:model.live="date_from"
                                class="input input-sm input-bordered bg-white dark:bg-zinc-900 rounded-xl border-zinc-200 dark:border-zinc-800 text-xs" />
                            <span class="text-zinc-400">to</span>
                            <input type="date" wire:model.live="date_to"
                                class="input input-sm input-bordered bg-white dark:bg-zinc-900 rounded-xl border-zinc-200 dark:border-zinc-800 text-xs" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="animate-fade-in-up" style="animation-delay: 0.3s">
            <div
                class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="table table-sm w-full">
                        <thead>
                            <tr class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                                    Info Keberangkatan</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                                    Rute & Trayek</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                                    Armada</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                                    Kru Tugas</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                                    Harga & Tipe</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] text-right">
                                    AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 text-sm">
                            @if (count($this->schedules) > 0)
                                @foreach ($this->schedules as $schedule)
                                    <tr wire:key="schedule-{{ $schedule->id }}"
                                        class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors border-b border-zinc-100 dark:border-zinc-800/50 last:border-0">
                                        <!-- Timing -->
                                        <td class="py-4 pl-6">
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-bold text-zinc-900 dark:text-white">{{ $schedule->departure_date->format('d M Y') }}</span>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span
                                                        class="px-1.5 py-0.5 rounded bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-[10px] font-mono font-bold border border-indigo-100 dark:border-indigo-800">
                                                        {{ $schedule->departure_time->format('H:i') }}
                                                    </span>
                                                    <x-heroicon-o-chevron-right class="w-3 h-3 text-zinc-300" />
                                                    <span class="text-[10px] text-zinc-500 font-medium">ETA
                                                        {{ $schedule->arrival_estimate->format('H:i') }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Route -->
                                        <td class="py-4">
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-bold text-zinc-800 dark:text-zinc-200 text-xs">{{ $schedule->route->name }}</span>
                                                <span
                                                    class="text-[10px] text-zinc-500 mt-0.5 font-semibold tracking-tighter uppercase">{{ $schedule->route->route_code }}</span>
                                            </div>
                                        </td>

                                        <!-- Bus -->
                                        <td class="py-4 whitespace-nowrap">
                                            @if ($schedule->bus)
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="p-1.5 rounded-lg bg-sky-50 dark:bg-sky-900/20 text-sky-600">
                                                        <x-heroicon-o-truck class="w-4 h-4" />
                                                    </div>
                                                    <div>
                                                        <p
                                                            class="text-[11px] font-bold text-zinc-800 dark:text-zinc-300 leading-tight">
                                                            {{ $schedule->bus->name }}</p>
                                                        <p class="text-[9px] text-zinc-500">
                                                            {{ $schedule->bus->plate_number }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="p-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 text-zinc-400">
                                                        <x-heroicon-o-truck class="w-4 h-4" />
                                                    </div>
                                                    <div>
                                                        <p
                                                            class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 leading-tight italic">
                                                            Belum Ditentukan</p>
                                                        <p class="text-[9px] text-zinc-400">
                                                            Pilih Armada
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Crews -->
                                        <td class="py-4">
                                            <div class="flex -space-x-1.5 overflow-hidden">
                                                @foreach ($schedule->crews as $crew)
                                                    <div wire:key="schedule-{{ $schedule->id }}-crew-{{ $crew->id }}"
                                                        class="inline-flex items-center justify-center size-6 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-white dark:border-zinc-900 text-[9px] font-bold text-zinc-600 dark:text-zinc-400"
                                                        title="{{ optional($crew->crew)->name }} ({{ optional($crew->position)->name }})">
                                                        {{ collect(explode(' ', optional($crew->crew)->name ?? ''))->take(1)->map(fn($w) => substr($w, 0, 1))->implode('') }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>

                                        <!-- Price & Type -->
                                        <td class="py-4">
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-bold text-indigo-600 dark:text-indigo-400 text-xs">Rp{{ number_format($schedule->base_price) }}</span>
                                                <span
                                                    class="text-[9px] text-zinc-400 uppercase tracking-widest font-bold mt-0.5">{{ $schedule->trip_type->value }}</span>
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="py-4">
                                            @php
                                                $statusClass = match ($schedule->status->value) {
                                                    'scheduled'
                                                        => 'bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800',
                                                    'on_the_way'
                                                        => 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800',
                                                    'arrived'
                                                        => 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800',
                                                    'canceled'
                                                        => 'bg-red-50 text-red-600 border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
                                                    default => 'bg-zinc-50 text-zinc-600 border-zinc-100',
                                                };
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold border uppercase tracking-wider {{ $statusClass }}">
                                                {{ $schedule->status->value }}
                                            </span>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <a wire:navigate href="{{ route('schedules.edit', $schedule) }}"
                                                    class="btn btn-ghost btn-xs btn-square hover:bg-zinc-100 dark:hover:bg-zinc-800 text-indigo-500 rounded-lg inline-flex items-center justify-center"
                                                    title="Edit Jadwal">
                                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                                </a>
                                                <button type="button"
                                                    wire:confirm="Batalkan jadwal rute {{ $schedule->route->name }}?"
                                                    wire:click="deleteSchedule({{ $schedule->id }})"
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
                                    <td colspan="7" class="py-12 text-center text-zinc-500">
                                        <x-heroicon-o-calendar-days class="w-12 h-12 mx-auto mb-3 opacity-20" />
                                        <p class="font-medium">Tidak ada jadwal ditemukan</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="animate-fade-in-up" style="animation-delay: 0.4s">
            {{ $this->schedules->links() }}
        </div>
    </div>

    @if (session()->has('message'))
        <div class="fixed bottom-6 right-6 z-50 animate-fade-in-up">
            <div class="p-4 bg-emerald-600 text-white rounded-2xl shadow-2xl flex items-center gap-3">
                <x-heroicon-s-check-circle class="w-5 h-5" />
                <span class="text-sm font-bold">{{ session('message') }}</span>
            </div>
        </div>
    @endif
</div>
