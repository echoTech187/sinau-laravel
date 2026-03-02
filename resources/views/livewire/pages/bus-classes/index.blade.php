@php
    /** @var \App\Livewire\Pages\BusClasses\Index $this */
@endphp
<div class="container relative min-h-full pb-20">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-125 h-125 bg-sky-500/5 rounded-full blur-[100px]"></div>
    </div>

    <div class="relative z-10 space-y-8">
        <header
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-6 animate-fade-in-up">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                    <div
                        class="p-2.5 rounded-2xl bg-linear-to-br from-sky-500 to-indigo-600 shadow-lg shadow-sky-500/20 text-white">
                        <x-heroicon-o-star class="w-6 h-6" />
                    </div>
                    Kelas & Fasilitas Armada
                </h1>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Pengaturan kelas layanan bus dan daftar
                    fasilitas pendukung kenyamanan penumpang.</p>
            </div>
            <!-- Action Buttons -->
            <div
                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 mt-4 sm:mt-0 w-full sm:w-auto">
                <button wire:click="$set('showingFacilityModal', true)"
                    class="btn btn-md bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-2xl shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-all font-bold">
                    <x-heroicon-o-sparkles class="w-5 h-5 text-sky-500" />
                    Manajemen Fasilitas
                </button>
                <a wire:navigate href="{{ route('bus-classes.create') }}"
                    class="btn btn-md bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/20 rounded-2xl transition-all hover:-translate-y-0.5 font-bold">
                    <x-heroicon-o-plus class="w-5 h-5" />
                    Tambah Kelas
                </a>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Left: Bus Classes Table -->
            <div class="lg:col-span-3 space-y-6 animate-fade-in-up" style="animation-delay: 0.1s">
                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="table table-sm w-full">
                            <thead>
                                <tr
                                    class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider text-[10px]">
                                    <th class="py-4 pl-6">Nama Kelas</th>
                                    <th class="py-4 text-center">Bagasi Gratis</th>
                                    <th class="py-4">Fasilitas Unggulan</th>
                                    <th class="py-4 text-center">Unit Bus</th>
                                    <th class="py-4 text-right pr-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @if (count($this->busClasses) > 0)
                                    @foreach ($this->busClasses as $bc)
                                        <tr wire:key="bc-{{ $bc->id }}"
                                            class="border-b border-zinc-100 dark:border-zinc-800/50 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                            <td class="py-4 pl-6">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="font-bold text-zinc-900 dark:text-white">{{ $bc->name }}</span>
                                                    <span
                                                        class="text-[10px] text-zinc-400 max-w-xs truncate">{{ $bc->description ?? 'Deskripsi belum diisi.' }}</span>
                                                </div>
                                            </td>
                                            <td class="py-4 text-center">
                                                <span
                                                    class="px-2.5 py-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-[11px] font-bold text-zinc-600 dark:text-zinc-400">
                                                    {{ $bc->free_baggage_kg }} KG
                                                </span>
                                            </td>
                                            <td class="py-4">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach ($bc->facilities->take(3) as $f)
                                                        <span wire:key="bc-fac-{{ $bc->id }}-{{ $f->id }}"
                                                            class="p-1 rounded-lg bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-500"
                                                            title="{{ $f->name }}">
                                                            <x-dynamic-component :component="$f->icon"
                                                                class="w-3.5 h-3.5" />
                                                        </span>
                                                    @endforeach
                                                    @if ($bc->facilities->count() > 3)
                                                        <span
                                                            class="text-[9px] font-bold text-zinc-400 flex items-center pl-1">+{{ $bc->facilities->count() - 3 }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-4 text-center">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm font-black text-zinc-800 dark:text-zinc-200">{{ $bc->buses_count }}</span>
                                                    <span
                                                        class="text-[9px] uppercase font-bold text-zinc-400">Armada</span>
                                                </div>
                                            </td>
                                            <td class="py-4 text-right pr-6">
                                                <div class="flex items-center justify-end gap-1">
                                                    <a wire:navigate href="{{ route('bus-classes.edit', $bc->id) }}"
                                                        class="p-2 text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-colors">
                                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                                    </a>
                                                    <button wire:click="confirmDeleteBusClass({{ $bc->id }})"
                                                        class="p-2 text-zinc-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                                        <x-heroicon-o-trash class="w-4 h-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="py-20 text-center text-zinc-500">
                                            <x-heroicon-o-star class="w-12 h-12 mx-auto mb-3 opacity-20" />
                                            <p class="font-medium text-lg text-zinc-800 dark:text-white">Belum Ada
                                                Kelas
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right: Facility List Quick View -->
            <div class="lg:col-span-1 space-y-6 animate-fade-in-up" style="animation-delay: 0.2s">
                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
                    <h2 class="text-xs font-black uppercase tracking-widest text-zinc-400 mb-6 flex items-center gap-2">
                        <x-heroicon-o-check-badge class="w-4 h-4" />
                        Daftar Fasilitas
                    </h2>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($this->facilities as $f)
                            <div wire:key="fac-item-{{ $f->id }}"
                                class="flex items-center gap-2 p-2 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 group relative">
                                <div class="p-2 rounded-lg bg-zinc-50 dark:bg-zinc-900 text-zinc-500">
                                    <x-dynamic-component :component="$f->icon" class="w-4 h-4" />
                                </div>
                                <span
                                    class="text-[10px] font-bold text-zinc-600 dark:text-zinc-400 truncate">{{ $f->name }}</span>
                                <button wire:click="deleteFacility({{ $f->id }})"
                                    class="absolute -top-1 -right-1 size-5 rounded-full bg-red-500 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-sm scale-75">
                                    <x-heroicon-o-x-mark class="w-3 h-3" />
                                </button>
                            </div>
                        @endforeach
                        <button wire:click="$set('showingFacilityModal', true)"
                            class="flex flex-col items-center justify-center gap-1 p-2 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 hover:border-sky-500 hover:bg-sky-50 dark:hover:bg-sky-950/30 transition-all text-zinc-400 hover:text-sky-600">
                            <x-heroicon-o-plus-circle class="w-5 h-5" />
                            <span class="text-[9px] font-bold">Baru</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Facility Modal -->
    <div class="modal {{ $showingFacilityModal ? 'modal-open' : '' }}" role="dialog">
        <div
            class="modal-box bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-8 max-w-sm">
            <h3 class="font-black text-xl text-zinc-900 dark:text-white">Tambah Fasilitas</h3>
            <p class="text-sm text-zinc-500 mt-1">Gunakan icon Heroicons (format: heroicon-o-name)</p>

            <div class="mt-6 space-y-4">
                <div class="form-control w-full">
                    <label class="label"><span
                            class="label-text font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-widest text-[10px]">Nama
                            Fasilitas</span></label>
                    <input type="text" wire:model="newFacilityName" placeholder="Contoh: AC Central"
                        class="input input-bordered bg-zinc-50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-2xl focus:ring-2 focus:ring-sky-500/20" />
                </div>
                <div class="form-control w-full">
                    <label class="label"><span
                            class="label-text font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-widest text-[10px]">Heroicon
                            Class</span></label>
                    <input type="text" wire:model="newFacilityIcon" placeholder="heroicon-o-wifi"
                        class="input input-bordered bg-zinc-50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-2xl focus:ring-2 focus:ring-sky-500/20" />
                </div>
            </div>

            <div class="modal-action mt-8">
                <button wire:click="$set('showingFacilityModal', false)" class="btn btn-ghost">Batal</button>
                <button wire:click="addFacility"
                    class="btn bg-sky-600 hover:bg-sky-700 text-white border-0 shadow-lg shadow-sky-600/20 rounded-2xl px-8 font-black uppercase tracking-widest text-[10px]">
                    Simpan
                </button>
            </div>
        </div>
        <div class="modal-backdrop bg-zinc-900/60 backdrop-blur-sm" wire:click="$set('showingFacilityModal', false)">
        </div>
    </div>

    <!-- Confirm Class Delete -->
    <div class="modal {{ $confirmingBusClassDeletion ? 'modal-open' : '' }}" role="dialog">
        <div class="modal-box bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-8">
            <h3 class="font-bold text-lg text-red-600">Hapus Kelas Bus?</h3>
            <p class="py-4 text-zinc-600 dark:text-zinc-400 text-sm">Menghapus kelas bus tidak bisa dibatalkan jika
                sudah ada armada yang terdaftar di kelas ini.</p>
            <div class="modal-action">
                <button wire:click="$set('confirmingBusClassDeletion', false)" class="btn btn-ghost">Batal</button>
                <button wire:click="deleteBusClass"
                    class="btn bg-red-600 hover:bg-red-700 text-white border-0 shadow-md">Ya, Hapus!</button>
            </div>
        </div>
        <div class="modal-backdrop bg-zinc-900/60" wire:click="$set('confirmingBusClassDeletion', false)"></div>
    </div>
</div>
