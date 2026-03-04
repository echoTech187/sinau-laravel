@php
    /** @var \App\Livewire\Pages\SeatLayouts\Create $this */
@endphp
<div class="relative min-h-full pb-10 sm:pb-20">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-150 h-150 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-1/4 w-125 h-125 bg-purple-500/10 dark:bg-purple-500/5 rounded-full blur-[100px]">
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
                            <x-heroicon-o-plus-circle class="w-5 h-5 text-white" />
                        </div>
                        Editor Layout Kursi
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Klik pada kotak untuk mengubah tipe elemen secara detail menggunakan popup.
                    </p>
                </div>
                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <a wire:navigate href="{{ route('seat-layouts.index') }}"
                        class="btn btn-sm bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl transition-all">
                        <x-heroicon-o-arrow-left class="w-4 h-4" />
                        Kembali
                    </a>
                </div>
            </header>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="lg:col-span-4 xl:col-span-3 space-y-6">
                <!-- Config Card -->
                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm space-y-4">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Konfigurasi Layout</h2>

                    <div class="form-control w-full">
                        <label class="label">
                            <span
                                class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                Nama Layout
                            </span>
                        </label>
                        <input type="text" wire:model="form.name" placeholder="Misal: Jetbus 5 HDD 2-2"
                            class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all" />
                        @error('form.name')
                            <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div
                        class="flex items-center justify-between p-4 bg-zinc-100 dark:bg-zinc-800/50 rounded-2xl border transition-all duration-300 {{ $form->is_double_decker ? 'border-emerald-500/20 bg-emerald-500/5' : 'border-zinc-200 dark:border-zinc-700/50' }}">
                        <label class="cursor-pointer flex flex-col">
                            <span
                                class="text-[10px] font-black uppercase tracking-wider transition-colors duration-300 {{ $form->is_double_decker ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-500' }}">Double
                                Decker?</span>
                            <span
                                class="text-[9px] transition-colors duration-300 {{ $form->is_double_decker ? 'text-emerald-500/80' : 'text-zinc-400' }}">Aktifkan
                                untuk bus tingkat</span>
                        </label>
                        <input type="checkbox" wire:model.live="form.is_double_decker"
                            class="toggle toggle-sm {{ $form->is_double_decker ? 'toggle-success' : 'toggle-primary' }}" />
                    </div>

                    <div class="space-y-4 pt-2">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Grid Deck Aktif</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label pb-0">
                                    <span
                                        class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">Baris</span>
                                </label>
                                <input type="number" wire:model.defer="tempRows"
                                    class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all" />
                            </div>
                            <div class="form-control">
                                <label class="label pb-0">
                                    <span
                                        class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">Kolom</span>
                                </label>
                                <input type="number" wire:model.defer="tempCols"
                                    class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all" />
                            </div>
                        </div>
                        <button type="button" wire:click="applyGridChanges"
                            class="btn btn-sm w-full bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border-0 rounded-xl font-black uppercase tracking-widest text-[9px] hover:scale-[1.02] active:scale-95 transition-all py-3 h-auto mt-2">
                            <x-heroicon-o-squares-plus class="w-3.5 h-3.5 mr-2" />
                            Terapkan Grid
                        </button>
                    </div>
                </div>

                <!-- Legend Card -->
                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm space-y-4">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Legenda Tipe</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-1 gap-x-4 gap-y-2">
                        @foreach ([
        'available' => ['bg-zinc-200 dark:bg-zinc-700', 'Kosong / Jalan'],
        'seat' => ['bg-indigo-500 shadow-md ring-2 ring-indigo-500/20', 'Kursi Penumpang'],
        'driver' => ['bg-amber-500', 'Driver / Kru'],
        'door' => ['bg-emerald-500', 'Pintu / Gate'],
        'toilet' => ['bg-sky-500', 'Toilet'],
        'stairs_up' => ['bg-rose-500', 'Tangga (Naik)'],
        'stairs_down' => ['bg-rose-600', 'Tangga (Turun)'],
        'smoking' => ['bg-zinc-600', 'Smoking Area'],
        'pantry' => ['bg-teal-500', 'Pantry / Dapur'],
    ] as $type => $data)
                            <div class="flex items-center gap-3">
                                <div class="size-4 rounded {{ $data[0] }}"></div>
                                <span class="text-xs font-medium">{{ $data[1] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Middle: Grid Editor -->
            <div class="lg:col-span-5 xl:col-span-6 space-y-6">
                <!-- Deck Selector if Double Decker -->
                @if ($form->is_double_decker)
                    <div
                        class="flex gap-2 p-1 bg-zinc-100 dark:bg-zinc-900 rounded-2xl w-fit border border-zinc-200 dark:border-zinc-800">
                        @foreach ($form->decks as $index => $deck)
                            <button type="button" wire:click="$set('activeDeckIndex', {{ $index }})"
                                class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all 
                                {{ $activeDeckIndex === $index ? 'bg-white dark:bg-zinc-800 text-indigo-600 shadow-md' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                                {{ $deck['name'] }}
                            </button>
                        @endforeach
                    </div>
                @endif

                <div
                    class="bg-zinc-100 dark:bg-zinc-950/50 rounded-3xl sm:rounded-[40px] p-4 sm:p-8 border border-zinc-200 dark:border-zinc-800 shadow-inner overflow-auto">
                    <div class="inline-grid gap-1.5 sm:gap-2 p-4 sm:p-6 bg-white dark:bg-zinc-900 rounded-3xl sm:rounded-4xl border border-zinc-200 dark:border-zinc-800 shadow-xl"
                        style="grid-template-columns: repeat({{ $form->decks[$activeDeckIndex]['cols'] }}, minmax(0, 1fr));">

                        @foreach ($form->decks[$activeDeckIndex]['mapping'] as $index => $seat)
                            <div wire:key="grid-{{ $activeDeckIndex }}-{{ $index }}"
                                wire:click="openEditor({{ $activeDeckIndex }}, {{ $index }})"
                                class="size-14 sm:size-16 md:size-20 rounded-xl sm:rounded-2xl flex flex-col items-center justify-center cursor-pointer transition-all hover:scale-105 active:scale-95 shadow-sm border-2 
                                 {{ $seat['type'] === 'available' ? 'bg-zinc-50 dark:bg-zinc-950 border-zinc-100 dark:border-zinc-800' : '' }}
                                 {{ $seat['type'] === 'seat' ? 'bg-indigo-600 border-indigo-500 text-white shadow-lg shadow-indigo-600/20' : '' }}
                                 {{ $seat['type'] === 'driver' ? 'bg-amber-500 border-amber-400 text-white' : '' }}
                                 {{ $seat['type'] === 'door' ? 'bg-emerald-500 border-emerald-400 text-white' : '' }}
                                 {{ $seat['type'] === 'toilet' ? 'bg-sky-500 border-sky-400 text-white' : '' }}
                                 {{ str_contains($seat['type'], 'stairs') ? 'bg-rose-500 border-rose-400 text-white' : '' }}
                                 {{ $seat['type'] === 'smoking' ? 'bg-zinc-600 border-zinc-500 text-white' : '' }}
                                 {{ $seat['type'] === 'pantry' ? 'bg-teal-500 border-teal-400 text-white' : '' }}">

                                @if ($seat['type'] === 'seat')
                                    <span
                                        class="text-[7px] font-bold opacity-50 uppercase tracking-tighter truncate w-full text-center px-1 mb-0.5 leading-none">
                                        {{ $busClasses->find($seat['bus_class_id'] ?? 0)->name ?? 'SEAT' }}
                                    </span>
                                    <span class="text-base font-black leading-none">{{ $seat['seat_number'] }}</span>
                                @elseif($seat['type'] === 'driver')
                                    <x-heroicon-o-user-circle class="w-8 h-8 opacity-40" />
                                @elseif($seat['type'] === 'door')
                                    <x-heroicon-o-arrow-left-on-rectangle class="w-8 h-8 opacity-40" />
                                @elseif($seat['type'] === 'toilet')
                                    <x-heroicon-o-sparkles class="w-8 h-8 opacity-40" />
                                @elseif($seat['type'] === 'stairs')
                                    <x-heroicon-o-bars-3-bottom-left class="w-8 h-8 opacity-40 rotate-90" />
                                @elseif($seat['type'] === 'smoking')
                                    <x-heroicon-o-no-symbol class="w-8 h-8 opacity-40" />
                                @elseif($seat['type'] === 'pantry')
                                    <x-heroicon-o-cake class="w-8 h-8 opacity-40" />
                                @else
                                    <div class="size-2 rounded-full bg-zinc-200 dark:bg-zinc-800"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Right: Summary Panel -->
            <div class="lg:col-span-3 space-y-6">
                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm space-y-5 h-fit lg:sticky lg:top-8">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Ringkasan Kursi</h2>

                    <div class="space-y-4">
                        @php $counts = $this->getSeatCounts(); @endphp
                        @if (count($counts) > 0)
                            @foreach ($counts as $classId => $count)
                                <div
                                    class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-zinc-100 dark:border-zinc-700/50">
                                    <div class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mb-1">
                                        {{ $busClasses->find($classId)->name ?? 'Unknown' }}
                                    </div>
                                    <div class="flex items-end justify-between">
                                        <span class="text-2xl font-black text-zinc-900 dark:text-white leading-none">
                                            {{ $count }}
                                        </span>
                                        <span class="text-[10px] font-bold text-zinc-400">Kursi</span>
                                    </div>
                                </div>
                            @endforeach

                            <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Total
                                        Kursi</span>
                                    <span class="text-lg font-black text-indigo-600 dark:text-indigo-400">
                                        {{ array_sum($counts) }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div
                                class="py-8 text-center bg-zinc-50 dark:bg-zinc-800/30 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-700">
                                <x-heroicon-o-information-circle class="w-8 h-8 mx-auto text-zinc-300 mb-2" />
                                <p class="text-[10px] font-bold text-zinc-400 italic">Belum ada kursi dikonfigurasi</p>
                            </div>
                        @endif
                    </div>

                    <div class="pt-2">
                        <button type="submit" wire:loading.attr="disabled" wire:click="save"
                            class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-0 px-8 rounded-2xl shadow-lg shadow-indigo-200 dark:shadow-none flex items-center justify-center gap-2 group transition-all">
                            <x-heroicon-o-check-circle wire:loading.remove wire:target="save"
                                class="w-5 h-5 group-hover:scale-110 transition-transform" />
                            <span wire:loading wire:target="save" class="loading loading-spinner loading-sm"></span>
                            Simpan Layout
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Editor -->
        @if ($showEditorModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-sm"
                    wire:click="$set('showEditorModal', false)">
                </div>
                <div
                    class="relative bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl border border-zinc-200 dark:border-zinc-800 w-full max-w-md overflow-hidden animate-zoom-in">
                    <div class="p-6 border-b border-zinc-100 dark:border-zinc-800">
                        <h3 class="font-black text-lg text-zinc-900 dark:text-white uppercase tracking-tight">Edit
                            Elemen
                            Grid</h3>
                        <p class="text-xs text-zinc-500">Posisi: Baris {{ $tempBaris ?? '?' }}, Kolom
                            {{ $tempKolom ?? '?' }}
                        </p>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="form-control">
                            <label class="label"><span class="label-text-alt font-black uppercase text-zinc-400">Tipe
                                    Elemen</span></label>
                            <select wire:model.live="tempType"
                                class="select select-bordered w-full bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl font-bold">
                                <option value="available">Kosong / Jalan</option>
                                <option value="seat">Kursi Penumpang</option>
                                <option value="driver">Driver / Kru</option>
                                <option value="door">Pintu / Gate</option>
                                <option value="toilet">Toilet</option>
                                <option value="stairs">Tangga / Akses</option>
                                <option value="smoking">Smoking Area</option>
                                <option value="pantry">Pentri</option>
                            </select>
                        </div>

                        @if ($tempType === 'seat')
                            <div class="grid grid-cols-2 gap-4 animate-fade-in">
                                <div class="form-control">
                                    <label class="label"><span
                                            class="label-text-alt font-black uppercase text-zinc-400">No.
                                            Kursi</span></label>
                                    <input type="text" wire:model="tempSeatNumber"
                                        class="input input-bordered w-full rounded-2xl bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800"
                                        placeholder="e.g. 1A" />
                                </div>
                                <div class="form-control">
                                    <label class="label"><span
                                            class="label-text-alt font-black uppercase text-zinc-400">Kelas
                                            Bus</span></label>
                                    <select wire:model="tempBusClassId"
                                        class="select select-bordered w-full rounded-2xl bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($busClasses as $bc)
                                            <option value="{{ $bc->id }}">{{ $bc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="p-6 bg-zinc-50 dark:bg-zinc-950 flex justify-end gap-3">
                        <button wire:click="$set('showEditorModal', false)"
                            class="btn btn-ghost rounded-xl px-6 text-zinc-500">Batal</button>
                        <button wire:click="saveElement"
                            class="btn btn-indigo rounded-xl px-8 shadow-lg shadow-indigo-600/30">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
