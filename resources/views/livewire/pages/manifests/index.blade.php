<div class="container relative min-h-screen pb-10">
    <!-- Decorative Background Blob -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div
            class="absolute top-0 right-0 w-[600px] h-[600px] bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-1/4 w-[500px] h-[500px] bg-purple-500/10 dark:bg-purple-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8">
        <!-- Header -->
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/50 pb-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/30">
                            <x-heroicon-s-clipboard-document-check class="w-6 h-6 text-white" />
                        </div>
                        Monitoring SJO & P2H
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Kelola jam operasional armada, penugasan kru, dan pantau status perjalanan secara real-time.
                    </p>
                </div>
                <!-- Action Buttons -->
                <div x-data="{ open: false }" class="relative flex items-center gap-3">
                    <button @click="open = !open"
                        class="btn btn-sm bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/30 rounded-xl transition-all hover:-translate-y-0.5 font-bold">
                        <x-heroicon-o-plus-circle class="w-4 h-4" />
                        Buat Draft SJO Baru
                    </button>

                    <!-- Available Schedules Dropdown -->
                    <div x-show="open" @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        class="absolute right-0 top-full mt-4 w-[420px] bg-white dark:bg-zinc-900 rounded-[2rem] shadow-[0_32px_64px_-12px_rgba(0,0,0,0.14)] border border-zinc-200 dark:border-zinc-800 overflow-hidden z-50">
                        <div
                            class="p-6 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 flex items-center justify-between">
                            <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Pilih Jadwal</span>
                            <div class="flex gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
                            </div>
                        </div>
                        <div class="max-h-[480px] overflow-y-auto p-3 space-y-1 custom-scrollbar">
                            @if (count($this->availableSchedules) > 0)
                                @foreach ($this->availableSchedules as $schedule)
                                    <button wire:click="generateForSchedule({{ $schedule->id }})" @click="open = false"
                                        class="w-full text-left p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-2xl transition-all group flex items-center gap-4 border border-transparent hover:border-zinc-100 dark:hover:border-zinc-700">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                            <x-heroicon-o-truck class="w-6 h-6" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div
                                                class="font-bold text-sm text-zinc-900 dark:text-white uppercase tracking-tight">
                                                {{ $schedule->bus->fleet_code }}
                                            </div>
                                            <div
                                                class="text-[10px] text-zinc-500 font-semibold uppercase truncate tracking-wide mt-0.5">
                                                {{ $schedule->route->name }}</div>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span
                                                    class="text-[9px] bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 px-2 py-0.5 rounded-lg text-zinc-600 dark:text-zinc-400 font-bold uppercase shadow-sm">
                                                    {{ $schedule->departure_time->format('H:i') }}
                                                </span>
                                                <span
                                                    class="text-[9px] bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 px-2 py-0.5 rounded-lg text-zinc-600 dark:text-zinc-400 font-bold uppercase shadow-sm">
                                                    {{ $schedule->departure_date->format('d M Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            @else
                                <div class="py-12 text-center opacity-40">
                                    <x-heroicon-o-calendar-days class="w-12 h-12 text-zinc-300 mx-auto mb-3" />
                                    <p class="text-zinc-400 font-bold uppercase tracking-widest text-[10px]">Kosong
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </header>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-up"
            style="animation-delay: 0.1s">
            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-zinc-500 dark:text-zinc-400">
                    <x-heroicon-o-document-text class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Total SJO</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">
                        {{ number_format($this->manifests->total()) }}</p>
                </div>
            </div>

            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-blue-50 dark:bg-blue-500/10 rounded-xl text-blue-500">
                    <x-heroicon-o-clock class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Draft</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">
                        {{ \App\Models\OperationalManifest::where('status', '=', 'draft')->count(['*']) }}
                    </p>
                </div>
            </div>

            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-amber-50 dark:bg-amber-500/10 rounded-xl text-amber-500">
                    <x-heroicon-o-map class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Approved</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">
                        {{ \App\Models\OperationalManifest::where('status', '=', 'approved')->count(['*']) }}
                    </p>
                </div>
            </div>

            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl text-emerald-500">
                    <x-heroicon-o-check-circle class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Grounded</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">0</p>
                </div>
            </div>
        </div>

        <!-- Toolbar & Filter -->
        <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-4 shadow-sm animate-fade-in-up"
            style="animation-delay: 0.2s">
            <div class="flex flex-col md:flex-row gap-3 items-center justify-between">
                <!-- Status Filter Pill -->
                <div
                    class="flex gap-2 p-1 bg-zinc-100/80 dark:bg-zinc-800/80 backdrop-blur rounded-xl border border-zinc-200/50 dark:border-zinc-700/50 w-full md:w-auto">
                    <select wire:model.live="status"
                        class="select select-sm border-0 bg-transparent focus:outline-none focus:ring-0 text-zinc-600 dark:text-zinc-300 font-medium">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <div class="w-px h-5 bg-zinc-300 dark:bg-zinc-700 my-auto"></div>
                    <input type="date"
                        class="input input-sm border-0 bg-transparent focus:outline-none focus:ring-0 text-zinc-600 dark:text-zinc-300 font-medium" />
                    <div class="w-px h-5 bg-zinc-300 dark:bg-zinc-700 my-auto"></div>
                    <input type="date"
                        class="input input-sm border-0 bg-transparent focus:outline-none focus:ring-0 text-zinc-600 dark:text-zinc-300 font-medium" />
                </div>

                <!-- Search -->
                <div class="relative w-full md:w-80 group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <x-heroicon-o-magnifying-glass
                            class="w-4 h-4 text-zinc-400 group-focus-within:text-indigo-500 transition-colors" />
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Rute, atau Kode..."
                        class="input input-sm w-full pl-11 bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20" />
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
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] text-right">
                                    AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 text-sm">
                            @if (count($this->manifests) > 0)
                                @foreach ($this->manifests as $manifest)
                                    <tr wire:key="manifest-{{ $manifest->id }}"
                                        class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors border-b border-zinc-100 dark:border-zinc-800/50 last:border-0">
                                        <td class="py-5 pl-6">
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-bold text-zinc-900 dark:text-white">{{ $manifest->created_at->format('d M Y') }}</span>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span
                                                        class="px-1.5 py-0.5 rounded bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-[10px] font-mono font-bold border border-indigo-100 dark:border-indigo-800">
                                                        {{ $manifest->schedule->departure_time->format('H:i') }}
                                                    </span>
                                                    <x-heroicon-o-chevron-double-right class="w-3 h-3 text-zinc-300" />
                                                    <span class="text-[10px] text-zinc-500 font-medium uppercase">ETA
                                                        10:00</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-5">
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-bold text-zinc-800 dark:text-zinc-200 text-xs">{{ $manifest->schedule->route->name }}</span>
                                                <span
                                                    class="text-[10px] text-zinc-500 mt-0.5 font-bold tracking-tight uppercase">{{ $manifest->manifest_number }}</span>
                                            </div>
                                        </td>
                                        <td class="py-5 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="p-2 rounded-lg bg-sky-50 dark:bg-sky-900/20 text-sky-600">
                                                    <x-heroicon-o-truck class="w-4 h-4" />
                                                </div>
                                                <div>
                                                    <p
                                                        class="text-[11px] font-bold text-zinc-800 dark:text-zinc-300 leading-tight">
                                                        {{ $manifest->schedule->bus->fleet_code }}</p>
                                                    <p class="text-[9px] text-zinc-500 uppercase font-medium">
                                                        {{ $manifest->schedule->bus->license_plate ?? 'B 7123 KGA' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-5">
                                            <div class="flex -space-x-1.5 overflow-hidden">
                                                @foreach ($manifest->schedule->crews as $crew)
                                                    <div class="inline-flex items-center justify-center size-6 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-white dark:border-zinc-900 text-[9px] font-bold text-zinc-600 dark:text-zinc-400"
                                                        title="{{ $crew->crew->name }} ({{ $crew->position->name }})">
                                                        {{ collect(explode(' ', $crew->crew->name))->take(1)->map(fn($w) => substr($w, 0, 1))->implode('') }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="py-5">
                                            @php
                                                $statusStyle = match ($manifest->status->value) {
                                                    'draft'
                                                        => 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800',
                                                    'approved'
                                                        => 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800',
                                                    'rejected'
                                                        => 'bg-red-50 text-red-600 border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
                                                    default
                                                        => 'bg-zinc-50 text-zinc-600 border-zinc-100 dark:bg-zinc-800/50 dark:text-zinc-400 dark:border-zinc-700',
                                                };
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold border uppercase tracking-wider {{ $statusStyle }}">
                                                {{ $manifest->status->value }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <a wire:navigate href="{{ route('manifests.checklist', $manifest) }}"
                                                    class="btn btn-ghost btn-xs btn-square hover:bg-zinc-100 dark:hover:bg-zinc-800 text-indigo-500 rounded-lg inline-flex items-center justify-center shadow-sm"
                                                    title="Cek P2H">
                                                    <x-heroicon-o-clipboard-document-check class="w-5 h-5" />
                                                </a>
                                                <button type="button"
                                                    wire:confirm="Hapus SJO {{ $manifest->manifest_number }}?"
                                                    wire:click="deleteManifest('{{ $manifest->id }}')"
                                                    class="btn btn-ghost btn-xs btn-square hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-red-500 rounded-lg"
                                                    title="Hapus">
                                                    <x-heroicon-o-trash class="w-5 h-5" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="py-20 text-center text-zinc-500">
                                        <x-heroicon-o-document-magnifying-glass
                                            class="w-12 h-12 mx-auto mb-3 opacity-20" />
                                        <p class="font-medium">Tidak ada SJO ditemukan</p>
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
            {{ $this->manifests->links() }}
        </div>
    </div>
</div>
