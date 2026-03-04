@php
    /** @var \App\Livewire\Pages\Bookings\Index $this */
@endphp
<div class="container relative min-h-full pb-20">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-125 h-125 bg-purple-500/5 rounded-full blur-[100px]"></div>
    </div>

    <div class="relative z-10 space-y-8">
        <x-page-header title="Riwayat Booking"
            description="Kelola dan pantau seluruh reservasi tiket penumpang secara real-time."
            icon="heroicon-o-clipboard-document-list" iconGradient="from-indigo-500 to-purple-600"
            iconShadow="shadow-indigo-500/20">
            <a wire:navigate href="{{ route('bookings.create') }}"
                class="btn btn-sm sm:btn-md bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/30 rounded-2xl px-6 font-bold truncate">
                <x-heroicon-o-plus class="w-5 h-5" />
                Reservasi Baru
            </a>
        </x-page-header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-in-up"
            style="animation-delay: 0.1s">
            <div
                class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4">
                    <div
                        class="p-3 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                        <x-heroicon-o-ticket class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Total Booking</p>
                        <p class="text-2xl font-black text-zinc-900 dark:text-white leading-none mt-1">
                            {{ number_format($this->stats['total']) }}</p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div
                        class="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        <x-heroicon-o-check-circle class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Terbayar (PAID)</p>
                        <p class="text-2xl font-black text-zinc-900 dark:text-white leading-none mt-1">
                            {{ number_format($this->stats['paid']) }}</p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-2xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">
                        <x-heroicon-o-clock class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Menunggu (UNPAID)</p>
                        <p class="text-2xl font-black text-zinc-900 dark:text-white leading-none mt-1">
                            {{ number_format($this->stats['unpaid']) }}</p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-2xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400">
                        <x-heroicon-o-banknotes class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Total Pendapatan</p>
                        <p class="text-xl font-black text-zinc-900 dark:text-white leading-none mt-1">Rp
                            {{ number_format($this->stats['total_revenue'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-4 animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="relative flex-1 group">
                <div
                    class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-indigo-500 transition-colors">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Cari Kode Booking, Nama Customer, No. Telp..."
                    class="input input-md w-full pl-12 bg-white/50 dark:bg-zinc-800/50 border-zinc-200 dark:border-zinc-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 shadow-sm font-medium" />
            </div>
            <div class="flex items-center gap-2">
                <select wire:model.live="status"
                    class="select select-bordered bg-white/50 dark:bg-zinc-800/50 border-zinc-200 dark:border-zinc-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 font-bold">
                    <option value="" class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-white">Semua Status
                    </option>
                    @foreach (\App\Enums\PaymentStatus::cases() as $s)
                        <option wire:key="pstatus-{{ $s->value }}" value="{{ $s->value }}"
                            class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-white">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-4xl overflow-hidden shadow-sm animate-fade-in-up"
            style="animation-delay: 0.3s">
            <div class="overflow-x-auto">
                <table class="table table-lg w-full">
                    <thead>
                        <tr class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">Kode
                                Booking</th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                                Customer</th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                                Perjalanan</th>
                            <th
                                class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] text-center">
                                Seats</th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">Total
                                Amount</th>
                            <th
                                class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] text-center">
                                Status</th>
                            <th
                                class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] text-right">
                                AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @if (count($this->bookings) > 0)
                            @foreach ($this->bookings as $b)
                                <tr wire:key="booking-{{ $b->id }}"
                                    class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors border-b border-zinc-100 dark:border-zinc-800/50 last:border-0 group">
                                    <td class="py-6">
                                        <span
                                            class="text-sm font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-tighter">#{{ $b->booking_code }}</span>
                                        <p
                                            class="text-[9px] font-bold text-zinc-400 mt-0.5 truncate uppercase tracking-widest">
                                            {{ $b->created_at->format('d M Y, H:i') }}</p>
                                    </td>
                                    <td class="py-6">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-bold text-zinc-800 dark:text-zinc-200 uppercase tracking-tight">{{ $b->customer_name }}</span>
                                            <span
                                                class="text-[10px] font-medium text-zinc-500 lowercase mt-0.5 italic">{{ $b->customer_phone }}</span>
                                        </div>
                                    </td>
                                    <td class="py-6">
                                        <div class="flex flex-col gap-1 items-start">
                                            <div class="flex items-center gap-2 group/route text-pretty transition-all">
                                                <span
                                                    class="text-xs font-black text-zinc-800 dark:text-white uppercase truncate max-w-30">{{ $b->schedule->route->origin->name }}</span>
                                                <x-heroicon-o-arrow-right
                                                    class="w-3 h-3 text-zinc-300 dark:text-zinc-600 group-hover/route:text-indigo-500" />
                                                <span
                                                    class="text-xs font-black text-zinc-800 dark:text-white uppercase truncate max-w-30 text-pretty">{{ $b->schedule->route->destination->name }}</span>
                                            </div>
                                            <span
                                                class="text-[9px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50/50 dark:bg-indigo-500/10 px-2 py-0.5 rounded-md">{{ $b->schedule->bus->busClass->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-6 text-center">
                                        <div
                                            class="inline-flex size-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 items-center justify-center text-xs font-black text-zinc-600 dark:text-zinc-400 shadow-inner">
                                            {{ $b->total_seats }}
                                        </div>
                                    </td>
                                    <td class="py-6">
                                        <span class="text-sm font-black text-zinc-900 dark:text-white tracking-tight">Rp
                                            {{ number_format($b->total_amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="py-6 text-center">
                                        <span
                                            class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest
                                        @if ($b->payment_status->value === 'paid') text-emerald-600 border border-emerald-500/20
                                        @elseif($b->payment_status->value === 'unpaid')
                                        @else bg-zinc-500/10 @endif
                                    ">
                                            {{ $b->payment_status->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end">
                                            <a wire:navigate href="{{ route('bookings.show', $b->id) }}"
                                                class="btn btn-ghost btn-xs btn-square hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-500 rounded-lg inline-flex items-center justify-center shadow-sm"
                                                title="Lihat Detail">
                                                <x-heroicon-o-eye class="w-5 h-5" />
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="py-24 text-center">
                                    <div class="flex flex-col items-center justify-center opacity-30">
                                        <x-heroicon-o-clipboard-document-list class="w-16 h-16 mb-4" />
                                        <p class="text-sm font-black uppercase tracking-widest">Belum Ada Data
                                            Booking
                                        </p>
                                        <p class="text-xs italic mt-1 leading-relaxed">Gunakan tombol 'Reservasi
                                            Baru'
                                            untuk memulai.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if ($this->bookings->hasPages())
                <div class="p-8 border-t border-zinc-100 dark:border-zinc-800">
                    {{ $this->bookings->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
