@php
    /** @var \App\Livewire\Pages\Shipments\Create $this */
@endphp
<div class="relative min-h-full">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-150 h-150 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
        <div class="absolute bottom-0 left-1/4 w-125 h-125 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8 animate-fade-in-up">
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/50 pb-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-linear-to-br from-indigo-500 to-blue-600 shadow-lg shadow-indigo-500/30">
                            <x-heroicon-o-plus class="w-5 h-5 text-white" />
                        </div>
                        Registrasi Kargo Baru
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Input data pengirim, penerima, dan detail muatan.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a wire:navigate href="{{ route('shipments.index') }}"
                        class="btn btn-sm bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl transition-all">
                        <x-heroicon-o-arrow-left class="w-4 h-4" />
                        Kembali
                    </a>
                    <button wire:click="save"
                        class="btn btn-sm bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/30 rounded-xl transition-all hover:-translate-y-0.5 font-bold">
                        <x-heroicon-o-document-plus class="w-4 h-4" />
                        Terbitkan Resi
                    </button>
                </div>
            </header>
        </div>

        <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Sender & Receiver -->
            <div class="lg:col-span-2 space-y-6">
                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-10">
                        <!-- Sender Section -->
                        <div class="space-y-6">
                            <h4
                                class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                                <x-heroicon-o-user class="w-4 h-4" />
                                Data Pengirim
                            </h4>
                            <div class="space-y-5">
                                <div class="form-control w-full">
                                    <label class="label pb-0">
                                        <span
                                            class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                            Nama Lengkap
                                        </span>
                                    </label>
                                    <input type="text" wire:model="form.sender_name" placeholder="Nama Pengirim..."
                                        class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-zinc-900 dark:text-white" />
                                    @error('form.sender_name')
                                        <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-control w-full">
                                    <label class="label pb-0">
                                        <span
                                            class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                            No. WhatsApp
                                        </span>
                                    </label>
                                    <input type="text" wire:model="form.sender_phone" placeholder="08..."
                                        class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-zinc-900 dark:text-white" />
                                    @error('form.sender_phone')
                                        <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Receiver Section -->
                        <div
                            class="space-y-6 border-t md:border-t-0 md:border-l border-zinc-100 dark:border-zinc-800/50 pt-8 md:pt-0 md:pl-10">
                            <h4
                                class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                                <x-heroicon-o-map-pin class="w-4 h-4" />
                                Data Penerima
                            </h4>
                            <div class="space-y-5">
                                <div class="form-control w-full">
                                    <label class="label pb-0">
                                        <span
                                            class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                            Nama Lengkap
                                        </span>
                                    </label>
                                    <input type="text" wire:model="form.receiver_name" placeholder="Nama Penerima..."
                                        class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-zinc-900 dark:text-white" />
                                    @error('form.receiver_name')
                                        <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-control w-full">
                                    <label class="label pb-0">
                                        <span
                                            class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                            No. WhatsApp
                                        </span>
                                    </label>
                                    <input type="text" wire:model="form.receiver_phone" placeholder="08..."
                                        class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-zinc-900 dark:text-white" />
                                    @error('form.receiver_phone')
                                        <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Details -->
                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm space-y-6">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-archive-box class="w-4 h-4" />
                        Detail Muatan
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="form-control w-full md:col-span-2">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Deskripsi Barang
                                </span>
                            </label>
                            <input type="text" wire:model="form.item_description"
                                placeholder="Contoh: Dokumen, Sparepart..."
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-zinc-900 dark:text-white" />
                            @error('form.item_description')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Berat Fisik (KG)
                                </span>
                            </label>
                            <input type="number" step="0.1" wire:model.live="form.actual_weight_kg"
                                placeholder="0.0"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-zinc-800 dark:text-zinc-100" />
                            @error('form.actual_weight_kg')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Berat Tagihan (KG)
                                </span>
                            </label>
                            <input type="number" step="0.1" wire:model="form.chargeable_weight_kg"
                                placeholder="0.0"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-zinc-800 dark:text-zinc-100" />
                            @error('form.chargeable_weight_kg')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Biaya Kargo (Rp)
                                </span>
                            </label>
                            <input type="number" wire:model="form.shipping_cost" placeholder="0"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-zinc-800 dark:text-zinc-100" />
                            @error('form.shipping_cost')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Routing & Logistics -->
            <div class="space-y-6">
                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm space-y-6">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-truck class="w-4 h-4" />
                        Logistik & Rute
                    </h4>

                    <div class="space-y-5">
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Titik Naik
                                </span>
                            </label>
                            <select wire:model="form.origin_location_id"
                                class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-zinc-900 dark:text-white">
                                <option value="">Pilih Asal</option>
                                @foreach ($this->locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Titik Turun
                                </span>
                            </label>
                            <select wire:model="form.destination_location_id"
                                class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-zinc-900 dark:text-white">
                                <option value="">Pilih Tujuan</option>
                                @foreach ($this->locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Tugaskan ke Bus / Trip
                                </span>
                            </label>
                            <select wire:model="form.schedule_id"
                                class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-zinc-900 dark:text-white opacity-90">
                                <option value="">Pilih Jadwal (Opsional)</option>
                                @foreach ($this->activeSchedules as $s)
                                    <option value="{{ $s->id }}">{{ $s->departure_time->format('d M H:i') }} -
                                        {{ $s->bus->fleet_code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Relasi Tiket Penumpang
                                </span>
                            </label>
                            <select wire:model="form.booking_id"
                                class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-zinc-900 dark:text-white">
                                <option value="">Bukan Bagasi (Kargo Mandiri)</option>
                                @foreach ($this->recentBookings as $b)
                                    <option value="{{ $b->id }}">{{ $b->booking_code }} -
                                        {{ $b->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-indigo-600 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-indigo-600/20 overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-16 -mt-16">
                    </div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80">Ringkasan Biaya</p>
                        <div class="text-3xl font-black mt-2 tracking-tighter">Rp
                            {{ number_format($form->shipping_cost, 0, ',', '.') }}</div>
                        <div class="flex items-center gap-2 mt-4 opacity-80 italic">
                            <x-heroicon-o-information-circle class="w-4 h-4" />
                            <span class="text-[10px] font-medium">{{ $form->chargeable_weight_kg }} KG x Tarif per
                                Rute</span>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="btn btn-block bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/30 rounded-2xl h-14 font-black tracking-tight text-sm transition-all transform active:scale-[0.98] group overflow-hidden relative">
                        <div
                            class="absolute inset-0 bg-linear-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer">
                        </div>
                        <span wire:loading.remove wire:target="save" class="flex items-center gap-2 relative z-10">
                            <x-heroicon-o-document-plus class="w-5 h-5" />
                            Terbitkan Resi
                        </span>
                        <span wire:loading wire:target="save"
                            class="loading loading-spinner loading-md relative z-10"></span>
                    </button>
                    <div class="mt-3 text-center">
                        <a wire:navigate href="{{ route('shipments.index') }}"
                            class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 hover:text-indigo-500 transition-colors">Batal</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
