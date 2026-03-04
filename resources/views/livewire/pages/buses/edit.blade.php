@php
    /** @var \App\Livewire\Pages\Buses\Edit $this */
@endphp
<div class="relative min-h-full">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-150 h-150 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-1/4 w-125 h-125 bg-amber-500/10 dark:bg-amber-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8">
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/50 pb-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-linear-to-br from-indigo-500 to-amber-600 shadow-lg shadow-indigo-500/30">
                            <x-heroicon-o-pencil-square class="w-5 h-5 text-white" />
                        </div>
                        Ubah Data Armada: {{ $bus->fleet_code }}
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Edit data informasi fisik, registrasi, dan metrik bus.
                    </p>
                </div>
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
                <div class="space-y-6">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-identification class="w-4 h-4" />
                        Identitas & Spesifikasi Dasar
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <x-form-input label="Nomor Lambung*" wire:model="form.fleet_code" placeholder="B-001" />
                        <x-form-input label="Nomor Polisi*" wire:model="form.plate_number" placeholder="B 1234 ABC" />
                        <x-form-input label="Nama/Julukan Armada" wire:model="form.name" placeholder="Raja Jalanan" />
                        <x-form-input label="RFID Tag ID" wire:model="form.rfid_tag_id"
                            placeholder="Opsi (Isi saat tap)" />

                        <x-form-select label="Kelas Armada*" wire:model="form.bus_class_id">
                            <option value="">Pilih Kelas</option>
                            @foreach ($this->busClasses as $bc)
                                <option wire:key="bc-{{ $bc->id }}" value="{{ $bc->id }}">
                                    {{ $bc->name }}</option>
                            @endforeach
                        </x-form-select>

                        <x-form-select label="Layout Kursi*" wire:model="form.seat_layout_id">
                            <option value="">Pilih Layout</option>
                            @foreach ($this->seatLayouts as $sl)
                                <option wire:key="sl-{{ $sl->id }}" value="{{ $sl->id }}">
                                    {{ $sl->name }} ({{ $sl->total_seats }} Kursi)</option>
                            @endforeach
                        </x-form-select>

                        <x-form-select label="Base Pool Utama*" wire:model="form.base_pool_id">
                            <option value="">Pilih Garasi</option>
                            @foreach ($this->basePools as $pool)
                                <option wire:key="bp-{{ $pool->id }}" value="{{ $pool->id }}">
                                    {{ $pool->name }}{{ $pool->city ? ' (' . $pool->city . ')' : '' }}</option>
                            @endforeach
                        </x-form-select>

                        <x-form-input label="Tahun Pembuatan*" wire:model="form.manufacture_year" type="number"
                            placeholder="2018" />
                    </div>
                </div>

                <div class="space-y-6">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-cog-8-tooth class="w-4 h-4" />
                        Sasis & Karoseri
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <x-form-input label="Merk Sasis*" wire:model="form.chassis_brand"
                            placeholder="Mercedes-Benz/Scania/Hino" />
                        <x-form-input label="Tipe Sasis*" wire:model="form.chassis_type" placeholder="O500R 1836" />
                        <x-form-input label="Karoseri (Body Maker)*" wire:model="form.body_maker"
                            placeholder="Adiputro/Laksana" />
                        <x-form-input label="Model Karoseri*" wire:model="form.body_model"
                            placeholder="Jetbus 3+ SHD" />
                        <x-form-input label="Nomor Mesin*" wire:model="form.engine_number" placeholder="Sesuai STNK"
                            class="font-mono text-zinc-700" />
                        <x-form-input label="Nomor Sasis*" wire:model="form.chassis_number" placeholder="Sesuai STNK"
                            class="font-mono text-zinc-700" />
                    </div>
                </div>

                <div class="space-y-6">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-archive-box class="w-4 h-4" />
                        Kapasitas & Metrik Operasional
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <x-form-input label="Total Kursi Fisik*" wire:model="form.total_seats" type="number"
                            placeholder="40" class="text-zinc-800 font-bold" />
                        <x-form-input label="Maks. Bagasi (KG)*" wire:model="form.max_baggage_weight_kg" type="number"
                            placeholder="1000" class="text-zinc-800 font-bold" />
                        <x-form-input label="Maks. Bagasi (Kubikasi m3)" wire:model="form.max_baggage_volume_m3"
                            type="number" placeholder="10.5" class="text-zinc-800 font-bold" />
                        <x-form-input label="Odometer Saat Ini (KM)*" wire:model="form.current_odometer" type="number"
                            placeholder="150000" class="text-zinc-800 font-bold" />
                        <x-form-input label="Rata-rata Harian (KM)*" wire:model="form.average_daily_km"
                            type="number" placeholder="500" class="text-zinc-800 font-bold" />

                        <x-form-select label="Status Armada*" wire:model="form.status" class="font-bold">
                            <option value="active">Active (Siap Jalan)</option>
                            <option value="maintenance">Maintenance (Perbaikan)</option>
                            <option value="inactive">Inactive (TIdak Beroperasi)</option>
                        </x-form-select>
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
                        <x-form-input label="Exp. STNK*" wire:model="form.stnk_expired_at" type="date" />
                        <x-form-input label="Exp. Uji KIR*" wire:model="form.kir_expired_at" type="date" />
                        <x-form-input label="Exp. KPS*" wire:model="form.kps_expired_at" type="date" />
                        <x-form-input label="Exp. Asuransi*" wire:model="form.insurance_expired_at" type="date" />
                    </div>
                </div>

                <!-- Section 5: Media -->
                <div class="space-y-6">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-photo class="w-4 h-4" />
                        Foto Armada
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                        <x-form-file label="Ganti Foto Bus" wire:model="form.photo"
                            hint="Format: JPG, PNG. Maks: 2MB. Mengupload foto baru akan menghapus foto lama secara otomatis." />

                        <div class="relative group">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-3">Preview /
                                Foto Saat Ini</p>
                            <div
                                class="aspect-video rounded-3xl border-2 border-dashed border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center overflow-hidden relative shadow-inner">
                                @if ($form->photo)
                                    <img src="{{ $form->photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                    <button type="button" wire:click="$set('form.photo', null)"
                                        class="absolute top-4 right-4 p-2 bg-red-500 text-white rounded-xl shadow-lg hover:bg-red-600 transition-all">
                                        <x-heroicon-o-trash class="w-5 h-5" />
                                    </button>
                                @elseif ($form->photo_path)
                                    <img src="{{ asset('storage/' . $form->photo_path) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="text-center p-6">
                                        <x-heroicon-o-camera class="w-12 h-12 text-zinc-300 mx-auto mb-2" />
                                        <p class="text-xs text-zinc-400">Belum ada foto</p>
                                    </div>
                                @endif

                                <div wire:loading wire:target="form.photo"
                                    class="absolute inset-0 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm flex items-center justify-center">
                                    <span class="loading loading-spinner loading-md text-indigo-600"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-8 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                        class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-0 px-8 rounded-2xl shadow-lg shadow-indigo-600/30 font-bold tracking-wide flex items-center justify-center gap-2 group transition-all">
                        <x-heroicon-o-check-circle wire:loading.remove wire:target="saveBus"
                            class="w-5 h-5 group-hover:scale-110 transition-transform" />
                        <span wire:loading wire:target="saveBus" class="loading loading-spinner loading-sm"></span>
                        Simpan Perubahan Bus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
