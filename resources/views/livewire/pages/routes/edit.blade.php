<div class="container relative min-h-screen pb-10">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div
            class="absolute top-0 right-0 w-[600px] h-[600px] bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-1/4 w-[500px] h-[500px] bg-sky-500/10 dark:bg-sky-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8">
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/50 pb-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-gradient-to-br from-indigo-500 to-sky-600 shadow-lg shadow-indigo-500/30">
                            <x-heroicon-o-pencil-square class="w-6 h-6 text-white" />
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
                        <label class="label pb-0">
                            <span
                                class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                Kode Operasional Tanda Rute*
                            </span>
                        </label>
                        <input type="text" wire:model="form.route_code"
                            class="input input-bordered uppercase bg-slate-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-inner text-zinc-400 cursor-not-allowed font-black tracking-widest"
                            disabled />
                    </div>
                    <div class="form-control w-full">
                        <label class="label pb-0">
                            <span
                                class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                Nama Singkat Rute / Jurusan*
                            </span>
                        </label>
                        <input type="text" wire:model="form.name"
                            class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all @error('form.name') border-red-500 @enderror"
                            placeholder="Surabaya - Malang PP" />
                        @error('form.name')
                            <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="form-control w-full">
                        <label class="label pb-0">
                            <span
                                class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-400 mb-1.5">
                                Terminal Keberangkatan*
                            </span>
                        </label>
                        <select wire:model="form.origin_location_id"
                            class="select select-bordered bg-white dark:bg-zinc-950 border-emerald-200 dark:border-emerald-900/50 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 transition-all @error('form.origin_location_id') border-red-500 @enderror">
                            <option value="">-- Pilih Asal Keberangkatan --</option>
                            @foreach ($this->locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                        @error('form.origin_location_id')
                            <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label pb-0">
                            <span
                                class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-red-600 dark:text-red-400 mb-1.5">
                                Destinasi Akhir Penurunan*
                            </span>
                        </label>
                        <select wire:model="form.destination_location_id"
                            class="select select-bordered bg-white dark:bg-zinc-950 border-red-200 dark:border-red-900/50 rounded-xl shadow-sm focus:ring-2 focus:ring-red-500 transition-all @error('form.destination_location_id') border-red-500 @enderror">
                            <option value="">-- Pilih Tujuan Akhir --</option>
                            @foreach ($this->locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                        @error('form.destination_location_id')
                            <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label pb-0">
                            <span
                                class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                Estimasi Jarak Induk (KM)
                            </span>
                        </label>
                        <input type="number" wire:model="form.distance_km"
                            class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all @error('form.distance_km') border-red-500 @enderror"
                            placeholder="Cth: 155" />
                        @error('form.distance_km')
                            <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Titik Pemberhentian Dinamis (Stops) -->
            <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm animate-fade-in-up"
                style="animation-delay: 0.2s">
                <div class="flex items-center justify-between mb-6 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2">
                            <x-heroicon-o-bars-3-center-left class="w-4 h-4" />
                            Kalkulasi Trayek & Titik Pemberhentian Antara (Stops)
                        </h4>
                        <p class="text-[10px] text-zinc-400 mt-1">Lakukan penyisipan, penghapusan, atau pergantian
                            aturan pada agen/pool yang telah dikonfigurasi sebelumnya.</p>
                    </div>
                    <button type="button" wire:click="addStop"
                        class="btn btn-sm bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-indigo-600 dark:text-indigo-400 border-0 rounded-lg shadow-sm">
                        <x-heroicon-o-plus class="w-4 h-4" /> Tambah Titik Baru
                    </button>
                </div>

                <div class="space-y-4">
                    @forelse($form->stops as $index => $stop)
                        <div wire:key="stop-{{ $index }}"
                            class="flex flex-col md:flex-row items-center gap-4 bg-zinc-50/50 dark:bg-zinc-800/20 border border-zinc-200 dark:border-zinc-700/50 p-4 rounded-xl group relative transition-all hover:bg-white dark:hover:bg-zinc-800">
                            <!-- Indicator Urutan -->
                            <div
                                class="w-8 h-8 shrink-0 bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-400 rounded-full flex items-center justify-center font-bold text-xs ring-4 ring-white dark:ring-zinc-900 shadow-sm">
                                {{ $index + 1 }}
                            </div>

                            <!-- Pemilihan Lokasi -->
                            <div class="w-full md:flex-1">
                                <label class="label hidden md:flex"><span
                                        class="label-text text-[10px] font-bold text-zinc-400">Pilih Lokasi
                                        Stop*</span></label>
                                <select wire:model="form.stops.{{ $index }}.location_id"
                                    class="select select-bordered select-sm w-full bg-white dark:bg-zinc-900 @error('form.stops.' . $index . '.location_id') border-red-500 @enderror shadow-sm">
                                    <option value="">Pilih Titik Lokasi / Agen</option>
                                    @foreach ($this->locations as $loc)
                                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                    @endforeach
                                </select>
                                @error('form.stops.' . $index . '.location_id')
                                    <span class="text-[10px] text-red-500 mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Aturan Penumpang -->
                            <div class="w-full md:w-48 shrink-0">
                                <label class="label hidden md:flex"><span
                                        class="label-text text-[10px] font-bold text-zinc-400">Aturan Akses
                                        Penumpang*</span></label>
                                <select wire:model="form.stops.{{ $index }}.type"
                                    class="select select-bordered select-sm w-full bg-white dark:bg-zinc-900 @error('form.stops.' . $index . '.type') border-red-500 @enderror shadow-sm">
                                    <option value="both">Naik turun Penumpang (Both)</option>
                                    <option value="boarding_only">Hanya Naik (Boarding)</option>
                                    <option value="dropoff_only">Hanya Turun (Dropoff)</option>
                                    <option value="transit">Check / Transit (No-Pax)</option>
                                </select>
                                @error('form.stops.' . $index . '.type')
                                    <span class="text-[10px] text-red-500 mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Is Checkpoint Switch -->
                            <div
                                class="w-full md:w-32 shrink-0 flex items-center justify-center md:justify-end gap-3 mt-4 md:mt-0 md:pt-6">
                                <label class="cursor-pointer label p-0 gap-2">
                                    <span class="label-text text-xs mr-2 font-medium">Cek Kontrol?</span>
                                    <input type="checkbox" wire:model="form.stops.{{ $index }}.is_checkpoint"
                                        class="toggle toggle-sm toggle-primary shadow-sm" />
                                </label>
                            </div>

                            <!-- Remove Button -->
                            <div class="absolute -top-3 -right-3 md:relative md:top-0 md:right-0 md:pt-6">
                                <button type="button" wire:click="removeStop({{ $index }})"
                                    class="btn btn-circle btn-sm md:btn-square md:rounded-lg bg-red-100 hover:bg-red-200 text-red-600 border-0 dark:bg-red-500/20 dark:hover:bg-red-500/30 dark:text-red-400 shadow-sm transition-all hover:scale-110">
                                    <x-heroicon-o-x-mark class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    @empty
                        <div
                            class="py-8 text-center bg-zinc-50/50 dark:bg-zinc-800/20 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700">
                            <x-heroicon-o-arrows-right-left
                                class="w-8 h-8 text-zinc-300 dark:text-zinc-600 mx-auto mb-2" />
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Belum ada Titik
                                Pemberhentian/Stop yang dikonfigurasi.</p>
                            <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Rute ini dianggap sebagai Rute
                                Langganan Cepat (Point-to-Point) antara Asal -> Tujuan tanpa melalui terminal agen lain.
                            </p>
                        </div>
                    @endforelse
                </div>

                @error('form.stops')
                    <div
                        class="mt-4 p-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl">
                        <span class="text-xs text-red-600 dark:text-red-400 font-bold flex items-center gap-2">
                            <x-heroicon-s-exclamation-circle class="w-4 h-4" /> Validasi Gagal: {{ $message }}
                        </span>
                    </div>
                @enderror
            </div>

            <!-- Submit Action -->
            <div class="flex justify-end pt-8 pb-12">
                <button type="submit"
                    class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-0 px-10 h-14 rounded-2xl shadow-xl shadow-indigo-600/30 font-black tracking-tight text-lg transition-all group overflow-hidden relative">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer">
                    </div>
                    <span wire:loading.remove wire:target="saveRoute" class="flex items-center gap-3 relative z-10">
                        <x-heroicon-o-check-circle class="w-6 h-6" />
                        SIMPAN PERUBAHAN
                    </span>
                    <span wire:loading wire:target="saveRoute"
                        class="loading loading-spinner loading-md relative z-10"></span>
                </button>
            </div>
        </form>
    </div>
</div>
