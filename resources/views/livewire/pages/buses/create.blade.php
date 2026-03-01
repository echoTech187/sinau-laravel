<div class="container relative min-h-screen pb-10">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div
            class="absolute top-0 right-0 w-[600px] h-[600px] bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-1/4 w-[500px] h-[500px] bg-purple-500/10 dark:bg-purple-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8">
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/50 pb-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 shadow-lg shadow-indigo-500/30">
                            <x-heroicon-o-plus-circle class="w-6 h-6 text-white" />
                        </div>
                        Tambah Armada Baru
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Tambahkan armada bus baru ke dalam sistem PO Bus.
                    </p>
                </div>
                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <a wire:navigate href="{{ route('buses.index') }}"
                        class="btn btn-sm bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl transition-all">
                        <x-heroicon-o-arrow-left class="w-4 h-4" />
                        Kembali
                    </a>
                </div>
            </header>
        </div>

        <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm animate-fade-in-up"
            style="animation-delay: 0.1s">
            <form id="busForm" wire:submit="saveBus" class="space-y-8">
                <!-- Section 1: General & Details -->
                <div class="space-y-6">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-identification class="w-4 h-4" />
                        Identitas & Spesifikasi Dasar
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Nomor Lambung*
                                </span>
                            </label>
                            <input type="text" wire:model="form.fleet_code"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all @error('form.fleet_code') border-red-500 @enderror"
                                placeholder="B-001" />
                            @error('form.fleet_code')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Nomor Polisi*
                                </span>
                            </label>
                            <input type="text" wire:model="form.plate_number"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all @error('form.plate_number') border-red-500 @enderror"
                                placeholder="B 1234 ABC" />
                            @error('form.plate_number')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Nama/Julukan Armada
                                </span>
                            </label>
                            <input type="text" wire:model="form.name"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all"
                                placeholder="Raja Jalanan" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    RFID Tag ID
                                </span>
                            </label>
                            <input type="text" wire:model="form.rfid_tag_id"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all"
                                placeholder="Opsi (Isi saat tap)" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Kelas Armada*
                                </span>
                            </label>
                            <select wire:model="form.bus_class_id"
                                class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all @error('form.bus_class_id') border-red-500 @enderror">
                                <option value="">Pilih Kelas</option>
                                @foreach ($this->busClasses as $bc)
                                    <option value="{{ $bc->id }}">{{ $bc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Layout Kursi*
                                </span>
                            </label>
                            <select wire:model="form.seat_layout_id"
                                class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all @error('form.seat_layout_id') border-red-500 @enderror">
                                <option value="">Pilih Layout</option>
                                @foreach ($this->seatLayouts as $sl)
                                    <option value="{{ $sl->id }}">{{ $sl->name }} ({{ $sl->total_seats }}
                                        Kursi)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Base Pool Utama*
                                </span>
                            </label>
                            <select wire:model="form.base_pool_id"
                                class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all @error('form.base_pool_id') border-red-500 @enderror">
                                <option value="">Pilih Garasi</option>
                                @foreach ($this->basePools as $pool)
                                    <option value="{{ $pool->id }}">{{ $pool->name }} ({{ $pool->city }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Tahun Pembuatan*
                                </span>
                            </label>
                            <input type="number" wire:model="form.manufacture_year"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all"
                                placeholder="2018" />
                        </div>
                    </div>
                </div>

                <!-- Section 2: Fisik Kendaraan -->
                <div class="space-y-6">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-cog-8-tooth class="w-4 h-4" />
                        Sasis & Karoseri
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Merk Sasis*
                                </span>
                            </label>
                            <input type="text" wire:model="form.chassis_brand"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all"
                                placeholder="Mercedes-Benz/Scania/Hino" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Tipe Sasis*
                                </span>
                            </label>
                            <input type="text" wire:model="form.chassis_type"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all"
                                placeholder="O500R 1836" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Karoseri (Body Maker)*
                                </span>
                            </label>
                            <input type="text" wire:model="form.body_maker"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all"
                                placeholder="Adiputro/Laksana" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Model Karoseri*
                                </span>
                            </label>
                            <input type="text" wire:model="form.body_model"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all"
                                placeholder="Jetbus 3+ SHD" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Nomor Mesin*
                                </span>
                            </label>
                            <input type="text" wire:model="form.engine_number"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-mono text-zinc-700"
                                placeholder="Sesuai STNK" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Nomor Sasis*
                                </span>
                            </label>
                            <input type="text" wire:model="form.chassis_number"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-mono text-zinc-700"
                                placeholder="Sesuai STNK" />
                        </div>
                    </div>
                </div>

                <!-- Section 3: Kapasitas & Metrik -->
                <div class="space-y-6">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-archive-box class="w-4 h-4" />
                        Kapasitas & Metrik Operasional
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Total Kursi Fisik*
                                </span>
                            </label>
                            <input type="number" wire:model="form.total_seats"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all text-zinc-800 font-bold"
                                placeholder="40" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Maks. Bagasi (KG)*
                                </span>
                            </label>
                            <input type="number" wire:model="form.max_baggage_weight_kg"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all text-zinc-800 font-bold"
                                placeholder="1000" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Maks. Bagasi (Kubikasi m3)
                                </span>
                            </label>
                            <input type="number" step="0.01" wire:model="form.max_baggage_volume_m3"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all text-zinc-800 font-bold"
                                placeholder="10.5" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Odometer Saat Ini (KM)*
                                </span>
                            </label>
                            <input type="number" wire:model="form.current_odometer"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all text-zinc-800 font-bold"
                                placeholder="150000" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Rata-rata Harian (KM)*
                                </span>
                            </label>
                            <input type="number" wire:model="form.average_daily_km"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all text-zinc-800 font-bold"
                                placeholder="500" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Status Armada*
                                </span>
                            </label>
                            <select wire:model="form.status"
                                class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                                <option value="active">Active (Siap Jalan)</option>
                                <option value="maintenance">Maintenance (Perbaikan)</option>
                                <option value="inactive">Inactive (TIdak Beroperasi)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Legalitas -->
                <div class="space-y-6">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-document-check class="w-4 h-4" />
                        Legalitas Kendaraan
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Exp. STNK*
                                </span>
                            </label>
                            <input type="date" wire:model="form.stnk_expired_at"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Exp. Uji KIR*
                                </span>
                            </label>
                            <input type="date" wire:model="form.kir_expired_at"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Exp. KPS*
                                </span>
                            </label>
                            <input type="date" wire:model="form.kps_expired_at"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Exp. Asuransi*
                                </span>
                            </label>
                            <input type="date" wire:model="form.insurance_expired_at"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all" />
                        </div>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="pt-8 flex justify-end">
                    <button type="submit"
                        class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-0 px-10 h-14 rounded-2xl shadow-xl shadow-indigo-600/30 font-black tracking-tight text-lg transition-all group overflow-hidden relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer">
                        </div>
                        <span wire:loading.remove wire:target="saveBus" class="flex items-center gap-3 relative z-10">
                            <x-heroicon-o-check-circle class="w-6 h-6" />
                            SIMPAN ARMADA
                        </span>
                        <span wire:loading wire:target="saveBus"
                            class="loading loading-spinner loading-md relative z-10"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
