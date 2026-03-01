<div class="container relative min-h-screen pb-20">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-emerald-500/5 rounded-full blur-[120px]"></div>
    </div>

    <div class="relative z-10 max-w-4xl mx-auto space-y-8 animate-fade-in-up">
        <header
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-zinc-200 dark:border-zinc-800 pb-8">
            <div class="flex items-center gap-5">
                <a wire:navigate href="{{ route('bookings.index') }}"
                    class="p-4 rounded-2xl bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all text-zinc-500">
                    <x-heroicon-o-arrow-left class="w-6 h-6" />
                </a>
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white uppercase">Detail
                        Reservasi</h1>
                    <p
                        class="text-[10px] font-black uppercase tracking-[.3em] text-zinc-400 mt-1 italic leading-relaxed">
                        Booking ID: #{{ $booking->booking_code }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span
                    class="px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest
                    {{ $booking->payment_status->value === 'paid' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 shadow-lg shadow-emerald-500/10' : '' }}
                    {{ $booking->payment_status->value === 'unpaid' ? 'bg-amber-500/10 text-amber-600 border border-amber-500/20 shadow-lg shadow-amber-500/10' : '' }}
                    {{ $booking->payment_status->value === 'expired' ? 'bg-zinc-500/10 text-zinc-500 border border-zinc-500/20 shadow-lg shadow-zinc-500/10' : '' }}
                ">
                    {{ $booking->payment_status->name }}
                </span>
                <button onclick="window.print()"
                    class="p-2.5 rounded-2xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 transition-all shadow-sm">
                    <x-heroicon-o-printer class="w-5 h-5 text-zinc-500" />
                </button>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Ticket Summary -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Trip Card -->
                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-[40px] p-8 shadow-xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:scale-110 transition-transform">
                        <x-heroicon-o-map-pin class="w-32 h-32" />
                    </div>

                    <div class="relative z-10 flex flex-col gap-8">
                        <div
                            class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-6">
                            <div class="flex items-center gap-4 text-pretty">
                                <div class="p-3 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500">
                                    <x-heroicon-o-truck class="w-6 h-6" />
                                </div>
                                <div>
                                    <h3
                                        class="text-lg font-bold text-zinc-800 dark:text-white uppercase tracking-tight">
                                        {{ $booking->schedule->bus->nickname }}</h3>
                                    <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">
                                        {{ $booking->schedule->bus->busClass->name }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span
                                    class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Berangkat</span>
                                <h4 class="text-lg font-black text-zinc-800 dark:text-white mt-1">
                                    {{ $booking->schedule->departure_date->format('l, d M Y') }}</h4>
                            </div>
                        </div>

                        <div class="flex items-center group/route transition-all">
                            <div class="flex flex-col flex-1 gap-1">
                                <span class="text-xs font-black text-indigo-500 uppercase tracking-widest">Dari</span>
                                <span
                                    class="text-2xl font-black text-zinc-900 dark:text-white uppercase tracking-tight">{{ $booking->boardingLocation->name }}</span>
                                <span
                                    class="text-base font-black text-zinc-400 uppercase">{{ $booking->boardingLocation->city }}</span>
                            </div>
                            <div class="flex flex-col items-center px-8 relative">
                                <div
                                    class="text-[10px] font-black text-zinc-300 dark:text-zinc-600 mb-2 group-hover/route:text-indigo-500 transition-colors uppercase tracking-[.2em] leading-relaxed italic">
                                    {{ $booking->schedule->departure_time->format('H:i') }}</div>
                                <div class="w-24 h-px bg-zinc-200 dark:bg-zinc-700 relative">
                                    <div
                                        class="absolute -top-1 left-1.2 size-2 bg-indigo-500 rounded-full animate-pulse border-2 border-white dark:border-zinc-900">
                                    </div>
                                </div>
                                <div class="text-[9px] font-black text-zinc-400 mt-2 uppercase tracking-widest">EST.
                                    {{ $booking->schedule->arrival_estimate->format('H:i') }}</div>
                            </div>
                            <div class="flex flex-col flex-1 items-end gap-1">
                                <span class="text-xs font-black text-rose-500 uppercase tracking-widest">Ke</span>
                                <span
                                    class="text-2xl font-black text-zinc-900 dark:text-white uppercase tracking-tight">{{ $booking->dropoffLocation->name }}</span>
                                <span
                                    class="text-base font-black text-zinc-400 uppercase">{{ $booking->dropoffLocation->city }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Passenger List -->
                <div class="space-y-4">
                    <h2 class="text-xs font-black uppercase tracking-[.4em] text-zinc-400 px-4">Daftar Tiket & Penumpang
                    </h2>
                    @foreach ($booking->tickets as $ticket)
                        <div
                            class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-[32px] p-6 shadow-sm flex flex-col md:flex-row items-center gap-6 group hover:translate-x-1 transition-all relative overflow-hidden">
                            <div
                                class="absolute left-0 top-0 bottom-0 w-1.5 bg-indigo-500 opacity-20 group-hover:opacity-100 transition-opacity">
                            </div>

                            <div
                                class="size-16 rounded-2xl bg-indigo-600 text-white flex flex-col items-center justify-center shadow-lg shadow-indigo-600/20 group-hover:rotate-6 transition-transform">
                                <span class="text-[9px] font-black uppercase tracking-tighter opacity-40 leading-none">
                                    Kursi </span>
                                <span class="text-2xl font-black mt-0.5 leading-none">{{ $ticket->seat_number }}</span>
                            </div>

                            <div class="flex-1 text-center md:text-left">
                                <h4 class="text-lg font-bold text-zinc-800 dark:text-white uppercase tracking-tight">
                                    {{ $ticket->passenger_name }}</h4>
                                <div class="flex items-center justify-center md:justify-start gap-4 mt-1">
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase flex items-center gap-2">
                                        <x-heroicon-o-ticket class="w-3.5 h-3.5" />
                                        TIKET-{{ $ticket->id }}
                                    </span>
                                    <span
                                        class="text-[10px] font-black text-indigo-500 uppercase flex items-center gap-2">
                                        <x-heroicon-o-check-circle class="w-3.5 h-3.5" />
                                        VALID
                                    </span>
                                </div>
                            </div>

                            <div
                                class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 group-hover:bg-white dark:group-hover:bg-zinc-900 transition-colors">
                                <x-heroicon-o-qr-code
                                    class="w-10 h-10 text-zinc-300 dark:text-zinc-600 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors" />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Payment Sidebar -->
            <aside class="space-y-6">
                <div
                    class="bg-zinc-900 dark:bg-zinc-950 rounded-[40px] p-8 shadow-2xl text-white relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-transparent"></div>
                    <div class="relative z-10 space-y-8">
                        <div>
                            <h3
                                class="text-xs font-black uppercase tracking-[.4em] text-indigo-400 mb-6 border-b border-white/5 pb-4">
                                Info Pembayaran</h3>
                            <div class="flex flex-col gap-2">
                                <span class="text-[10px] font-black uppercase tracking-widest text-white/40">Total
                                    Tagihan</span>
                                <span class="text-3xl font-black tracking-tight">Rp
                                    {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div
                                class="flex items-center justify-between text-xs py-2 border-b border-white/5 uppercase font-bold tracking-widest">
                                <span class="text-white/40">Item</span>
                                <span>{{ $booking->total_seats }} Kursi</span>
                            </div>
                            <div
                                class="flex items-center justify-between text-xs py-2 border-b border-white/5 uppercase font-bold">
                                <span class="text-white/40 tracking-widest">Metode</span>
                                <span
                                    class="tracking-widest">{{ $booking->payment_method ?: 'TRANSFER / AGEN' }}</span>
                            </div>
                        </div>

                        @if ($booking->payment_status->value === 'unpaid')
                            <div class="p-6 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-sm">
                                <div class="flex items-start gap-4">
                                    <x-heroicon-o-clock class="w-5 h-5 text-amber-500" />
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-amber-500">Batas
                                            Waktu</p>
                                        <p class="text-sm font-bold mt-1">
                                            {{ $booking->expired_at->format('d M, H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <button
                            class="btn btn-block btn-lg bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-xl shadow-indigo-600/30 rounded-3xl h-16 font-black uppercase tracking-widest text-xs group">
                            Bayar Sekarang
                            <x-heroicon-o-credit-card class="w-5 h-5 ml-2 group-hover:scale-110 transition-transform" />
                        </button>
                    </div>
                </div>

                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-[40px] p-8 shadow-sm space-y-6">
                    <h3
                        class="text-[10px] font-black uppercase tracking-[.4em] text-zinc-400 border-b border-zinc-50 dark:border-zinc-800 pb-4">
                        Kontak Pemesan</h3>
                    <div class="space-y-4">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Nama
                                Pemesan</span>
                            <span
                                class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $booking->customer_name }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="text-[9px] font-black uppercase tracking-widest text-zinc-400 text-pretty">WhatsApp
                                / Telp</span>
                            <span
                                class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $booking->customer_phone }}</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
