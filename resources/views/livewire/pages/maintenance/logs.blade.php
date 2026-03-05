@php
    /** @var \App\Livewire\Pages\Maintenance\Logs $this */
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
        <!-- Header -->
        <div class="animate-fade-in-up">
            <header
                class="flex flex-wrap items-center justify-between gap-6 border-b border-zinc-200 dark:border-zinc-700/50 pb-6">
                <div class="flex-1 min-w-[280px]">
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-linear-to-br from-indigo-500 to-blue-600 shadow-lg shadow-indigo-500/30">
                            <x-heroicon-o-clipboard-document-check class="w-6 h-6 text-white" />
                        </div>
                        Riwayat Perawatan
                    </h1>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                        Kelola dan telusuri sejarah perbaikan teknis serta perawatan berkala seluruh armada.
                    </p>
                </div>
                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center justify-end gap-3 w-full sm:w-auto">
                    <button @click="$wire.showCreateModal = true"
                        class="btn btn-sm sm:btn-md bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/30 rounded-2xl transition-all hover:-translate-y-0.5 font-bold">
                        <x-heroicon-o-plus class="w-5 h-5" />
                        Catat Perawatan
                    </button>
                </div>
            </header>
        </div>

        <!-- Table Card -->
        <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm animate-fade-in-up"
            style="animation-delay: 0.1s">
            <div class="overflow-x-auto">
                <table class="table table-sm w-full">
                    <thead>
                        <tr
                            class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider text-[10px]">
                            <th class="py-4 pl-4">Tanggal</th>
                            <th class="py-4">Armada</th>
                            <th class="py-4">Jenis & Tugas</th>
                            <th class="py-4">Deskripsi</th>
                            <th class="py-4 text-right">Biaya</th>
                            <th class="py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @if (count($logs) > 0)
                            @foreach ($logs as $log)
                                <tr wire:key="log-{{ $log->id }}"
                                    class="border-b border-zinc-100 dark:border-zinc-800/50 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                    <td class="py-3 pl-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="font-bold text-zinc-900 dark:text-white">{{ $log->created_at->format('d/m/Y') }}</span>
                                            <span
                                                class="text-[10px] text-zinc-500">{{ $log->created_at->format('H:i') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="size-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-xs border border-zinc-200 dark:border-zinc-700">
                                                {{ $log->bus->fleet_code }}
                                            </div>
                                            <span
                                                class="text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ number_format($log->odometer_at_service) }}
                                                KM</span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="px-2 py-0.5 rounded-full text-[9px] font-bold border uppercase tracking-wider w-fit
                                                {{ $log->type->value == 'preventive'
                                                    ? 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20'
                                                    : 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20' }}">
                                                {{ $log->type->value }}
                                            </span>
                                            <span
                                                class="text-xs font-bold text-zinc-700 dark:text-zinc-300">{{ $log->rule?->task_name ?? 'UMUM' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 max-w-xs transition-all">
                                        <p class="text-xs text-zinc-600 dark:text-zinc-400 line-clamp-2"
                                            title="{{ $log->issue_description }}">
                                            {{ $log->issue_description }}
                                        </p>
                                        @if ($log->vendor_name)
                                            <span
                                                class="text-[9px] text-indigo-500 font-bold uppercase mt-1 flex items-center gap-1">
                                                <x-heroicon-o-map-pin class="w-2.5 h-2.5" />
                                                {{ $log->vendor_name }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right pr-4 font-mono font-bold text-zinc-900 dark:text-white">
                                        Rp {{ number_format($log->total_cost, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 text-center">
                                        @php
                                            $statusClass = match ($log->status->value) {
                                                'resolved'
                                                    => 'bg-green-50 text-green-600 border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20',
                                                'pending'
                                                    => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
                                                'in_progress'
                                                    => 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20',
                                                default => 'bg-zinc-100 text-zinc-500 border-zinc-200',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold border uppercase tracking-wider {{ $statusClass }}">
                                            {{ $log->status->value }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6"
                                    class="py-12 text-center text-zinc-500 dark:text-zinc-400 uppercase tracking-widest text-[10px] font-bold">
                                    <x-heroicon-o-magnifying-glass class="w-12 h-12 mx-auto mb-3 opacity-20" />
                                    Belum ada catatan perawatan.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Form Redesigned -->
    <x-modal wire:model="showCreateModal" title="Catat Perawatan Baru" separator>
        <div class="grid grid-cols-1 gap-6 p-2">
            <!-- Armada Selection -->
            <div class="bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                <x-select label="Pilih Armada" wire:model="selectedBusId" :options="$buses" option-label="fleet_code"
                    placeholder="Pilih Bus Berdasarkan Fleet" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-select label="Jenis Perawatan" wire:model="type" :options="[
                    ['id' => 'preventive', 'name' => 'Preventive (Berkala/Rutin)'],
                    ['id' => 'corrective', 'name' => 'Corrective (Perbaikan/Laka)'],
                ]" />
                <x-select label="Aturan Terkait" wire:model="ruleId" :options="$rulesList" option-label="task_name"
                    placeholder="Pilih Aturan Servis" />
            </div>

            <x-select label="Bengkel / Lokasi Perbaikan" wire:model="locationId" :options="$locationsList" option-label="name"
                placeholder="Pilih Pool/Agen/Bengkel Terdaftar" />

            <x-textarea label="Detail Pekerjaan & Suku Cadang" wire:model="issue"
                placeholder="Tuliskan detail perincian perbaikan atau penggantian sparepart..." rows="4" />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-input label="Odometer Saat Ini" wire:model="odoAtService" type="number" suffix="KM" />
                <x-input label="Total Biaya" wire:model="cost" type="number" prefix="Rp" />
                <x-select label="Status Pengerjaan" wire:model="status" :options="[
                    ['id' => 'pending', 'name' => 'Pending'],
                    ['id' => 'in_progress', 'name' => 'Dalam Pengerjaan'],
                    ['id' => 'resolved', 'name' => 'Selesai (Resolved)'],
                ]" />
            </div>

            <div
                class="bg-indigo-50/30 dark:bg-indigo-500/5 p-4 rounded-2xl border border-indigo-100 dark:border-indigo-900/30">
                <x-input label="Bengkel Rekanan (Luar)" wire:model="vendor"
                    placeholder="Nama vendor jika diperbaiki di bengkel luar" />
                <p class="text-[10px] text-zinc-500 mt-2 italic">Kosongkan jika diperbaiki di bengkel internal/pool.</p>
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Batal" @click="$wire.showCreateModal = false" class="btn-ghost" />
            <x-button label="Simpan Riwayat Perawatan"
                class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl px-6" wire:click="saveLog"
                spinner="saveLog" />
        </x-slot:actions>
    </x-modal>
</div>
