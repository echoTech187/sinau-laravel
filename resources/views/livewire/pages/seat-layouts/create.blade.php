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
                        Editor Layout Kursi
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Klik pada kotak untuk mengubah tipe elemen (Kursi, Pintu, Toilet, dll).
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

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="lg:col-span-1 space-y-6">
                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm space-y-4">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Konfigurasi Grid</h2>

                    <div class="form-control w-full">
                        <label class="label pb-0">
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

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Baris
                                </span>
                            </label>
                            <input type="number" wire:model.live="form.grid_rows"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all" />
                        </div>
                        <div class="form-control">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Kolom
                                </span>
                            </label>
                            <input type="number" wire:model.live="form.grid_columns"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all" />
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm space-y-4">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Legenda Tipe</h2>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <div class="size-4 rounded bg-zinc-200 dark:bg-zinc-700"></div><span
                                class="text-xs font-medium">Kosong / Jalan</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="size-4 rounded bg-indigo-500 shadow-md ring-2 ring-indigo-500/20"></div><span
                                class="text-xs font-medium">Kursi Penumpang</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="size-4 rounded bg-amber-500"></div><span class="text-xs font-medium">Driver /
                                Kru</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="size-4 rounded bg-emerald-500"></div><span class="text-xs font-medium">Pintu /
                                Gate</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="size-4 rounded bg-sky-500"></div><span class="text-xs font-medium">Toilet /
                                Kamar Mandi</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="size-4 rounded bg-rose-500"></div><span class="text-xs font-medium">Tangga /
                                Akses</span>
                        </div>
                    </div>
                    <p
                        class="text-[9px] text-zinc-400 leading-relaxed italic border-t border-zinc-100 dark:border-zinc-800 pt-4">
                        Tip: Klik pada grid untuk mengubah tipe elemen secara berurutan sesuai legenda di atas.</p>
                </div>
            </div>

            <!-- Right: Grid Editor -->
            <div class="lg:col-span-3">
                <div
                    class="bg-zinc-100 dark:bg-zinc-950/50 rounded-[40px] p-8 border border-zinc-200 dark:border-zinc-800 shadow-inner perspective-1000 overflow-auto">
                    <div class="inline-grid gap-2 p-6 bg-white dark:bg-zinc-900 rounded-[32px] border border-zinc-200 dark:border-zinc-800 shadow-xl"
                        style="grid-template-columns: repeat({{ $form->grid_columns }}, minmax(0, 1fr));">

                        @foreach ($form->layout_mapping as $index => $seat)
                            <div wire:click="toggleSeat({{ $index }})"
                                class="size-16 sm:size-20 rounded-2xl flex flex-col items-center justify-center cursor-pointer transition-all hover:scale-105 active:scale-95 shadow-sm border-2 
                                 {{ $seat['type'] === 'available' ? 'bg-zinc-50 dark:bg-zinc-950 border-zinc-100 dark:border-zinc-800' : '' }}
                                 {{ $seat['type'] === 'seat' ? 'bg-indigo-600 border-indigo-500 text-white shadow-lg shadow-indigo-600/20' : '' }}
                                 {{ $seat['type'] === 'driver' ? 'bg-amber-500 border-amber-400 text-white' : '' }}
                                 {{ $seat['type'] === 'door' ? 'bg-emerald-500 border-emerald-400 text-white' : '' }}
                                 {{ $seat['type'] === 'toilet' ? 'bg-sky-500 border-sky-400 text-white' : '' }}
                                 {{ $seat['type'] === 'stairs' ? 'bg-rose-500 border-rose-400 text-white' : '' }}">

                                @if ($seat['type'] === 'seat')
                                    <span
                                        class="text-xs font-black opacity-40 uppercase tracking-widest leading-none">SEAT</span>
                                    <input type="text"
                                        wire:model="form.layout_mapping.{{ $index }}.seat_number"
                                        class="w-full bg-transparent border-0 text-center text-lg font-black focus:ring-0 p-0 text-white selection:bg-white/20"
                                        onclick="event.stopPropagation()" />
                                @elseif($seat['type'] === 'driver')
                                    <x-heroicon-o-user-circle class="w-8 h-8 opacity-40" />
                                    <span class="text-[8px] font-black uppercase mt-1">Driver</span>
                                @elseif($seat['type'] === 'door')
                                    <x-heroicon-o-arrow-left-on-rectangle class="w-8 h-8 opacity-40" />
                                    <span class="text-[8px] font-black uppercase mt-1">Door</span>
                                @elseif($seat['type'] === 'toilet')
                                    <x-heroicon-o-sparkles class="w-8 h-8 opacity-40" />
                                    <span class="text-[8px] font-black uppercase mt-1">Toilet</span>
                                @elseif($seat['type'] === 'stairs')
                                    <x-heroicon-o-bars-3-bottom-left class="w-8 h-8 opacity-40 rotate-90" />
                                    <span class="text-[8px] font-black uppercase mt-1">Stairs</span>
                                @else
                                    <div
                                        class="size-2 rounded-full bg-zinc-200 dark:bg-zinc-800 group-hover:bg-indigo-400 transition-colors">
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="pt-8 flex justify-end">
                    <button wire:click="save"
                        class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-0 px-10 h-14 rounded-2xl shadow-xl shadow-indigo-600/30 font-black tracking-tight text-lg transition-all group overflow-hidden relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer">
                        </div>
                        <span wire:loading.remove wire:target="save" class="flex items-center gap-3 relative z-10">
                            <x-heroicon-o-check-circle class="w-6 h-6" />
                            SIMPAN LAYOUT
                        </span>
                        <span wire:loading wire:target="save"
                            class="loading loading-spinner loading-md relative z-10"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
