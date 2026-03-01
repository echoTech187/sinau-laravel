<div class="container relative min-h-screen pb-10">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div
            class="absolute top-0 right-0 w-[600px] h-[600px] bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-1/4 w-[500px] h-[500px] bg-teal-500/10 dark:bg-teal-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8">
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/50 pb-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/30">
                            <x-heroicon-o-user-plus class="w-6 h-6 text-white" />
                        </div>
                        Tambah Kru Baru
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Daftarkan data awak bus operasional baru ke dalam sistem.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a wire:navigate href="{{ route('crews.index') }}"
                        class="btn btn-sm bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl transition-all">
                        <x-heroicon-o-arrow-left class="w-4 h-4" />
                        Kembali
                    </a>
                </div>
            </header>
        </div>

        <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm animate-fade-in-up"
            style="animation-delay: 0.1s">
            <form id="crewForm" wire:submit="saveCrew" class="space-y-8">
                <div>
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-4 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-identification class="w-4 h-4" />
                        Informasi Personal & Pekerjaan
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Nama Lengkap*
                                </span>
                            </label>
                            <input type="text" wire:model="form.name"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all"
                                placeholder="Nama Lengkap Sesuai KTP" />
                            @error('form.name')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-control w-full">
                                <label class="label pb-0">
                                    <span
                                        class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                        Nomor Induk Karyawan*
                                    </span>
                                </label>
                                <input type="text" wire:model="form.employee_number"
                                    class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold"
                                    placeholder="EMP-2023-001" />
                            </div>
                            <div class="form-control w-full">
                                <label class="label pb-0">
                                    <span
                                        class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                        No. Handphone*
                                    </span>
                                </label>
                                <input type="text" wire:model="form.phone_number"
                                    class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold"
                                    placeholder="08123456789" />
                            </div>
                        </div>

                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Posisi Kru*
                                </span>
                            </label>
                            <select wire:model="form.crew_position_id"
                                class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                                <option value="">Pilih Posisi Penugasan</option>
                                @foreach ($this->crewPositions as $cp)
                                    <option value="{{ $cp->id }}">{{ $cp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-4 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-document-check class="w-4 h-4" />
                        Dokumen Legalitas & Status Layanan
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    No. Lisensi (SIM/SIPA)
                                </span>
                            </label>
                            <input type="text" wire:model="form.license_number"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-mono"
                                placeholder="Opsional untuk Asisten" />
                        </div>

                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Exp. Lisensi Utama
                                </span>
                            </label>
                            <input type="date" wire:model="form.license_expired_at"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all" />
                        </div>

                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Status Kru*
                                </span>
                            </label>
                            <select wire:model="form.status"
                                class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                                <option value="active">Active (Tersedia Beroperasi)</option>
                                <option value="on_leave">On Leave (Cuti / Standby)</option>
                                <option value="suspended">Suspended (Skorsing)</option>
                                <option value="inactive">Inactive (Keluar/Pensiun)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-zinc-100 dark:border-zinc-800 flex justify-end">
                    <button type="submit"
                        class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-0 px-10 h-14 rounded-2xl shadow-xl shadow-indigo-600/30 font-black tracking-tight text-lg transition-all group overflow-hidden relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer">
                        </div>
                        <span wire:loading.remove wire:target="saveCrew" class="flex items-center gap-3 relative z-10">
                            <x-heroicon-o-check-circle class="w-6 h-6" />
                            DAFTARKAN KRU BARU
                        </span>
                        <span wire:loading wire:target="saveCrew"
                            class="loading loading-spinner loading-md relative z-10"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
