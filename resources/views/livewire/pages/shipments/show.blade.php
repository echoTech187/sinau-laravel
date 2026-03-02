@php
    /** @var \App\Livewire\Pages\Shipments\Show $this */
@endphp
<div class="container relative min-h-full pb-20">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-125 h-125 bg-indigo-500/5 rounded-full blur-[100px]"></div>
    </div>

    <div class="relative z-10 max-w-5xl mx-auto space-y-8 animate-fade-in-up">
        <header
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-6">
            <div class="flex items-center gap-4 group cursor-pointer" wire:navigate href="{{ route('shipments.index') }}">
                <div
                    class="p-3 rounded-2xl bg-white/50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 group-hover:bg-zinc-100 dark:group-hover:bg-zinc-700 transition-all">
                    <x-heroicon-o-arrow-left class="w-5 h-5 text-zinc-500" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-linear-to-br from-indigo-500 to-blue-600 shadow-lg shadow-indigo-500/20 text-white group-hover:scale-110 transition-transform">
                            <x-heroicon-o-eye class="w-6 h-6" />
                        </div>
                        Detail Resi Kargo
                    </h1>
                    <p class="text-sm font-bold text-zinc-500 dark:text-zinc-400 mt-1 uppercase tracking-widest">
                        {{ $this->shipment->waybill_number }}
                    </p>
                </div>
            </div>

            <div class="flex gap-3">
                <button onclick="window.print()"
                    class="btn bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded-2xl h-12 px-6 font-bold transition-all shadow-sm">
                    <x-heroicon-o-printer class="w-5 h-5 mr-1.5 text-zinc-400" />
                    Cetak Resi
                </button>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Waybill & Timeline -->
            <div class="lg:col-span-2 space-y-6">
                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm relative overflow-hidden">
                    <!-- Receipt Style Top -->
                    <div
                        class="flex flex-col md:flex-row justify-between items-start mb-8 pb-8 border-b border-dashed border-zinc-200 dark:border-zinc-800 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <div class="size-2 rounded-full bg-indigo-500"></div>
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Barcode
                                    ID</span>
                            </div>
                            <div
                                class="text-3xl lg:text-4xl font-black tracking-tighter text-zinc-900 dark:text-white uppercase">
                                {{ $this->shipment->barcode_number }}
                            </div>
                        </div>
                        <div class="flex flex-col items-start md:items-end">
                            <div
                                class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-900 shadow-inner">
                                <!-- Simulated QR Code -->
                                <div
                                    class="size-20 bg-zinc-900 dark:bg-white rounded-xl p-1 grid grid-cols-4 gap-0.5 opacity-80">
                                    @for ($i = 0; $i < 16; $i++)
                                        <div class="size-full {{ rand(0, 1) ? 'bg-indigo-500' : 'bg-transparent' }}">
                                        </div>
                                    @endfor
                                </div>
                            </div>
                            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mt-3">Scan for
                                validation</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 md:gap-10">
                        <div class="space-y-5">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Pengirim</p>
                            <div>
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white break-words">
                                    {{ $this->shipment->sender_name }}
                                </h3>
                                <p class="text-sm font-medium text-zinc-500 mt-1">{{ $this->shipment->sender_phone }}
                                </p>
                                <p class="text-xs font-bold text-zinc-400 mt-3 flex items-center gap-2">
                                    <x-heroicon-o-map-pin class="w-4 h-4 text-indigo-500" />
                                    {{ $this->shipment->origin->name }} ({{ $this->shipment->origin->city }})
                                </p>
                            </div>
                        </div>
                        <div class="space-y-5 sm:border-l border-zinc-100 dark:border-zinc-800/50 sm:pl-8">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Penerima</p>
                            <div>
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white break-words">
                                    {{ $this->shipment->receiver_name }}
                                </h3>
                                <p class="text-sm font-medium text-zinc-500 mt-1">{{ $this->shipment->receiver_phone }}
                                </p>
                                <p class="text-xs font-bold text-zinc-400 mt-3 flex items-center gap-2">
                                    <x-heroicon-o-flag class="w-4 h-4 text-rose-500" />
                                    {{ $this->shipment->destination->name }}
                                    ({{ $this->shipment->destination->city }})
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-10 p-6 md:p-8 rounded-3xl bg-zinc-50 dark:bg-zinc-950/40 border border-zinc-100 dark:border-zinc-900/60">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-5">Informasi Barang
                        </p>
                        <div class="flex flex-col md:flex-row justify-between gap-6">
                            <div class="flex-1">
                                <h4
                                    class="text-lg font-medium text-zinc-800 dark:text-zinc-200 tracking-tight leading-relaxed italic">
                                    "{{ $this->shipment->item_description }}"
                                </h4>
                                <div
                                    class="flex gap-4 mt-3 text-[10px] font-black uppercase tracking-widest text-zinc-400">
                                    <span>Registrasi: {{ $this->shipment->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-6 md:gap-8">
                                <div class="text-left md:text-right">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Berat
                                        Fisik</p>
                                    <p class="text-xl font-black text-zinc-900 dark:text-white">
                                        {{ $this->shipment->actual_weight_kg }} <span
                                            class="text-xs text-zinc-400">KG</span>
                                    </p>
                                </div>
                                <div class="text-left md:text-right">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Biaya
                                        Kirim</p>
                                    <p class="text-xl font-black text-indigo-600 dark:text-indigo-400">
                                        Rp {{ number_format($this->shipment->shipping_cost, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tracking Timeline -->
                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-8 border-b border-zinc-100 dark:border-zinc-800 pb-4 flex items-center gap-2">
                        <x-heroicon-o-map-pin class="w-4 h-4" />
                        Tracking History
                    </h4>

                    <div
                        class="relative space-y-10 before:absolute before:inset-0 before:ml-5 before:-translate-x-px before:h-full before:w-0.5 before:bg-linear-to-b before:from-indigo-500 before:via-zinc-200 before:to-transparent">
                        @php
                            $statuses = [
                                'received_at_agent' => 'Laporan Diterima Agen',
                                'loaded_to_bus' => 'Dimasukkan ke Lambung Bus',
                                'in_transit' => 'Dalam Perjalanan Menuju Tujuan',
                                'inspected_by_checker' => 'Lolos Razia Checker (Anti-Fraud)',
                                'unloaded' => 'Diturunkan di Agen Tujuan',
                                'claimed_by_receiver' => 'Diterima oleh Penerima',
                            ];
                            $currentStatus = $this->shipment->status->value;
                            $found = false;
                        @endphp
                        @foreach ($statuses as $key => $label)
                            <div wire:key="status-timeline-{{ $key }}"
                                class="relative flex items-center justify-between group">
                                <div class="flex items-center">
                                    <div
                                        class="flex items-center justify-center size-10 rounded-full {{ $currentStatus === $key ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-600/30' : 'bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-300' }} z-10 transition-all group-hover:scale-110">
                                        <x-heroicon-o-check
                                            class="w-5 h-5 {{ $found || $currentStatus === $key ? '' : 'opacity-0' }}" />
                                    </div>
                                    <div class="ml-6 md:ml-8">
                                        <div
                                            class="text-[11px] font-black uppercase {{ $currentStatus === $key ? 'text-indigo-600 dark:text-indigo-400' : 'text-zinc-400' }} tracking-widest">
                                            {{ $label }}
                                        </div>
                                        @if ($currentStatus === $key)
                                            <div
                                                class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 mt-1 italic tracking-widest uppercase">
                                                TERCETAT: {{ $this->shipment->updated_at->format('d M Y, H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if ($currentStatus === $key)
                                    <div
                                        class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[8px] font-black uppercase tracking-widest rounded-lg border border-emerald-500/20 shadow-sm">
                                        CURRENT
                                    </div>
                                @endif
                                @php
                                    if ($currentStatus === $key) {
                                        $found = true;
                                    }
                                @endphp
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right: Logistics Summary -->
            <aside class="space-y-6">
                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm space-y-6">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 border-b border-zinc-100 dark:border-zinc-800 pb-4 mb-4">
                        Logistik Bus
                    </h4>

                    @if ($this->shipment->schedule)
                        <div class="space-y-5">
                            <div
                                class="p-5 rounded-3xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-900">
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-4">Armada
                                    Pengangkut</p>
                                <div class="flex items-center gap-4">
                                    <div
                                        class="size-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg">
                                        <x-heroicon-o-truck class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <div class="text-lg font-black text-zinc-900 dark:text-white tracking-tighter">
                                            {{ $this->shipment->schedule->bus->fleet_code }}
                                        </div>
                                        <div
                                            class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">
                                            {{ $this->shipment->schedule->bus->busClass->name }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3 p-1">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="text-zinc-400 uppercase tracking-wider">Trip ID</span>
                                    <span
                                        class="text-zinc-700 dark:text-zinc-200 tracking-widest">#{{ $this->shipment->schedule->id }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="text-zinc-400 uppercase tracking-wider">Berangkat</span>
                                    <span
                                        class="text-zinc-700 dark:text-zinc-200">{{ $this->shipment->schedule->departure_time->format('H:i') }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="text-zinc-400 uppercase tracking-wider">Driver</span>
                                    <span class="text-zinc-700 dark:text-zinc-200 italic">Assign by SPJ</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-6 text-center opacity-40 italic">
                            <x-heroicon-o-clock class="w-10 h-10 mb-3" />
                            <p class="text-[10px] font-bold uppercase tracking-widest">Menunggu Kesiapan Bus</p>
                        </div>
                    @endif
                </div>

                @if ($this->shipment->booking)
                    <div
                        class="bg-indigo-600 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-indigo-600/30 space-y-6 relative overflow-hidden group">
                        <div
                            class="absolute -bottom-8 -right-8 size-32 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000">
                        </div>
                        <h2
                            class="text-xs font-black uppercase tracking-[0.2em] opacity-80 border-b border-white/20 pb-4">
                            Relasi Tiket
                        </h2>
                        <div class="flex items-center gap-4 relative z-10">
                            <div
                                class="size-12 rounded-2xl bg-white/20 text-white flex items-center justify-center backdrop-blur-md">
                                <x-heroicon-o-ticket class="w-6 h-6" />
                            </div>
                            <div>
                                <div class="text-lg font-black tracking-tighter leading-none">
                                    {{ $this->shipment->booking->booking_code }}
                                </div>
                                <div class="text-[9px] font-bold uppercase tracking-widest opacity-80 mt-1.5">
                                    Passenger: {{ Str::limit($this->shipment->booking->customer_name, 15) }}
                                </div>
                            </div>
                        </div>
                        <a wire:navigate href="{{ route('bookings.show', $this->shipment->booking->id) }}"
                            class="btn btn-sm btn-block bg-white/10 hover:bg-white/20 border-0 text-white font-bold uppercase tracking-widest text-[10px] rounded-xl h-12">
                            Detail Reservasi
                        </a>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</div>
