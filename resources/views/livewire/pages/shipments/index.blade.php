@php /** @var \App\Livewire\Pages\Shipments\Index $this */ @endphp
<div class="container relative min-h-screen pb-10">
    <!-- Decorative Background Blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-150 h-150 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
        <div class="absolute bottom-0 left-1/4 w-125 h-125 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-[100px]">
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
                            class="p-2.5 rounded-2xl bg-linear-to-br from-indigo-500 to-blue-600 shadow-lg shadow-indigo-500/30">
                            <x-heroicon-o-archive-box class="w-6 h-6 text-white" />
                        </div>
                        Manajemen Kargo (SJO)
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Portal pengelolaan logistik, paket, dan bagasi armada secara terpusat.
                    </p>
                </div>
                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <a wire:navigate href="{{ route('shipments.create') }}"
                        class="btn btn-sm bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/30 rounded-xl transition-all hover:-translate-y-0.5 font-bold">
                        <x-heroicon-o-plus class="w-4 h-4" />
                        Input Kargo Baru
                    </a>
                </div>
            </header>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-up"
            style="animation-delay: 0.1s">
            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-zinc-500 dark:text-zinc-400">
                    <x-heroicon-o-document-duplicate class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Total Resi</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['total'] }}</p>
                </div>
            </div>

            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-blue-50 dark:bg-blue-500/10 rounded-xl text-blue-500">
                    <x-heroicon-o-clock class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Menunggu</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['pending'] }}</p>
                </div>
            </div>

            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-amber-50 dark:bg-amber-500/10 rounded-xl text-amber-500">
                    <x-heroicon-o-truck class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Dalam Bus</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['in_transit'] }}</p>
                </div>
            </div>

            <div
                class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl text-emerald-500">
                    <x-heroicon-o-check-circle class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Terkirim</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['completed'] }}</p>
                </div>
            </div>
        </div>

        <!-- Table Card (with integrated filters) -->
        <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm animate-fade-in-up"
            style="animation-delay: 0.2s">

            <!-- Filters & Search Row -->
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between mb-6">
                <!-- Status Quick Filter -->
                <div
                    class="flex gap-2 p-1 bg-zinc-100/80 dark:bg-zinc-800/80 backdrop-blur rounded-xl border border-zinc-200/50 dark:border-zinc-700/50 w-full md:w-auto overflow-x-auto">
                    @foreach (['' => 'Semua', 'received_at_agent' => 'Diterima', 'in_transit' => 'Dalam Bus'] as $key => $label)
                        <button wire:click="$set('status', '{{ $key }}')"
                            class="flex-1 px-4 py-2 rounded-lg text-[10px] font-black tracking-[0.15em] uppercase transition-all {{ $status === $key ? 'bg-white dark:bg-zinc-700 shadow-md text-indigo-600 dark:text-indigo-400 border border-zinc-100 dark:border-zinc-600' : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <!-- Search -->
                <div class="relative w-full md:w-80 group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <x-heroicon-o-magnifying-glass
                            class="w-4 h-4 text-zinc-400 group-focus-within:text-indigo-500 transition-colors" />
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari No. Resi, Barcode, Pengirim..."
                        class="input input-sm w-full pl-11 bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20" />
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="table table-sm w-full">
                    <thead>
                        <tr
                            class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider text-[10px]">
                            <th class="py-4 pl-4 rounded-tl-xl">Waybill / Item</th>
                            <th class="py-4">Relasi Pihak</th>
                            <th class="py-4">Rute & Armada</th>
                            <th class="py-4">Logistik</th>
                            <th class="py-4">Status</th>
                            <th class="py-4 rounded-tr-xl text-right pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @if (count($this->shipments) > 0)
                            @foreach ($this->shipments as $s)
                                <tr wire:key="shipment-{{ $s->id }}"
                                    class="border-b border-zinc-100 dark:border-zinc-800/50 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors last:border-0 group">
                                    <td class="py-4 pl-4">
                                        <div class="flex flex-col mt-0.5">
                                            <div class="flex items-center gap-2 mb-1.5">
                                                @if ($s->booking_id)
                                                    <span
                                                        class="px-2 py-0.5 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-[8px] font-black rounded border border-rose-100 dark:border-rose-800/50">BAGASI</span>
                                                @else
                                                    <span
                                                        class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[8px] font-black rounded border border-indigo-100 dark:border-indigo-800/50">PAKET</span>
                                                @endif
                                                <span
                                                    class="text-sm font-black text-zinc-900 dark:text-white group-hover:text-indigo-600 transition-colors leading-none tracking-tight">{{ $s->waybill_number }}</span>
                                            </div>
                                            <p
                                                class="text-[10px] text-zinc-500 dark:text-zinc-400 font-medium italic overflow-hidden whitespace-nowrap text-ellipsis max-w-[160px]">
                                                "{{ $s->item_description }}"
                                            </p>
                                            <p
                                                class="text-[9px] text-zinc-400 dark:text-zinc-500 font-black tracking-widest mt-0.5">
                                                #{{ $s->barcode_number }}</p>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-6 h-6 bg-zinc-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center text-zinc-400 shrink-0">
                                                    <x-heroicon-o-user class="w-3.5 h-3.5" />
                                                </div>
                                                <span
                                                    class="text-xs font-bold text-zinc-700 dark:text-zinc-300 truncate max-w-[120px]">{{ $s->sender_name }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-6 h-6 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-400 shrink-0">
                                                    <x-heroicon-o-truck class="w-3.5 h-3.5" />
                                                </div>
                                                <span
                                                    class="text-xs font-bold text-indigo-600 dark:text-indigo-400 truncate max-w-[120px]">{{ $s->receiver_name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <div class="space-y-1.5">
                                            <div
                                                class="flex items-center gap-2 font-bold text-xs text-zinc-900 dark:text-white">
                                                <span>{{ $s->origin->city }}</span>
                                                <x-heroicon-o-chevron-double-right
                                                    class="w-3 h-3 text-zinc-300 dark:text-zinc-600 shrink-0" />
                                                <span
                                                    class="text-indigo-600 dark:text-indigo-400">{{ $s->destination->city }}</span>
                                            </div>
                                            @if ($s->schedule)
                                                <div
                                                    class="inline-flex items-center gap-1.5 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 rounded-lg border border-zinc-100 dark:border-zinc-800">
                                                    <x-heroicon-s-truck class="w-3 h-3 text-zinc-400" />
                                                    <span
                                                        class="text-[9px] text-zinc-500 font-black">{{ $s->schedule->bus->fleet_code }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <div class="space-y-1">
                                            <div class="text-xs font-black text-zinc-900 dark:text-white">
                                                {{ $s->actual_weight_kg }}
                                                <small class="text-[9px] opacity-40">KG</small>
                                            </div>
                                            <div class="text-[10px] text-indigo-600 dark:text-indigo-400 font-black">Rp
                                                {{ number_format($s->shipping_cost, 0, ',', '.') }}</div>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        @php
                                            $statusConfig = [
                                                'received_at_agent' => ['label' => 'DITERIMA', 'color' => 'indigo'],
                                                'loaded_to_bus' => ['label' => 'DALAM BUS', 'color' => 'amber'],
                                                'in_transit' => ['label' => 'PERJALANAN', 'color' => 'blue'],
                                                'inspected_by_checker' => ['label' => 'CHECKED', 'color' => 'emerald'],
                                                'unloaded' => ['label' => 'BONGKAR', 'color' => 'rose'],
                                                'claimed_by_receiver' => ['label' => 'SELESAI', 'color' => 'zinc'],
                                            ][$s->status->value] ?? ['label' => $s->status->name, 'color' => 'zinc'];
                                        @endphp
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-{{ $statusConfig['color'] }}-50 dark:bg-{{ $statusConfig['color'] }}-900/20 text-{{ $statusConfig['color'] }}-700 dark:text-{{ $statusConfig['color'] }}-400 border border-{{ $statusConfig['color'] }}-100 dark:border-{{ $statusConfig['color'] }}-800/50 text-[9px] font-black shadow-sm">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full bg-{{ $statusConfig['color'] }}-500"></span>
                                            {{ $statusConfig['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-right pr-4">
                                        <div
                                            class="flex items-center justify-end gap-1 translate-x-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300">
                                            <a wire:navigate href="{{ route('shipments.show', $s->id) }}"
                                                class="btn btn-ghost btn-xs btn-square hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-500 rounded-lg inline-flex items-center justify-center hover:text-indigo-500"
                                                title="Lihat Detail">
                                                <x-heroicon-o-eye class="w-4 h-4" />
                                            </a>
                                            <button wire:click="delete('{{ $s->id }}')"
                                                wire:confirm="Yakin ingin menghapus kargo ini?"
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
                                <td colspan="6" class="px-8 py-32 text-center text-zinc-400 dark:text-zinc-600">
                                    <div class="flex flex-col items-center gap-5">
                                        <div
                                            class="w-24 h-24 rounded-full bg-zinc-50 dark:bg-zinc-800/50 flex items-center justify-center border border-zinc-100 dark:border-zinc-800">
                                            <x-heroicon-o-archive-box-x-mark
                                                class="w-10 h-10 text-zinc-300 dark:text-zinc-600" />
                                        </div>
                                        <div>
                                            <p class="text-lg text-zinc-500 dark:text-zinc-400 font-bold">Tidak Ada
                                                Kargo</p>
                                            <p class="text-sm text-zinc-400 dark:text-zinc-500 mt-1">Belum ada data
                                                pengiriman untuk filter yang dipilih.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($this->shipments->hasPages())
                <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                    {{ $this->shipments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
