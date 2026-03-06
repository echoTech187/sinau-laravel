@php
    /** @var \App\Livewire\Pages\Routes\Edit $this */
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
                            <x-heroicon-o-pencil-square class="w-5 h-5 text-white" />
                        </div>
                        Ubah Master Rute: {{ $route->name }}
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Sesuaikan titik lokasi utama atau lakukan modifikasi pada jalur perjalanan (Stops).
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a wire:navigate href="{{ route('routes.index') }}"
                        class="btn btn-sm bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl transition-all">
                        <x-heroicon-o-arrow-left class="w-4 h-4" />
                        Kembali Ke Daftar
                    </a>
                </div>
            </header>
        </div>

        <form id="routeForm" wire:submit="saveRoute" class="space-y-6">
            <!-- Parameter Induk Rute -->
            <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm animate-fade-in-up"
                style="animation-delay: 0.1s">
                <h4
                    class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-6 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                    <x-heroicon-o-map class="w-4 h-4" />
                    Parameter Induk Rute
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="form-control w-full">
                        <label class="label pt-0 pb-1.5">
                            <span class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">
                                Kode Operasional Tanda Rute*
                            </span>
                        </label>
                        <input type="text" wire:model="form.route_code"
                            class="input input-bordered uppercase bg-slate-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-inner text-zinc-400 cursor-not-allowed font-black tracking-widest"
                            disabled />
                    </div>
                    <x-form-input label="Nama Singkat Rute / Jurusan*" wire:model="form.name"
                        placeholder="Surabaya - Malang PP" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-form-select label="Terminal Keberangkatan*" wire:model.live="form.origin_agent_id"
                        class="border-emerald-200 dark:border-emerald-900/50 focus:ring-emerald-500">
                        <option value="">-- Pilih Agen Asal --</option>
                        @foreach ($this->agents->groupBy(fn($a) => $a->location->province ?? 'LAINNYA') as $province => $provAgents)
                            <optgroup label="{{ $province }}">
                                @foreach ($provAgents as $agent)
                                    <option wire:key="agt-org-{{ $agent->id }}" value="{{ $agent->id }}">
                                        {{ $agent->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </x-form-select>

                    <x-form-select label="Destinasi Akhir Penurunan*" wire:model.live="form.destination_agent_id"
                        class="border-red-200 dark:border-red-900/50 focus:ring-red-500">
                        <option value="">-- Pilih Agen Tujuan --</option>
                        @foreach ($this->agents->groupBy(fn($a) => $a->location->province ?? 'LAINNYA') as $province => $provAgents)
                            <optgroup label="{{ $province }}">
                                @foreach ($provAgents as $agent)
                                    <option wire:key="agt-dest-{{ $agent->id }}" value="{{ $agent->id }}">
                                        {{ $agent->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </x-form-select>

                    <div class="form-control w-full">
                        <x-form-input label="Estimasi Jarak Tempuh (KM)" wire:model="form.distance_km" type="number"
                            placeholder="Cth: 155" />
                        <div wire:loading wire:target="updatedFormOriginAgentId,updatedFormDestinationAgentId"
                            class="text-[10px] text-indigo-500 mt-1 flex items-center gap-1">
                            <span class="loading loading-xs loading-spinner"></span> Menghitung estimasi...
                        </div>
                        <p class="text-[10px] text-zinc-400 mt-1 flex items-center gap-1">
                            <x-heroicon-o-cpu-chip class="w-3 h-3" />
                            Auto-estimasi dari koordinat agen (Haversine ×1.35)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stops + Preview layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up" style="animation-delay: 0.2s">

                <!-- Left: Stops Editor (2/3 width) -->
                <div class="lg:col-span-2 order-2 lg:order-1">
                    <div
                        class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm h-full">
                        <div
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                            <div>
                                <h4
                                    class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2">
                                    <x-heroicon-o-bars-3-center-left class="w-4 h-4" />
                                    Kalkulasi Trayek &amp; Titik Pemberhentian Antara (Stops)
                                </h4>
                                <p class="text-[10px] text-zinc-400 mt-1">Lakukan penyisipan, penghapusan, atau
                                    pergantian
                                    aturan pada agen/pool yang telah dikonfigurasi sebelumnya.</p>
                            </div>
                            <button type="button" wire:click="addStop"
                                class="btn btn-sm bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-indigo-600 dark:text-indigo-400 border-0 rounded-lg shadow-sm shrink-0">
                                <x-heroicon-o-plus class="w-4 h-4" /> Tambah Titik Baru
                            </button>
                        </div>

                        <div class="space-y-4">
                            @if (count($form->stops) > 0)
                                @foreach ($form->stops as $index => $stop)
                                    <div wire:key="stop-{{ $index }}"
                                        class="flex flex-col md:flex-row items-center gap-4 bg-zinc-50/50 dark:bg-zinc-800/20 border border-zinc-200 dark:border-zinc-700/50 p-4 rounded-xl group relative transition-all hover:bg-white dark:hover:bg-zinc-800">
                                        <div
                                            class="w-8 h-8 shrink-0 bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-400 rounded-full flex items-center justify-center font-bold text-xs ring-4 ring-white dark:ring-zinc-900 shadow-sm">
                                            {{ $index + 1 }}
                                        </div>

                                        <div class="w-full md:flex-1">
                                            <label class="label hidden md:flex"><span
                                                    class="label-text text-[10px] font-bold text-zinc-400">Pilih Agen
                                                    Stop*</span></label>
                                            <select wire:model.live="form.stops.{{ $index }}.agent_id"
                                                class="select select-bordered select-sm w-full bg-white dark:bg-zinc-900 @error('form.stops.' . $index . '.agent_id')  @enderror shadow-sm">
                                                <option value="">Pilih Agen Stop</option>
                                                @foreach ($this->agents->groupBy(fn($a) => $a->location->province ?? 'LAINNYA') as $province => $provAgents)
                                                    <optgroup label="{{ $province }}">
                                                        @foreach ($provAgents as $agent)
                                                            <option
                                                                wire:key="stop-agt-{{ $index }}-{{ $agent->id }}"
                                                                value="{{ $agent->id }}">{{ $agent->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                            @error('form.stops.' . $index . '.agent_id')
                                                <span class="text-[10px] text-red-500 mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="w-full sm:w-44 shrink-0">
                                            <label class="label hidden md:flex"><span
                                                    class="label-text text-[10px] font-bold text-zinc-400">Aturan Akses
                                                    Penumpang*</span></label>
                                            <select wire:model="form.stops.{{ $index }}.type"
                                                class="select select-bordered select-sm w-full bg-white dark:bg-zinc-900 @error('form.stops.' . $index . '.type')  @enderror shadow-sm">
                                                <option value="both">Naik turun Penumpang (Both)</option>
                                                <option value="boarding_only">Hanya Naik (Boarding)</option>
                                                <option value="dropoff_only">Hanya Turun (Dropoff)</option>
                                                <option value="transit">Check / Transit (No-Pax)</option>
                                            </select>
                                            @error('form.stops.' . $index . '.type')
                                                <span class="text-[10px] text-red-500 mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div
                                            class="w-full sm:w-auto shrink-0 flex items-center justify-start sm:justify-end gap-3 sm:pt-6">
                                            <label class="cursor-pointer label p-0 gap-2">
                                                <span class="label-text text-xs mr-2 font-medium">Cek Kontrol?</span>
                                                <input type="checkbox"
                                                    wire:model="form.stops.{{ $index }}.is_checkpoint"
                                                    class="toggle toggle-sm toggle-primary shadow-sm" />
                                            </label>
                                        </div>

                                        <div class="absolute -top-3 -right-3 sm:relative sm:top-0 sm:right-0 sm:pt-6">
                                            <button type="button" wire:click="removeStop({{ $index }})"
                                                class="btn btn-circle btn-sm md:btn-square md:rounded-lg bg-red-100 hover:bg-red-200 text-red-600 border-0 dark:bg-red-500/20 dark:hover:bg-red-500/30 dark:text-red-400 shadow-sm transition-all hover:scale-110">
                                                <x-heroicon-o-x-mark class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div
                                    class="py-8 text-center bg-zinc-50/50 dark:bg-zinc-800/20 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700">
                                    <x-heroicon-o-arrows-right-left
                                        class="w-8 h-8 text-zinc-300 dark:text-zinc-600 mx-auto mb-2" />
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Belum ada Titik
                                        Pemberhentian/Stop yang dikonfigurasi.</p>
                                </div>
                            @endif
                        </div>

                        @error('form.stops')
                            <div class="mt-4 p-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:/20 rounded-xl">
                                <span class="text-xs text-red-600 dark:text-red-400 font-bold flex items-center gap-2">
                                    <x-heroicon-s-exclamation-circle class="w-4 h-4" /> Validasi Gagal:
                                    {{ $message }}
                                </span>
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Right: Route Preview Card (1/3 width) -->
                <div class="lg:col-span-1 order-1 lg:order-2">
                    <div
                        class="lg:sticky lg:top-6 bg-linear-to-br from-indigo-600 to-violet-700 rounded-3xl p-6 shadow-xl shadow-indigo-500/30 text-white">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 rounded-xl bg-white/20">
                                <x-heroicon-o-map class="w-5 h-5" />
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">Pratinjau Trayek</h4>
                                @if ($form->distance_km)
                                    <p class="text-xs text-indigo-200">≈ {{ $form->distance_km }} KM</p>
                                @else
                                    <p class="text-xs text-indigo-300">Pilih agen untuk estimasi jarak</p>
                                @endif
                            </div>
                        </div>

                        @php
                            $segments = $this->segmentedRoute;
                            $hasData = collect($segments)->contains(fn($s) => $s['name'] !== '—');
                        @endphp

                        @if (!$hasData)
                            <div class="py-6 text-center">
                                <x-heroicon-o-map-pin class="w-8 h-8 mx-auto mb-2 text-indigo-300" />
                                <p class="text-xs text-indigo-200">Pilih Terminal &amp; Destinasi<br>untuk melihat
                                    pratinjau rute.</p>
                            </div>
                        @else
                            <div class="space-y-0">
                                @foreach ($segments as $i => $seg)
                                    @php
                                        $isLast = $i === count($segments) - 1;
                                        $dotColor = match ($seg['type']) {
                                            'origin' => 'bg-emerald-400 ring-emerald-300/50 w-3 h-3',
                                            'destination' => 'bg-red-400 ring-red-300/50 w-3 h-3',
                                            default => 'bg-white/60 ring-white/20 w-2.5 h-2.5',
                                        };
                                        $labelColor = match ($seg['type']) {
                                            'origin' => 'text-emerald-300',
                                            'destination' => 'text-red-300',
                                            default => 'text-indigo-200',
                                        };
                                        $etaSeg = null;
                                        if ($seg['km']) {
                                            $j = floor($seg['km'] / 60);
                                            $m = round(fmod($seg['km'] / 60, 1) * 60);
                                            $etaSeg = ($j > 0 ? "{$j}j " : '') . "{$m}m";
                                        }
                                    @endphp
                                    <div class="flex gap-3">
                                        <div class="flex flex-col items-center">
                                            <div class="rounded-full ring-2 shrink-0 mt-0.5 {{ $dotColor }}">
                                            </div>
                                            @if (!$isLast)
                                                <div class="w-0.5 flex-1 bg-white/20 my-1"></div>
                                            @endif
                                        </div>
                                        <div class="{{ $isLast ? '' : 'pb-3' }}">
                                            <p
                                                class="text-[10px] font-bold uppercase tracking-widest {{ $labelColor }}">
                                                {{ $seg['label'] }}
                                            </p>
                                            <p class="text-sm font-semibold">{{ $seg['name'] }}</p>
                                            @if ($seg['km'])
                                                <p class="text-[10px] text-white/50 mt-0.5">
                                                    ↑ {{ $seg['km'] }} km &nbsp;·&nbsp; ~{{ $etaSeg }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Stats --}}
                            @php
                                $stopCount = collect($segments)->where('type', 'stop')->count();
                                $etaJam = $form->distance_km ? floor($form->distance_km / 60) : null;
                                $etaMenit = $form->distance_km ? round(fmod($form->distance_km / 60, 1) * 60) : null;
                                $etaLabel =
                                    $etaJam !== null ? ($etaJam > 0 ? "{$etaJam}j " : '') . "{$etaMenit}m" : '—';
                            @endphp
                            <div class="mt-5 pt-4 border-t border-white/20 grid grid-cols-3 gap-2">
                                <div class="bg-white/10 rounded-xl p-3 text-center">
                                    <p class="text-lg font-black">{{ $stopCount }}</p>
                                    <p class="text-[10px] text-indigo-200 uppercase tracking-wide">Titik Stop</p>
                                </div>
                                <div class="bg-white/10 rounded-xl p-3 text-center">
                                    <p class="text-lg font-black">{{ $form->distance_km ?: '—' }}</p>
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
                            <button type="submit"
                                class="btn w-full bg-white text-indigo-700 hover:bg-indigo-50 border-0 h-11 rounded-2xl font-black tracking-tight text-sm transition-all group overflow-hidden relative shadow-lg">
                                <div
                                    class="absolute inset-0 bg-linear-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer">
                                </div>
                                <span wire:loading.remove wire:target="saveRoute"
                                    class="flex items-center justify-center gap-2 relative z-10">
                                    <x-heroicon-o-check-circle class="w-5 h-5" />
                                    SIMPAN PERUBAHAN
                                </span>
                                <span wire:loading wire:target="saveRoute"
                                    class="loading loading-spinner loading-md relative z-10"></span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </form>
    </div>
</div>
