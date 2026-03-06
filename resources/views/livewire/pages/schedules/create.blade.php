@php
    /** @var \App\Livewire\Pages\Schedules\Create $this */
@endphp
<div class="relative min-h-full">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-150 h-150 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
        <div class="absolute bottom-0 left-1/4 w-125 h-125 bg-sky-500/10 dark:bg-sky-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8">
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/50 pb-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-linear-to-br from-indigo-500 to-sky-600 shadow-lg shadow-indigo-500/30">
                            <x-heroicon-o-calendar-days class="w-5 h-5 text-white" />
                        </div>
                        Terbitkan Jadwal Keberangkatan Baru
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Pilih armada, rute, dan tugaskan kru terbaik untuk keberangkatan kali ini.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a wire:navigate href="{{ route('schedules.index') }}"
                        class="btn btn-sm bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl transition-all">
                        <x-heroicon-o-arrow-left class="w-4 h-4" />
                        Kembali Ke Daftar
                    </a>
                </div>
            </header>
        </div>

        <form wire:submit="saveSchedule" class="space-y-8 pb-20">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Info Section -->
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm animate-fade-in-up">
                        <h4
                            class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-6 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                            <x-heroicon-o-information-circle class="w-4 h-4" />
                            Informasi Utama Operasional
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-control w-full">
                                <x-form-select label="Trayek Rute Utama*" wire:model.live="form.route_id">
                                    <option value="">-- Pilih Rute --</option>
                                    @foreach ($this->routes as $route)
                                        <option wire:key="opt-route-{{ $route->id }}" value="{{ $route->id }}">
                                            {{ $route->name }} ({{ $route->route_code }})
                                        </option>
                                    @endforeach
                                </x-form-select>
                                <p class="mt-2 text-[10px] text-amber-600 dark:text-amber-400 font-medium">*Mengubah
                                    rute akan mereset estimasi stop.</p>
                            </div>

                            <x-form-select label="Armada Bus" wire:model="form.bus_id">
                                <option value="">-- Pilih Bus --</option>
                                @foreach ($this->buses as $bus)
                                    <option wire:key="opt-bus-{{ $bus->id }}" value="{{ $bus->id }}">
                                        {{ $bus->name }} ({{ $bus->plate_number }})
                                    </option>
                                @endforeach
                            </x-form-select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <x-form-input label="Tanggal Berangkat*" wire:model="form.departure_date" type="date" />
                            <x-form-input label="Jam Berangkat*" wire:model.live="form.departure_time"
                                type="datetime-local" />
                            <x-form-input label="Estimasi Sampai*" wire:model="form.arrival_estimate"
                                type="datetime-local" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <x-form-input label="Harga Dasar (Tiket)" wire:model="form.base_price" type="number"
                                placeholder="0" step="1" class="font-bold text-zinc-800">
                                <x-slot:prefix>Rp</x-slot:prefix>
                            </x-form-input>
                            <x-form-select label="Jenis Perjalanan" wire:model="form.trip_type" class="font-bold">
                                <option value="revenue">Operasional (Revenue)</option>
                                <option value="relocation">Relokasi (Kirim Bus)</option>
                            </x-form-select>
                        </div>
                    </div>

                    <!-- Crews Section -->
                    <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm animate-fade-in-up"
                        style="animation-delay: 0.1s">
                        <div
                            class="flex items-center justify-between mb-6 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                            <h4
                                class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2">
                                <x-heroicon-o-users class="w-4 h-4" />
                                Penugasan Kru Operasional
                            </h4>
                            <button type="button" wire:click="addCrew"
                                class="btn btn-xs bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 border-0 rounded-lg shadow-sm">
                                <x-heroicon-o-plus class="w-4 h-4" /> Tambah Kru
                            </button>
                        </div>

                        <div class="space-y-4">
                            @if (count($form->crews) > 0)
                                @foreach ($form->crews as $index => $crew)
                                    <div wire:key="crew-{{ $index }}"
                                        class="bg-zinc-50/50 dark:bg-zinc-800/20 border border-zinc-100 dark:border-zinc-800 p-4 rounded-2xl relative group">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="form-control">
                                                <label class="label pb-0"><span
                                                        class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">Pilih
                                                        Personel*</span></label>
                                                <select wire:model="form.crews.{{ $index }}.crew_id"
                                                    class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-semibold">
                                                    <option value="">-- Pilih Kru --</option>
                                                    @foreach ($this->crews as $c)
                                                        <option
                                                            wire:key="opt-crew-{{ $index }}-{{ $c->id }}"
                                                            value="{{ $c->id }}">{{ $c->name }}
                                                            ({{ $c->nik }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('form.crews.' . $index . '.crew_id')
                                                    <span
                                                        class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-control">
                                                <label class="label pb-0"><span
                                                        class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">Jabatan
                                                        Penugasan*</span></label>
                                                <select
                                                    wire:model="form.crews.{{ $index }}.assigned_position_id"
                                                    class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-semibold">
                                                    <option value="">-- Pilih Jabatan --</option>
                                                    @foreach ($this->positions as $pos)
                                                        <option
                                                            wire:key="opt-pos-{{ $index }}-{{ $pos->id }}"
                                                            value="{{ $pos->id }}">{{ $pos->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('form.crews.' . $index . '.assigned_position_id')
                                                    <span
                                                        class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                            <div class="form-control">
                                                <label class="label pb-0"><span
                                                        class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">Lokasi
                                                        Naik (Boarding)*</span></label>
                                                <select
                                                    wire:model="form.crews.{{ $index }}.boarding_location_id"
                                                    class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-semibold">
                                                    <option value="">-- Pilih Agen --</option>
                                                    @foreach ($this->agents->groupBy(fn($a) => $a->location->province ?? 'LAINNYA') as $province => $provAgents)
                                                        <optgroup label="{{ $province }}">
                                                            @foreach ($provAgents as $agent)
                                                                <option
                                                                    wire:key="opt-boarding-{{ $index }}-{{ $agent->id }}"
                                                                    value="{{ $agent->id }}">{{ $agent->name }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-control">
                                                <label class="label pb-0"><span
                                                        class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">Lokasi
                                                        Turun (Dropoff)*</span></label>
                                                <select
                                                    wire:model="form.crews.{{ $index }}.dropoff_location_id"
                                                    class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-semibold">
                                                    <option value="">-- Pilih Agen --</option>
                                                    @foreach ($this->agents->groupBy(fn($a) => $a->location->province ?? 'LAINNYA') as $province => $provAgents)
                                                        <optgroup label="{{ $province }}">
                                                            @foreach ($provAgents as $agent)
                                                                <option
                                                                    wire:key="opt-dropoff-{{ $index }}-{{ $agent->id }}"
                                                                    value="{{ $agent->id }}">{{ $agent->name }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <button type="button" wire:click="removeCrew({{ $index }})"
                                            class="absolute -top-2 -right-2 btn btn-circle btn-xs bg-red-500 text-white border-0 shadow-lg hover:bg-red-600 transition-all opacity-0 group-hover:opacity-100">
                                            <x-heroicon-o-x-mark class="w-3 h-3" />
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div
                                    class="py-8 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-3xl">
                                    <p class="text-xs font-semibold text-zinc-400">Belum ada kru yang ditugaskan.</p>
                                    <button type="button" wire:click="addCrew"
                                        class="mt-4 text-indigo-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-widest hover:underline">Tugaskan
                                        Sekarang</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Stops Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-indigo-600 dark:bg-indigo-900/60 rounded-3xl p-6 shadow-xl shadow-indigo-600/20 text-white animate-fade-in-up"
                        style="animation-delay: 0.2s">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-white/20 rounded-xl">
                                <x-heroicon-o-clock class="w-5 h-5" />
                            </div>
                            <h4 class="text-sm font-bold tracking-tight">Estimasi Waktu Rute</h4>
                        </div>

                        <div class="space-y-6 relative ml-3 border-l-2 border-white/20 pb-4">
                            @if (count($form->stops) > 0 || $routeOriginName)

                                {{-- ORIGIN --}}
                                @if ($routeOriginName)
                                    <div class="relative pl-6">
                                        <div
                                            class="absolute -left-[11px] top-0 size-5 bg-emerald-400 rounded-full flex items-center justify-center border-4 border-indigo-600 dark:border-indigo-900">
                                            <div class="size-1.5 bg-white rounded-full"></div>
                                        </div>
                                        <div
                                            class="bg-white/15 backdrop-blur-md rounded-2xl p-4 border border-white/10">
                                            <p
                                                class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-1">
                                                🛫 KEBERANGKATAN</p>
                                            <p class="text-xs font-bold text-white mb-2">{{ $routeOriginName }}</p>
                                            <div class="text-sm font-bold text-emerald-200 tracking-widest">
                                                {{ $form->departure_time ? \Carbon\Carbon::parse($form->departure_time)->format('H:i') : '--:--' }}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- STOPS --}}
                                @foreach ($form->stops as $index => $stop)
                                    <div wire:key="stop-{{ $index }}" class="relative pl-6">
                                        <div
                                            class="absolute -left-[11px] top-0 size-5 bg-white rounded-full flex items-center justify-center border-4 border-indigo-600 dark:border-indigo-900 group">
                                            <div
                                                class="size-1.5 bg-indigo-600 rounded-full group-hover:scale-150 transition-all">
                                            </div>
                                        </div>
                                        <div
                                            class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 hover:bg-white/20 transition-all cursor-default">
                                            <p
                                                class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-1">
                                                {{ $index + 1 }}. {{ $stop['location_name'] }}</p>
                                            <input type="time"
                                                wire:model="form.stops.{{ $index }}.estimated_time"
                                                class="input input-xs bg-white/10 border-white/20 text-white w-full font-bold focus:bg-white focus:text-zinc-900 transition-all" />
                                            @error('form.stops.' . $index . '.estimated_time')
                                                <span
                                                    class="text-[9px] text-red-200 font-bold mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach

                                {{-- DESTINATION --}}
                                @if ($routeDestinationName)
                                    <div class="relative pl-6">
                                        <div
                                            class="absolute -left-[11px] top-0 size-5 bg-red-400 rounded-full flex items-center justify-center border-4 border-indigo-600 dark:border-indigo-900">
                                            <div class="size-1.5 bg-white rounded-full"></div>
                                        </div>
                                        <div
                                            class="bg-white/15 backdrop-blur-md rounded-2xl p-4 border border-white/10">
                                            <p
                                                class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-1">
                                                🏁 TUJUAN AKHIR</p>
                                            <p class="text-xs font-bold text-white mb-2">{{ $routeDestinationName }}
                                            </p>
                                            <div class="text-sm font-bold text-red-200 tracking-widest">
                                                {{ $form->arrival_estimate ? \Carbon\Carbon::parse($form->arrival_estimate)->format('H:i') : '--:--' }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div
                                    class="px-4 py-8 text-center bg-white/5 rounded-2xl border border-dashed border-white/10">
                                    <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest italic">
                                        Pilih rute untuk melihat detail estimasi per stop.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Stats --}}
                        @if (count($form->stops) > 0 || $routeOriginName)
                            @php
                                $stopCount = count($form->stops);
                                $etaJam = $totalRouteKm ? floor($totalRouteKm / 60) : null;
                                $etaMenit = $totalRouteKm ? round(fmod($totalRouteKm / 60, 1) * 60) : null;
                                $etaLabel =
                                    $etaJam !== null ? ($etaJam > 0 ? "{$etaJam}j " : '') . "{$etaMenit}m" : '—';
                            @endphp
                            <div class="mt-5 pt-4 border-t border-white/20 grid grid-cols-3 gap-2">
                                <div class="bg-white/10 rounded-xl p-3 text-center">
                                    <p class="text-lg font-black">{{ $stopCount }}</p>
                                    <p class="text-[10px] text-indigo-200 uppercase tracking-wide">Titik Stop</p>
                                </div>
                                <div class="bg-white/10 rounded-xl p-3 text-center">
                                    <p class="text-lg font-black">{{ $totalRouteKm ?: '—' }}</p>
                                    <p class="text-[10px] text-indigo-200 uppercase tracking-wide">KM Total</p>
                                </div>
                                <div class="bg-white/10 rounded-xl p-3 text-center">
                                    <p class="text-lg font-black">{{ $etaLabel }}</p>
                                    <p class="text-[10px] text-indigo-200 uppercase tracking-wide">Est. Waktu</p>
                                </div>
                            </div>
                            <p class="text-[9px] text-indigo-300/60 mt-2 text-center">*estimasi @ ~60 km/jam rata-rata
                            </p>
                        @endif

                        <!-- Submit Button -->
                        <div class="mt-5">
                            <button type="submit" wire:loading.attr="disabled"
                                class="btn w-full bg-white text-indigo-700 hover:bg-indigo-50 border-0 h-11 rounded-2xl font-black tracking-tight text-sm transition-all group overflow-hidden relative shadow-lg">
                                <div
                                    class="absolute inset-0 bg-linear-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer">
                                </div>
                                <span class="flex items-center justify-center gap-2 relative z-10">
                                    <x-heroicon-o-check-circle wire:loading.remove wire:target="saveSchedule"
                                        class="w-5 h-5" />
                                    <span wire:loading wire:target="saveSchedule"
                                        class="loading loading-spinner loading-sm"></span>
                                    Terbitkan JADWAL
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
