<div class="container relative min-h-screen pb-10">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div
            class="absolute top-0 right-0 w-[600px] h-[600px] bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-1/4 w-[500px] h-[500px] bg-purple-500/10 dark:bg-purple-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 w-full max-w-xl mx-auto space-y-10 animate-fade-in mt-10">
        <!-- Header -->
        <header class="text-center space-y-4">
            <div
                class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-zinc-800 to-zinc-900 dark:from-zinc-100 dark:to-white shadow-xl shadow-zinc-500/20 text-white dark:text-zinc-900 transform hover:rotate-12 transition-transform duration-500">
                <x-heroicon-o-shield-check class="w-10 h-10 text-emerald-400 dark:text-emerald-500" />
            </div>
            <div>
                <h1 class="text-4xl font-black tracking-tight text-zinc-900 dark:text-white uppercase">Anti-Fraud
                    Checker</h1>
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mt-2">Validasi Kargo & Bagasi
                    Armada</p>
            </div>
        </header>

        <!-- Scanner / Input Card -->
        <div
            class="w-full bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-8 shadow-xl relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                <div class="w-full h-full bg-indigo-600 animate-slide-in-right"></div>
            </div>

            <div class="space-y-6">
                <div class="flex items-center justify-between px-2">
                    <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Input Barcode /
                        Waybill</span>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase">Ready to
                            Scan</span>
                    </div>
                </div>

                <div class="relative group">
                    <input type="text" wire:model.live.debounce.500ms="barcode" wire:keydown.enter="check"
                        placeholder="SCAN DISINI..."
                        class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl py-8 px-8 text-3xl font-black text-center text-zinc-900 dark:text-white tracking-[0.2em] placeholder-zinc-300 dark:placeholder-zinc-700 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all uppercase shadow-inner"
                        autofocus />

                    <button wire:click="check"
                        class="absolute right-6 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-400 hover:text-indigo-600 hover:border-indigo-600 hover:shadow-lg transition-all active:scale-95">
                        <x-heroicon-o-magnifying-glass class="w-6 h-6" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Result Section -->
        @if ($selectedShipment)
            <div
                class="w-full bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border-2 border-emerald-500/20 rounded-3xl p-8 shadow-2xl space-y-8 animate-bounce-in relative overflow-hidden">
                <!-- Status Badge -->
                <div class="flex justify-center -mt-14 mb-6">
                    @if ($selectedShipment->status->value === 'inspected_by_checker')
                        <div
                            class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-lg shadow-emerald-500/30 flex items-center gap-3">
                            <x-heroicon-s-check-badge class="w-5 h-5" />
                            VERIFIED (SAFE)
                        </div>
                    @else
                        <div
                            class="px-8 py-3 bg-gradient-to-r from-amber-500 to-amber-600 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-lg shadow-amber-500/30 flex items-center gap-3">
                            <x-heroicon-o-clock class="w-5 h-5" />
                            PENDING VERIFICATION
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-6">
                    <div
                        class="w-20 h-20 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-xl shadow-indigo-500/30 rotate-3 shrink-0">
                        <x-heroicon-o-archive-box class="w-10 h-10" />
                    </div>
                    <div class="min-w-0">
                        <p
                            class="text-[10px] font-black text-indigo-500 dark:text-indigo-400 uppercase tracking-widest mb-1">
                            {{ $selectedShipment->waybill_number }}</p>
                        <h2 class="text-2xl font-black text-zinc-900 dark:text-white truncate leading-tight">
                            {{ $selectedShipment->item_description }}</h2>
                    </div>
                </div>

                <div
                    class="grid grid-cols-2 gap-8 border-y border-zinc-100 dark:border-zinc-800/50 py-8 text-[11px] font-bold uppercase">
                    <div class="space-y-1.5">
                        <p class="text-[9px] font-black text-zinc-400 tracking-[0.2em]">Pihak Pengirim</p>
                        <p class="text-zinc-800 dark:text-zinc-200">{{ $selectedShipment->sender_name }}</p>
                        <p class="text-zinc-400 text-[10px] font-medium lowercase italic">
                            {{ $selectedShipment->sender_phone }}</p>
                    </div>
                    <div class="space-y-1.5 text-right">
                        <p class="text-[9px] font-black text-zinc-400 tracking-[0.2em]">Destinasi</p>
                        <p class="text-zinc-800 dark:text-zinc-200">{{ $selectedShipment->destination->city }}</p>
                        <p class="text-indigo-600 dark:text-indigo-400 text-[10px] font-black">
                            {{ $selectedShipment->receiver_name }}</p>
                    </div>
                </div>

                @if ($selectedShipment->schedule)
                    <div
                        class="bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl p-6 flex items-center justify-between border border-zinc-100 dark:border-zinc-800 shadow-inner">
                        <div class="space-y-1">
                            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Armada
                                Bus</span>
                            <p class="text-xl font-black text-zinc-900 dark:text-white tracking-tighter">
                                {{ $selectedShipment->schedule->bus->fleet_code }}</p>
                        </div>
                        <div class="text-right space-y-1">
                            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Validitas</span>
                            <div
                                class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-black text-[10px]">
                                <span>MANIFEST OK</span>
                                <x-heroicon-s-check-circle class="w-4 h-4" />
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex flex-col md:flex-row gap-4 pt-4">
                    <button wire:click="$set('selectedShipment', null)"
                        class="flex-1 py-4 px-6 border border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 font-black rounded-2xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all text-[10px] uppercase tracking-widest active:scale-95">
                        RESET / SCAN ULANG
                    </button>
                    @if ($selectedShipment->status->value !== 'inspected_by_checker')
                        <button wire:click="verify"
                            class="flex-[2] py-4 px-8 bg-emerald-600 text-white font-black rounded-2xl shadow-xl shadow-emerald-600/30 hover:bg-emerald-700 transition-all text-[10px] uppercase tracking-[0.15em] active:scale-95 group">
                            KONFIRMASI LOLOS CEK
                            <x-heroicon-o-shield-check
                                class="inline-block w-5 h-5 ml-2 group-hover:rotate-12 transition-transform" />
                        </button>
                    @endif
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div
                class="w-full flex flex-col items-center justify-center p-20 bg-zinc-50/50 dark:bg-zinc-900/30 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-3xl text-zinc-300 dark:text-zinc-600 group hover:border-indigo-300 dark:hover:border-indigo-500/30 hover:bg-indigo-50/20 dark:hover:bg-indigo-900/10 transition-all duration-500">
                <div class="relative">
                    <x-heroicon-o-qr-code
                        class="w-24 h-24 mb-6 group-hover:text-indigo-600 dark:group-hover:text-indigo-500 transition-colors duration-500" />
                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-indigo-500 rounded-full animate-ping"></div>
                </div>
                <h3
                    class="text-xl font-black uppercase text-zinc-400 dark:text-zinc-500 tracking-widest transition-colors duration-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                    System Ready</h3>
                <p class="text-[10px] font-bold mt-3 text-zinc-400 max-w-xs text-center leading-relaxed">Letakkan
                    barcode di hadapan scanner atau masukkan nomor waybill secara manual.</p>
            </div>
        @endif

        <!-- Warning Disclaimer -->
        <div
            class="p-6 bg-rose-50/50 dark:bg-rose-500/5 border border-rose-100 dark:border-rose-500/10 rounded-3xl flex items-center gap-5 text-rose-700 dark:text-rose-400 shadow-sm">
            <div
                class="w-12 h-12 bg-white dark:bg-rose-950/50 rounded-2xl flex items-center justify-center shadow-sm shrink-0 border border-rose-100 dark:border-rose-900/50">
                <x-heroicon-o-exclamation-circle class="w-7 h-7 text-rose-600 dark:text-rose-500 animate-pulse" />
            </div>
            <p class="text-[10px] font-black uppercase tracking-widest leading-relaxed">
                Peringatan Kerugian: Setiap kargo tidak terdaftar yang ditemukan di lambung bus akan dikenakan sanksi
                denda 10x lipat biaya pengiriman kepada kru bertugas.
            </p>
        </div>
    </div>
</div>
