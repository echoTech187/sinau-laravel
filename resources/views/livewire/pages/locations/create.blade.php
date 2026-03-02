@php
    /** @var \App\Livewire\Pages\Locations\Create $this */
@endphp
<div class="relative min-h-full">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-150 h-150 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
        <div class="absolute bottom-0 left-1/4 w-125 h-125 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8">
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/50 pb-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-linear-to-br from-indigo-500 to-blue-600 shadow-lg shadow-indigo-500/30">
                            <x-heroicon-o-map-pin class="w-5 h-5 text-white" />
                        </div>
                        Tambah Lokasi Operasional
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Daftarkan titik henti, agen, atau fasilitas baru ke dalam sistem.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a wire:navigate href="{{ route('locations.index') }}"
                        class="btn btn-sm bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl transition-all">
                        <x-heroicon-o-arrow-left class="w-4 h-4" />
                        Kembali
                    </a>
                </div>
            </header>
        </div>

        <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up"
            style="animation-delay: 0.1s">
            <!-- Left: Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-6 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-information-circle class="w-4 h-4" />
                        Informasi Dasar
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Nama Lokasi
                                </span>
                            </label>
                            <input type="text" wire:model="form.name" placeholder="Contoh: Terminal Arjosari"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-zinc-900 dark:text-white" />
                            @error('form.name')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Kota
                                </span>
                            </label>
                            <input type="text" wire:model="form.city" placeholder="Contoh: Malang"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-zinc-900 dark:text-white" />
                            @error('form.city')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Provinsi
                                </span>
                            </label>
                            <input type="text" wire:model="form.province" placeholder="Contoh: Jawa Timur"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-zinc-900 dark:text-white" />
                            @error('form.province')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    QR Code Gate (Opsional)
                                </span>
                            </label>
                            <input type="text" wire:model="form.qr_code_gate" placeholder="ID Gerbang"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-mono text-zinc-900 dark:text-white uppercase tracking-widest" />
                            @error('form.qr_code_gate')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-control w-full">
                        <label class="label pb-0">
                            <span
                                class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                Alamat Lengkap
                            </span>
                        </label>
                        <textarea wire:model="form.address" placeholder="Jalan Raya No. 123..."
                            class="textarea textarea-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-zinc-900 dark:text-white h-24"></textarea>
                        @error('form.address')
                            <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-6 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-globe-alt class="w-4 h-4" />
                        Koordinat & Geofence
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Latitude
                                </span>
                            </label>
                            <input type="text" wire:model="form.latitude" placeholder="-7.123456"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-zinc-900 dark:text-white" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Longitude
                                </span>
                            </label>
                            <input type="text" wire:model="form.longitude" placeholder="112.123456"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-zinc-900 dark:text-white" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="form-control">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Tipe Geofence
                                </span>
                            </label>
                            <select wire:model="form.geofence_type"
                                class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-zinc-900 dark:text-white">
                                <option value="">Tanpa Geofence</option>
                                <option value="circular">Circular (Radius)</option>
                                <option value="polygon">Polygon (Area)</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Radius (Meter)
                                </span>
                            </label>
                            <input type="number" wire:model="form.geofence_radius_meter" placeholder="100"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-zinc-900 dark:text-white" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Settings & Role -->
            <div class="space-y-6">
                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm space-y-4">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-6 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-tag class="w-4 h-4" />
                        Peran Lokasi
                    </h4>
                    <div class="flex flex-col gap-2">
                        @foreach ($locationRoles as $role)
                            <label wire:key="loc-role-{{ $role->id }}"
                                class="flex items-start gap-4 p-4 rounded-2xl bg-white/50 dark:bg-zinc-950/50 hover:bg-zinc-50 dark:hover:bg-zinc-800/80 cursor-pointer transition-all border border-zinc-200 dark:border-zinc-800">
                                <input type="checkbox" wire:model="form.role_ids" value="{{ $role->id }}"
                                    class="checkbox checkbox-sm checkbox-primary rounded-lg mt-0.5" />
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-bold text-zinc-900 dark:text-white">{{ $role->name }}</span>
                                    <span
                                        class="text-[10px] uppercase font-bold text-zinc-500 tracking-wider mt-1">{{ $role->code }}</span>
                                    <span
                                        class="text-[10px] text-zinc-400 leading-tight mt-1">{{ $role->description }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm space-y-4">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-6 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-wrench-screwdriver class="w-4 h-4" />
                        Fasilitas Lainnya
                    </h4>
                    <label
                        class="flex items-center justify-between cursor-pointer group p-4 rounded-2xl bg-white/50 dark:bg-zinc-950/50 hover:bg-zinc-50 dark:hover:bg-zinc-800/80 transition-all border border-zinc-200 dark:border-zinc-800">
                        <span
                            class="text-xs font-bold text-zinc-600 dark:text-zinc-400 group-hover:text-amber-500 transition-colors uppercase tracking-wider">Memiliki
                            Bengkel/P2H</span>
                        <input type="checkbox" wire:model="form.has_maintenance_facility"
                            class="toggle toggle-amber toggle-sm" />
                    </label>
                </div>

                <div class="pt-4 flex flex-col gap-3">
                    <button type="submit"
                        class="btn btn-block bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/30 rounded-2xl h-14 font-black tracking-tight text-sm transition-all transform active:scale-[0.98] group overflow-hidden relative">
                        <div
                            class="absolute inset-0 bg-linear-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer">
                        </div>
                        <span wire:loading.remove wire:target="save" class="flex items-center gap-2 relative z-10">
                            <x-heroicon-o-check-circle class="w-5 h-5" />
                            Simpan Lokasi
                        </span>
                        <span wire:loading wire:target="save"
                            class="loading loading-spinner loading-md relative z-10"></span>
                    </button>
                    <a wire:navigate href="{{ route('locations.index') }}"
                        class="btn btn-block btn-ghost bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border-0 shadow-sm rounded-2xl h-14 font-black tracking-tight text-sm transition-all">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
