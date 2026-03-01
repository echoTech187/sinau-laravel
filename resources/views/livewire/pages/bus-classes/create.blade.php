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
                        Tambah Kelas Layanan
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Definisikan kelas bus baru (misal: Eksekutif, Sleeper) beserta fasilitas unggulannya.
                    </p>
                </div>
                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <a wire:navigate href="{{ route('bus-classes.index') }}"
                        class="btn btn-sm bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl transition-all">
                        <x-heroicon-o-arrow-left class="w-4 h-4" />
                        Kembali
                    </a>
                </div>
            </header>
        </div>

        <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm animate-fade-in-up"
            style="animation-delay: 0.1s">
            <form wire:submit="save" class="space-y-8">
                <div class="space-y-6">
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-information-circle class="w-4 h-4" />
                        Identitas & Detail Kelas
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Nama Kelas
                                </span>
                            </label>
                            <input type="text" wire:model="form.name" placeholder="Contoh: Eksekutif Plus"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all h-14" />
                            @error('form.name')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.25em] text-zinc-400 mb-2 text-pretty">
                                    Bagasi Gratis (KG)
                                </span>
                            </label>
                            <div class="relative">
                                <input type="number" wire:model="form.free_baggage_kg" placeholder="20"
                                    class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all h-14 w-full pr-12" />
                                <span
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-zinc-400 tracking-widest">KG</span>
                            </div>
                            @error('form.free_baggage_kg')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-control w-full">
                        <label class="label pb-0">
                            <span
                                class="label-text text-[10px] font-black uppercase tracking-[0.25em] text-zinc-400 mb-2">
                                Keterangan / Deskripsi
                            </span>
                        </label>
                        <textarea wire:model="form.description" placeholder="Jelaskan detail layanan kelas ini..."
                            class="textarea textarea-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all h-32 leading-relaxed"></textarea>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="flex items-center justify-between pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-2">
                            <x-heroicon-o-check-badge class="w-4 h-4" />
                            Pilih Fasilitas
                        </h4>
                        <span
                            class="text-[10px] font-black text-zinc-400 uppercase bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded-lg">{{ count($form->facility_ids) }}
                            Terpilih</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach ($facilities as $f)
                            <label
                                class="group relative flex items-center gap-3 p-4 rounded-2xl border-2 transition-all cursor-pointer {{ in_array($f->id, $form->facility_ids) ? 'bg-indigo-50/50 dark:bg-indigo-950/20 border-indigo-500/50' : 'bg-white dark:bg-zinc-900/50 border-zinc-100 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700' }}">
                                <input type="checkbox" wire:model="form.facility_ids" value="{{ $f->id }}"
                                    class="hidden" />
                                <div
                                    class="p-2 rounded-xl {{ in_array($f->id, $form->facility_ids) ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/30' : 'bg-zinc-100 dark:bg-zinc-950 text-zinc-500 group-hover:bg-zinc-200 transition-colors' }}">
                                    <x-dynamic-component :component="$f->icon" class="w-5 h-5" />
                                </div>
                                <span
                                    class="text-xs font-bold {{ in_array($f->id, $form->facility_ids) ? 'text-indigo-700 dark:text-indigo-400' : 'text-zinc-600 dark:text-zinc-400' }}">{{ $f->name }}</span>

                                @if (in_array($f->id, $form->facility_ids))
                                    <div
                                        class="absolute top-2 right-2 flex items-center justify-center p-0.5 rounded-full bg-indigo-500 text-white shadow-sm ring-4 ring-indigo-50 dark:ring-indigo-900/20">
                                        <x-heroicon-o-check class="w-2 h-2" />
                                    </div>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>
                <!-- Action Footer -->
                <div class="pt-8 flex justify-end">
                    <button type="submit"
                        class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-0 px-10 h-14 rounded-2xl shadow-xl shadow-indigo-600/30 font-black tracking-tight text-lg transition-all group overflow-hidden relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer">
                        </div>
                        <span wire:loading.remove wire:target="save" class="flex items-center gap-3 relative z-10">
                            <x-heroicon-o-check-circle class="w-6 h-6" />
                            SIMPAN KELAS
                        </span>
                        <span wire:loading wire:target="save"
                            class="loading loading-spinner loading-md relative z-10"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
