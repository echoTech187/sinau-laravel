<div class="container relative min-h-screen pb-20">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-indigo-500/5 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-rose-500/5 rounded-full blur-[100px]"></div>
    </div>

    <div class="relative z-10 max-w-6xl mx-auto space-y-8">
        <header
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-zinc-200 dark:border-zinc-800 pb-8 animate-fade-in-up">
            <div class="flex items-center gap-5">
                <div
                    class="p-4 rounded-3xl bg-gradient-to-br from-indigo-500 to-blue-600 shadow-xl shadow-indigo-500/20 text-white transform hover:rotate-6 transition-transform">
                    <x-heroicon-o-ticket class="w-8 h-8" />
                </div>
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white uppercase">Reservasi
                        Tiket</h1>
                    <p class="mt-1 text-sm font-medium text-zinc-500 dark:text-zinc-400 leading-relaxed italic">
                        Reservasi perjalanan Anda dengan cepat, mudah, dan transparan.</p>
                </div>
            </div>

            <div
                class="flex items-center bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md rounded-2xl border border-zinc-200 dark:border-zinc-800 p-1.5 shadow-sm">
                @for ($i = 1; $i <= 4; $i++)
                    <div
                        class="flex items-center gap-2 px-4 py-2 rounded-xl {{ $step == $i ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-zinc-400' }} transition-all">
                        <span class="text-xs font-black">{{ $i }}</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest hidden lg:block">
                            @if ($i == 1)
                                Cari
                            @elseif($i == 2)
                                Jadwal
                            @elseif($i == 3)
                                Kursi
                            @else
                                Detail
                            @endif
                        </span>
                    </div>
                    @if ($i < 4)
                        <x-heroicon-o-chevron-right class="w-3 h-3 text-zinc-300 dark:text-zinc-700" />
                    @endif
                @endfor
            </div>
        </header>

        <!-- STEP 1 & 2: Search & Select Schedule -->
        @if ($step <= 2)
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Search Panel -->
                <aside class="lg:col-span-1 space-y-6 animate-fade-in-up" style="animation-delay: 0.1s">
                    <div
                        class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-8 shadow-sm">
                        <h2
                            class="text-xs font-black uppercase tracking-[.2em] text-indigo-500 mb-6 flex items-center gap-2">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                            Cari Perjalanan
                        </h2>

                        <div class="space-y-5">
                            <div class="form-control w-full">
                                <label class="label pb-0">
                                    <span
                                        class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                        Berangkat Dari
                                    </span>
                                </label>
                                <select wire:model.live="origin_id"
                                    class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                                    <option value="">Pilih Asal</option>
                                    @foreach ($this->locations as $loc)
                                        <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->city }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex justify-center relative -my-4 z-10">
                                <button
                                    class="p-2 rounded-xl bg-indigo-600 text-white shadow-lg hover:rotate-180 transition-transform duration-500 border-4 border-white dark:border-zinc-900">
                                    <x-heroicon-o-arrows-up-down class="w-4 h-4" />
                                </button>
                            </div>

                            <div class="form-control w-full">
                                <label class="label pb-0">
                                    <span
                                        class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                        Tujuan Ke
                                    </span>
                                </label>
                                <select wire:model.live="destination_id"
                                    class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                                    <option value="">Pilih Tujuan</option>
                                    @foreach ($this->locations as $loc)
                                        <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->city }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-control w-full">
                                <label class="label pb-0">
                                    <span
                                        class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                        Tanggal Perjalanan
                                    </span>
                                </label>
                                <input type="date" wire:model.live="departure_date"
                                    class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all" />
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Results Panel -->
                <main class="lg:col-span-3 space-y-6 animate-fade-in-up" style="animation-delay: 0.2s">
                    @forelse($this->schedules as $s)
                        <div wire:key="schedule-{{ $s->id }}"
                            class="group bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-[32px] p-6 shadow-sm hover:shadow-2xl hover:shadow-indigo-500/10 transition-all hover:-translate-y-1 relative overflow-hidden">
                            <!-- Background Accent -->
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/5 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-indigo-500/10 transition-colors">
                            </div>

                            <div class="flex flex-col md:flex-row gap-8 items-center relative z-10">
                                <!-- Departure Info -->
                                <div
                                    class="flex flex-col items-center justify-center min-w-[120px] text-center px-6 border-r border-zinc-100 dark:border-zinc-800">
                                    <span
                                        class="text-3xl font-black text-zinc-900 dark:text-white">{{ $s->departure_time->format('H:i') }}</span>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mt-1">{{ $s->departure_date->format('D, d M') }}</span>
                                </div>

                                <!-- Route Timeline -->
                                <div class="flex-1 flex flex-col gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="flex flex-col flex-1">
                                            <span
                                                class="text-xs font-black text-indigo-500 uppercase tracking-widest">Berangkat</span>
                                            <span
                                                class="text-lg font-bold text-zinc-800 dark:text-white truncate">{{ $s->route->origin->name }}</span>
                                        </div>
                                        <div class="flex flex-col items-center px-4">
                                            <div class="w-16 h-px bg-zinc-200 dark:bg-zinc-700 relative">
                                                <div
                                                    class="absolute -top-1 blur-[1px] font-bold text-pretty left-1/2 -translate-x-1/2 size-2 bg-indigo-500 rounded-full animate-ping">
                                                </div>
                                                <div
                                                    class="absolute -top-1 left-1/2 -translate-x-1/2 size-2 bg-indigo-600 rounded-full">
                                                </div>
                                            </div>
                                            <span
                                                class="text-[9px] font-black text-zinc-400 mt-2 uppercase">Langsung</span>
                                        </div>
                                        <div class="flex flex-col flex-1 items-end">
                                            <span
                                                class="text-xs font-black text-rose-500 uppercase tracking-widest text-pretty">Tujuan</span>
                                            <span
                                                class="text-lg font-bold text-zinc-800 dark:text-white truncate">{{ $s->route->destination->name }}</span>
                                        </div>
                                    </div>

                                    <div
                                        class="flex flex-wrap gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800/50">
                                        <span
                                            class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-full text-[10px] font-bold text-zinc-600 dark:text-zinc-400 flex items-center gap-2">
                                            <x-heroicon-o-truck class="w-3 h-3" />
                                            {{ $s->bus->nickname }} ({{ $s->bus->busClass->name }})
                                        </span>
                                        @foreach ($s->bus->busClass->facilities->take(5) as $f)
                                            <span
                                                class="px-2 py-1 bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800 rounded-lg text-zinc-400"
                                                title="{{ $f->name }}">
                                                <x-dynamic-component :component="$f->icon" class="w-3.5 h-3.5" />
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Price & Action -->
                                <div
                                    class="flex flex-col items-center md:items-end justify-center min-w-[180px] space-y-4">
                                    <div class="text-right">
                                        <span
                                            class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Mulai
                                            Dari</span>
                                        <div
                                            class="text-2xl font-black text-indigo-600 dark:text-indigo-400 leading-none mt-1">
                                            Rp {{ number_format($s->base_price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <button wire:click="selectSchedule({{ $s->id }})"
                                        class="btn btn-block bg-zinc-900 hover:bg-zinc-950 text-white border-0 rounded-2xl font-black uppercase tracking-widest text-[10px] py-4 h-auto min-h-0 shadow-lg shadow-zinc-900/10">
                                        Pilih Kursi
                                        <x-heroicon-o-arrow-right class="w-4 h-4 ml-2" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="flex flex-col items-center justify-center py-24 bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-[40px] text-center group">
                            <div
                                class="p-6 rounded-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 mb-6 group-hover:scale-110 transition-transform">
                                <x-heroicon-o-calendar class="w-12 h-12 opacity-20" />
                            </div>
                            <h3 class="text-xl font-black text-zinc-800 dark:text-white uppercase tracking-tight">Tidak
                                Ada Jadwal</h3>
                            <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1 max-w-xs leading-relaxed italic">
                                Silakan ganti rute atau pilih tanggal keberangkatan yang lain.</p>
                        </div>
                    @endforelse
                </main>
            </div>

            <!-- STEP 3: Seat Selection -->
        @elseif($step == 3)
            <div class="animate-fade-in-up">
                <div class="flex flex-col lg:flex-row gap-8 items-start">
                    <!-- Left: Seat Grid -->
                    <div
                        class="flex-1 bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-[40px] p-10 shadow-xl overflow-auto text-pretty">
                        <div class="flex items-center justify-between mb-10">
                            <div>
                                <h2 class="text-2xl font-black text-zinc-900 dark:text-white uppercase tracking-tight">
                                    Pilih No. Kursi</h2>
                                <p class="text-xs font-medium text-zinc-400 lowercase mt-1 italic leading-relaxed">Klik
                                    pada kursi yang tersedia untuk memilih.</p>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex items-center gap-2">
                                    <div class="size-3 rounded-full bg-zinc-200 dark:bg-zinc-700 shadow-inner"></div>
                                    <span
                                        class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">Tersedia</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="size-3 rounded-full bg-rose-500 shadow-md"></div>
                                    <span
                                        class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">Terisi</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="size-3 rounded-full bg-indigo-500 shadow-lg animate-pulse"></div>
                                    <span
                                        class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">Pilihanmu</span>
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex justify-center p-12 bg-zinc-50 dark:bg-zinc-950/50 rounded-[48px] border border-zinc-100 dark:border-zinc-900 shadow-inner perspective-1000">
                            <div class="inline-grid gap-3 p-8 bg-white dark:bg-zinc-900 rounded-[40px] border border-zinc-200 dark:border-zinc-800 shadow-2xl origin-top transform hover:rotate-x-1 group transition-transform duration-500"
                                style="grid-template-columns: repeat({{ $this->selectedSchedule->bus->seatLayout->grid_columns }}, minmax(0, 1fr));">

                                @foreach ($this->selectedSchedule->bus->seatLayout->layout_mapping as $seat)
                                    @php
                                        $seatNum = $seat['seat_number'] ?? ($seat['label'] ?? null);
                                        $isTaken =
                                            $seat['type'] === 'seat' && $seatNum
                                                ? in_array($seatNum, $this->takenSeats)
                                                : false;
                                        $isSelected =
                                            $seat['type'] === 'seat' && $seatNum
                                                ? in_array($seatNum, $this->selected_seats)
                                                : false;
                                    @endphp
                                    <div @if ($seat['type'] === 'seat' && $seatNum) wire:click="toggleSeat('{{ $seatNum }}')" @endif
                                        class="size-14 sm:size-16 rounded-xl flex flex-col items-center justify-center cursor-pointer transition-all hover:scale-110 active:scale-90 border-2 
                                            {{ $seat['type'] === 'seat'
                                                ? ($isTaken
                                                    ? 'bg-zinc-100 border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700 cursor-not-allowed text-zinc-400'
                                                    : ($isSelected
                                                        ? 'bg-indigo-600 border-indigo-500 text-white shadow-xl shadow-indigo-600/30'
                                                        : 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 hover:border-indigo-400 text-zinc-600 dark:text-zinc-400 shadow-sm'))
                                                : 'opacity-0 pointer-events-none' }}">

                                        @if ($seat['type'] === 'seat')
                                            <span
                                                class="text-[9px] font-black uppercase tracking-tighter opacity-40 leading-none">
                                                Kursi </span>
                                            <span
                                                class="text-xl font-black mt-0.5 leading-none">{{ $seatNum }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Right: Summary -->
                    <aside class="w-full lg:w-[360px] space-y-6">
                        <div
                            class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-[40px] p-8 shadow-xl">
                            <h2
                                class="text-xs font-black uppercase tracking-[.3em] text-indigo-500 mb-8 border-b border-zinc-100 dark:border-zinc-800 pb-4">
                                Ringkasan Kursi</h2>

                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Kursi
                                        Terpilih</span>
                                    <span
                                        class="text-lg font-black text-zinc-900 dark:text-white">{{ count($this->selected_seats) }}</span>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($this->selected_seats as $seat)
                                        <span
                                            class="px-4 py-2 bg-indigo-600 text-white text-xs font-black rounded-xl shadow-md transform hover:rotate-3 transition-transform">{{ $seat }}</span>
                                    @empty
                                        <span class="text-xs italic text-zinc-400 lowercase leading-relaxed">Belum ada
                                            kursi dipilih...</span>
                                    @endforelse
                                </div>

                                <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800">
                                    <div class="flex items-center justify-between mb-8">
                                        <span
                                            class="text-xs font-bold text-zinc-400 uppercase tracking-widest text-pretty">Total
                                            Estimasi</span>
                                        <span
                                            class="text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">
                                            Rp
                                            {{ number_format(collect($this->passengers)->sum('price'), 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <button wire:click="goToStep4"
                                        class="btn btn-block btn-lg bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-2xl shadow-indigo-600/30 rounded-3xl h-16 font-black uppercase tracking-widest text-xs group">
                                        Lanjutkan Detail
                                        <x-heroicon-o-chevron-right
                                            class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" />
                                    </button>
                                    <button wire:click="$set('step', 2)"
                                        class="btn btn-block btn-ghost mt-4 font-black uppercase tracking-widest text-[10px] text-zinc-400 opacity-60 hover:opacity-100 tracking-widest transition-all">Kembali</button>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>

            <!-- STEP 4: Passenger Details -->
        @elseif($step == 4)
            <div class="animate-fade-in-up max-w-4xl mx-auto">
                <form wire:submit="saveBooking" class="space-y-8">
                    <!-- Customer Contact -->
                    <div
                        class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-[40px] p-10 shadow-xl space-y-8">
                        <h2
                            class="text-sm font-black uppercase tracking-[.4em] text-indigo-500 flex items-center gap-3">
                            <x-heroicon-o-user class="w-5 h-5" />
                            Kontak Pemesan
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="form-control w-full">
                                <label class="label pb-0">
                                    <span
                                        class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                        Nama Customer
                                    </span>
                                </label>
                                <input type="text" wire:model="customer_name"
                                    placeholder="Masukan nama lengkap..."
                                    class="input input-bordered h-14 bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all" />
                                @error('customer_name')
                                    <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-control w-full">
                                <label class="label pb-0">
                                    <span
                                        class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                        No. WhatsApp / Telp
                                    </span>
                                </label>
                                <input type="text" wire:model="customer_phone" placeholder="08..."
                                    class="input input-bordered h-14 bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all" />
                                @error('customer_phone')
                                    <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Passenger Details -->
                    <div
                        class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-[40px] p-10 shadow-xl space-y-10">
                        <h2 class="text-sm font-black uppercase tracking-[.4em] text-rose-500 flex items-center gap-3">
                            <x-heroicon-o-users class="w-5 h-5 text-pretty" />
                            Data Penumpang
                        </h2>

                        <div class="space-y-6">
                            @foreach ($selected_seats as $seat)
                                <div
                                    class="flex flex-col md:flex-row gap-6 p-8 rounded-[32px] bg-zinc-50 dark:bg-zinc-950/30 border border-zinc-100 dark:border-zinc-900 group/item hover:border-indigo-500/30 transition-all shadow-sm">
                                    <div class="flex items-center gap-5 md:min-w-[140px]">
                                        <div
                                            class="size-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-xl font-black shadow-lg shadow-indigo-600/30 transform group-hover/item:rotate-12 transition-all">
                                            {{ $seat }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span
                                                class="text-[10px] font-black uppercase tracking-widest text-zinc-400">No.
                                                Kursi</span>
                                            <span
                                                class="text-xs font-bold text-zinc-500 tracking-widest text-pretty">AVAILABLE</span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <label class="label pb-0">
                                            <span
                                                class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                                Nama Penumpang
                                            </span>
                                        </label>
                                        <input type="text" wire:model="passengers.{{ $seat }}.name"
                                            placeholder="Sesuai KTP/Identitas..."
                                            class="input input-bordered h-12 w-full bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all" />
                                    </div>
                                    <div class="md:min-w-[160px] flex flex-col items-end justify-center">
                                        <span
                                            class="text-[9px] font-black uppercase tracking-widest text-zinc-400 text-pretty">Harga
                                            Tiket</span>
                                        <span class="text-base font-black text-indigo-600 dark:text-indigo-400">Rp
                                            {{ number_format($passengers[$seat]['price'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex gap-4 pt-12">
                            <button type="button" wire:click="$set('step', 3)"
                                class="btn btn-ghost rounded-2xl font-black uppercase tracking-[.3em] text-[10px] text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 shadow-sm border border-transparent hover:border-zinc-200 px-8">Koreksi
                                Kursi</button>
                            <button type="submit"
                                class="btn flex-1 bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-2xl shadow-indigo-600/30 rounded-3xl h-20 font-black uppercase tracking-widest text-lg group overflow-hidden relative">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer">
                                </div>
                                <span wire:loading.remove wire:target="saveBooking"
                                    class="flex items-center gap-3 relative z-10">
                                    <x-heroicon-o-check-circle class="w-7 h-7" />
                                    KONFIRMASI & TERBITKAN TIKET
                                </span>
                                <span wire:loading wire:target="saveBooking"
                                    class="loading loading-spinner loading-lg relative z-10"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
