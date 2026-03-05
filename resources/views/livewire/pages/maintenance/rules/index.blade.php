@php
    /** @var \App\Livewire\Pages\Maintenance\Rules\Index $this */
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
                            <x-heroicon-o-adjustments-vertical class="w-6 h-6 text-white" />
                        </div>
                        Aturan Perawatan (Maintenance Rules)
                    </h1>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                        Atur interval servis, durasi pengerjaan, dan lokasi rekomendasi untuk setiap tipe armada.
                    </p>
                </div>
                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center justify-end gap-3 w-full sm:w-auto">
                    <button wire:click="create"
                        class="btn btn-sm sm:btn-md bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/30 rounded-2xl transition-all hover:-translate-y-0.5 font-bold">
                        <x-heroicon-o-plus class="w-5 h-5" />
                        Tambah Aturan
                    </button>
                </div>
            </header>
        </div>

        <!-- Table Card -->
        <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm animate-fade-in-up"
            style="animation-delay: 0.1s">
            <div class="flex justify-end mb-6">
                <div class="relative w-full md:w-80 group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <x-heroicon-o-magnifying-glass
                            class="w-4 h-4 text-zinc-400 group-focus-within:text-indigo-500 transition-colors" />
                    </div>
                    <input type="text" placeholder="Cari Tugas..." wire:model.live.debounce.300ms="search"
                        class="input input-sm w-full pl-11 bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20" />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-sm w-full">
                    <thead>
                        <tr
                            class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider text-[10px]">
                            <th class="py-4 pl-4">Tugas Perawatan</th>
                            <th class="py-4 text-center">Interval (KM)</th>
                            <th class="py-4 text-center">Toleransi (KM)</th>
                            <th class="py-4 text-center">Durasi (Jam)</th>
                            <th class="py-4">Lokasi Rekomendasi</th>
                            <th class="py-4 text-right pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach ($maintenanceRules as $rule)
                            <tr wire:key="rule-{{ $rule->id }}"
                                class="border-b border-zinc-100 dark:border-zinc-800/50 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="py-3 pl-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-bold text-zinc-900 dark:text-white">{{ $rule->task_name }}</span>
                                        <span
                                            class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">{{ $rule->chassis_brand ?? 'SEMUA BRAND' }}</span>
                                    </div>
                                </td>
                                <td class="py-3 text-center font-mono font-bold">{{ number_format($rule->interval_km) }}
                                </td>
                                <td class="py-3 text-center text-zinc-500">{{ number_format($rule->tolerance_km) }}</td>
                                <td class="py-3 text-center">
                                    <span
                                        class="px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-[10px] font-bold border border-indigo-100 dark:border-indigo-500/20">
                                        {{ $rule->estimated_hours }} Jam
                                    </span>
                                </td>
                                <td class="py-3">
                                    @if ($rule->preferredAgent)
                                        <div class="flex items-center gap-1.5 text-xs text-zinc-600 dark:text-zinc-400">
                                            <x-heroicon-o-map-pin class="w-3.5 h-3.5 text-red-500" />
                                            {{ $rule->preferredAgent->name }}
                                        </div>
                                    @else
                                        <span class="text-[10px] text-zinc-400 italic">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="py-3 text-right pr-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <button wire:click="edit({{ $rule->id }})"
                                            class="btn btn-ghost btn-xs btn-square text-indigo-500 rounded-lg">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </button>
                                        <button wire:click="delete({{ $rule->id }})"
                                            wire:confirm="Yakin ingin menghapus aturan ini? Ini akan berdampak pada kalkulasi kesehatan armada."
                                            class="btn btn-ghost btn-xs btn-square text-zinc-400 hover:text-red-500 rounded-lg">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $maintenanceRules->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <x-modal wire:model="showModal" title="{{ $ruleId ? 'Edit Aturan' : 'Tambah Aturan Baru' }}" separator>
        <div class="grid grid-cols-1 gap-6 p-2">
            <x-input label="Nama Tugas" wire:model="taskName" placeholder="Contoh: Ganti Oli Mesin" />

            <div class="grid grid-cols-2 gap-4">
                <x-input label="Brand Sasis (Opsional)" wire:model="chassisBrand"
                    placeholder="Contoh: Hino / Mercedes" />
                <x-select label="Lokasi Rekomendasi" wire:model="preferredAgentId" :options="$agents"
                    placeholder="Pilih Bengkel Utama" />
            </div>

            <div class="grid grid-cols-3 gap-4">
                <x-input label="Interval (KM)" wire:model="intervalKm" type="number" suffix="KM" />
                <x-input label="Toleransi (KM)" wire:model="toleranceKm" type="number" suffix="KM" />
                <x-input label="Estimasi (Jam)" wire:model="estimatedHours" type="number" suffix="JAM" />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Batal" @click="$wire.showModal = false" class="btn-ghost" />
            <x-button label="Simpan Aturan" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl px-6"
                wire:click="save" spinner="save" />
        </x-slot:actions>
    </x-modal>
</div>
