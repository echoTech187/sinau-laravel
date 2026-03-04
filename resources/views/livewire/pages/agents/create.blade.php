@php
    /** @var \App\Livewire\Pages\Agents\Create $this */
@endphp
<div class="relative min-h-full">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-150 h-150 bg-sky-500/10 dark:bg-sky-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-1/4 w-125 h-125 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8">
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/50 pb-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-linear-to-br from-sky-500 to-indigo-600 shadow-lg shadow-sky-500/30">
                            <x-heroicon-o-plus-circle class="w-5 h-5 text-white" />
                        </div>
                        Tambah Agen / Mitra Baru
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Daftarkan kantor cabang atau agen mitra baru untuk operasional ticketing.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a wire:navigate href="{{ route('agents.index') }}"
                        class="btn btn-sm bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl transition-all">
                        <x-heroicon-o-arrow-left class="w-4 h-4" />
                        Kembali
                    </a>
                </div>
            </header>
        </div>

        <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm animate-fade-in-up"
            style="animation-delay: 0.1s">
            <form id="agentForm" wire:submit="saveAgent" class="space-y-8">
                <div>
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-4 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-building-storefront class="w-4 h-4" />
                        Informasi Dasar Agen
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Row 1: Identity --}}
                        <x-form-input label="Kode Agen*" wire:model="form.agent_code" placeholder="AGT-001"
                            class="uppercase font-bold placeholder:font-normal placeholder:lowercase" />
                        <x-form-input label="Nama / Perusahaan Agen*" wire:model="form.name"
                            placeholder="Nama Agen / Cabang" class="font-bold" />

                        {{-- Row 2: Type Selection (Full Width) --}}
                        <div class="form-control md:col-span-2">
                            <label class="label pt-0 pb-1.5">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">
                                    Tipe Kemitraan Agen*
                                </span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                @foreach (\App\Enums\AgentType::cases() as $type)
                                    <label class="relative flex flex-col cursor-pointer focus:outline-hidden group">
                                        <input type="radio" wire:model="form.type" value="{{ $type->value }}"
                                            class="peer sr-only" />
                                        <div
                                            class="flex flex-col xl:flex-row xl:items-start xl:gap-5 h-full p-5 rounded-[2rem] border-2 border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-950/50 shadow-sm transition-all duration-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50/30 dark:peer-checked:bg-indigo-500/5 peer-checked:ring-4 peer-checked:ring-indigo-500/10 hover:border-zinc-300 dark:hover:border-zinc-700">
                                            <div class="flex items-center justify-between xl:block mb-5 xl:mb-0">
                                                <div
                                                    class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30 group-hover:text-indigo-600 transition-all peer-checked:bg-indigo-500 peer-checked:text-white">
                                                    <x-dynamic-component :component="$type->icon()" class="w-6 h-6" />
                                                </div>
                                                <div
                                                    class="xl:hidden w-6 h-6 rounded-full border-2 border-zinc-200 dark:border-zinc-700 flex items-center justify-center group-has-[:checked]:border-indigo-500 group-has-[:checked]:bg-indigo-500 transition-all">
                                                    <div
                                                        class="w-2.5 h-2.5 rounded-full bg-white scale-0 group-has-[:checked]:scale-100 transition-all duration-300">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between mb-2">
                                                    <h3
                                                        class="font-black text-sm uppercase tracking-wider text-zinc-900 dark:text-white leading-none">
                                                        {{ $type->label() }}
                                                    </h3>
                                                    <div
                                                        class="hidden xl:flex w-6 h-6 rounded-full border-2 border-zinc-200 dark:border-zinc-700 items-center justify-center group-has-[:checked]:border-indigo-500 group-has-[:checked]:bg-indigo-500 transition-all">
                                                        <div
                                                            class="w-2.5 h-2.5 rounded-full bg-white scale-0 group-has-[:checked]:scale-100 transition-all duration-300">
                                                        </div>
                                                    </div>
                                                </div>
                                                <p
                                                    class="text-xs leading-relaxed text-zinc-500 dark:text-zinc-400 font-medium">
                                                    {{ $type->description() }}
                                                </p>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('form.type')
                                <span class="text-[10px] text-red-500 mt-2 font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Row 3: Contact & Location --}}
                        <x-form-input label="No. Telepon / WhatsApp*" wire:model="form.phone_number" placeholder="08..."
                            class="font-bold" />

                        <x-form-select label="Titik Lokasi Terintegrasi" wire:model="form.location_id"
                            class="font-bold">
                            <option value="">Tanpa Titik Lokasi Peta</option>
                            @foreach ($this->locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </x-form-select>
                    </div>
                </div>

                {{-- === JAM OPERASIONAL (EDITABLE) ========================= --}}
                <div>
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-1 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-clock class="w-4 h-4" />
                        Jam Operasional
                    </h4>
                    <p class="text-[11px] text-zinc-400 mb-4">Atur jadwal buka per hari. Aktifkan "24 Jam" atau tandai
                        "Tutup" untuk hari libur.</p>

                    @php $dayLabels = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu']; @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-3">
                        @foreach ($dayLabels as $day => $label)
                            <div
                                class="flex flex-col gap-3 rounded-2xl border-2 p-4 transition-all duration-200
                                {{ $operationalHours[$day]['is_closed'] ? 'border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/30 opacity-60' : ($operationalHours[$day]['is_24_hours'] ? 'border-emerald-400 dark:border-emerald-600 bg-emerald-50/30 dark:bg-emerald-900/10' : 'border-indigo-200 dark:border-indigo-800 bg-indigo-50/30 dark:bg-indigo-900/10') }}">

                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-zinc-500 text-center">{{ $label }}</span>

                                {{-- Toggle: Tutup --}}
                                <label class="flex items-center justify-between cursor-pointer">
                                    <span class="text-[11px] font-semibold text-zinc-500">Tutup</span>
                                    <input type="checkbox"
                                        wire:model.live="operationalHours.{{ $day }}.is_closed"
                                        class="toggle toggle-sm toggle-error" />
                                </label>

                                @if (!$operationalHours[$day]['is_closed'])
                                    {{-- Toggle: 24 Jam --}}
                                    <label class="flex items-center justify-between cursor-pointer">
                                        <span class="text-[11px] font-semibold text-zinc-500">24 Jam</span>
                                        <input type="checkbox"
                                            wire:model.live="operationalHours.{{ $day }}.is_24_hours"
                                            class="toggle toggle-sm toggle-success" />
                                    </label>

                                    @if (!$operationalHours[$day]['is_24_hours'])
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Buka</span>
                                            <input type="time"
                                                wire:model="operationalHours.{{ $day }}.open_time"
                                                class="input input-sm input-bordered w-full bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-400 transition-all" />
                                            @error("operationalHours.{$day}.open_time")
                                                <span class="text-[9px] text-red-500 font-bold">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Tutup</span>
                                            <input type="time"
                                                wire:model="operationalHours.{{ $day }}.close_time"
                                                class="input input-sm input-bordered w-full bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-400 transition-all" />
                                            @error("operationalHours.{$day}.close_time")
                                                <span class="text-[9px] text-red-500 font-bold">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center py-2">
                                            <x-heroicon-o-sun class="w-5 h-5 text-emerald-500" />
                                            <span
                                                class="text-xs font-black text-emerald-600 dark:text-emerald-400 ml-1">Non-Stop</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="flex items-center justify-center py-2">
                                        <x-heroicon-o-moon class="w-5 h-5 text-zinc-400" />
                                        <span class="text-xs font-semibold text-zinc-400 ml-1">Libur</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                {{-- ========================================================= --}}

                <div>
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-4 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-currency-dollar class="w-4 h-4" />
                        Pengaturan Hierarki & Skema Komisi
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <x-form-select label="Induk / Cabang Utama" wire:model="form.parent_branch_id"
                            class="font-bold">
                            <option value="">Berdiri Sendiri (Pusat)</option>
                            @foreach ($this->branchOffices as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </x-form-select>

                        <div class="form-control w-full">
                            <label class="label pt-0 pb-1.5">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">
                                    Tipe Skema Komisi*
                                </span>
                            </label>
                            <div class="flex items-center gap-6 mt-3 px-2">
                                <label class="cursor-pointer flex items-center gap-3">
                                    <input type="radio" wire:model.live="form.commission_type" value="percentage"
                                        class="radio radio-primary radio-sm" />
                                    <span class="label-text font-bold text-zinc-700 dark:text-zinc-300">Persentase
                                        (%)</span>
                                </label>
                                <label class="cursor-pointer flex items-center gap-3">
                                    <input type="radio" wire:model.live="form.commission_type" value="flat"
                                        class="radio radio-primary radio-sm" />
                                    <span class="label-text font-bold text-zinc-700 dark:text-zinc-300">Flat
                                        (Rp)</span>
                                </label>
                            </div>
                            @error('form.commission_type')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control w-full">
                            <label class="label pt-0 pb-1.5">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">
                                    Nilai Komisi Dasar*
                                </span>
                            </label>
                            <div class="flex items-stretch">
                                <span
                                    class="flex items-center px-4 text-sm font-bold text-zinc-500 bg-zinc-50 dark:bg-zinc-800/50 border border-r-0 border-zinc-200 dark:border-zinc-800 rounded-l-xl">
                                    {{ $form->commission_type === 'percentage' ? '%' : 'Rp' }}
                                </span>
                                <input type="number" step="0.01" wire:model="form.commission_value"
                                    class="input input-bordered w-full rounded-l-none bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-r-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold" />
                            </div>
                            @error('form.commission_value')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-4 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-shield-check class="w-4 h-4" />
                        Status Operasional
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <x-form-select label="Status Operasional*" wire:model="form.status" class="font-bold">
                            <option value="active">Active (Dapat Menjual)</option>
                            <option value="inactive">Inactive</option>
                            <option value="banned">Banned (Masalah Akun)</option>
                        </x-form-select>
                    </div>
                </div>

                <div class="pt-8 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-3 mt-8">
                    <button type="submit" wire:loading.attr="disabled"
                        class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-0 px-8 rounded-2xl shadow-lg shadow-indigo-600/30 font-bold tracking-wide flex items-center justify-center gap-2 group transition-all">
                        <x-heroicon-o-check-circle wire:loading.remove wire:target="saveAgent"
                            class="w-5 h-5 group-hover:scale-110 transition-transform" />
                        <span wire:loading wire:target="saveAgent" class="loading loading-spinner loading-sm"></span>
                        Daftarkan Agen (Cabang)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
