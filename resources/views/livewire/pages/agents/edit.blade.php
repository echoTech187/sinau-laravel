<div class="container relative min-h-screen pb-10">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div
            class="absolute top-0 right-0 w-[600px] h-[600px] bg-sky-500/10 dark:bg-sky-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-1/4 w-[500px] h-[500px] bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8">
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/50 pb-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/30">
                            <x-heroicon-o-pencil-square class="w-6 h-6 text-white" />
                        </div>
                        Ubah Informasi Agen: {{ $agent->name }}
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Lakukan penyesuaian untuk komisi dan entitas relasi agen ini.
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-control w-full">
                                <label class="label pb-0">
                                    <span
                                        class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                        Kode Agen*
                                    </span>
                                </label>
                                <input type="text" wire:model="form.agent_code"
                                    class="input input-bordered uppercase bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold placeholder:font-normal placeholder:lowercase opacity-60 cursor-not-allowed"
                                    placeholder="AGT-001" disabled />
                                @error('form.agent_code')
                                    <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-control w-full">
                                <label class="label pb-0">
                                    <span
                                        class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                        Tipe Agen*
                                    </span>
                                </label>
                                <select wire:model="form.type"
                                    class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                                    <option value="branch_office">Kantor Cabang Internal</option>
                                    <option value="partner_exclusive">Mitra Agen Eksklusif</option>
                                    <option value="partner_general">Mitra Agen Reguler</option>
                                </select>
                                @error('form.type')
                                    <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Nama / Perusahaan Agen*
                                </span>
                            </label>
                            <input type="text" wire:model="form.name"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold"
                                placeholder="Nama Agen / Cabang" />
                            @error('form.name')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    No. Telepon / WhatsApp*
                                </span>
                            </label>
                            <input type="text" wire:model="form.phone_number"
                                class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold"
                                placeholder="08..." />
                            @error('form.phone_number')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Titik Lokasi Terintegrasi
                                </span>
                            </label>
                            <select wire:model="form.location_id"
                                class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                                <option value="">Tanpa Titik Lokasi Peta</option>
                                @foreach ($this->locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                            @error('form.location_id')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h4
                        class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-4 flex items-center gap-2 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-currency-dollar class="w-4 h-4" />
                        Pengaturan Hierarki & Skema Komisi
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="form-control w-full">
                            <label class="label pb-0">
                                <span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                    Induk / Cabang Utama
                                </span>
                            </label>
                            <select wire:model="form.parent_branch_id"
                                class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                                <option value="">Berdiri Sendiri (Pusat)</option>
                                @foreach ($this->branchOffices as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            @error('form.parent_branch_id')
                                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div
                            class="form-control w-full border border-sky-100 dark:border-sky-900 rounded-xl p-3 bg-sky-50/50 dark:bg-sky-900/10">
                            <label class="label pb-0"><span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">Tipe
                                    Skema
                                    Komisi*</span></label>
                            <div class="flex gap-4">
                                <label class="cursor-pointer flex items-center gap-2">
                                    <input type="radio" wire:model="form.commission_type" value="percentage"
                                        class="radio radio-primary radio-sm" />
                                    <span class="label-text font-medium">Persentase (%)</span>
                                </label>
                                <label class="cursor-pointer flex items-center gap-2">
                                    <input type="radio" wire:model="form.commission_type" value="flat"
                                        class="radio radio-primary radio-sm" />
                                    <span class="label-text font-medium">Flat (Rp)</span>
                                </label>
                            </div>
                            @error('form.commission_type')
                                <span class="text-[10px] text-red-500 mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div
                            class="form-control w-full border border-sky-100 dark:border-sky-900 rounded-xl p-3 bg-sky-50/50 dark:bg-sky-900/10">
                            <label class="label pb-0"><span
                                    class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">Nilai
                                    Komisi
                                    Dasar*</span></label>
                            <div class="input-group flex items-center">
                                <span
                                    class="text-sm px-3 text-zinc-500 bg-white dark:bg-zinc-800 rounded-l-lg border border-r-0 border-zinc-200 dark:border-zinc-700 py-3">{{ $form->commission_type === 'percentage' ? '%' : 'Rp' }}</span>
                                <input type="number" step="0.01" wire:model="form.commission_value"
                                    class="input input-sm border-l-0 rounded-l-none w-full border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 focus:outline-none" />
                            </div>
                            @error('form.commission_value')
                                <span class="text-[10px] text-red-500 mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-control w-full max-w-xs mt-4">
                    <label class="label pb-0">
                        <span
                            class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                            Status Operasional*
                        </span>
                    </label>
                    <select wire:model="form.status"
                        class="select select-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                        <option value="active">Active (Dapat Menjual)</option>
                        <option value="inactive">Inactive</option>
                        <option value="banned">Banned (Masalah Akun)</option>
                    </select>
                    @error('form.status')
                        <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-8 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-3 mt-8">
                    <button type="submit"
                        class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-0 px-10 h-14 rounded-2xl shadow-xl shadow-indigo-600/30 font-black tracking-tight text-lg transition-all group overflow-hidden relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer">
                        </div>
                        <span wire:loading.remove wire:target="saveAgent"
                            class="flex items-center gap-3 relative z-10">
                            <x-heroicon-o-check-circle class="w-6 h-6" />
                            SIMPAN PERUBAHAN
                        </span>
                        <span wire:loading wire:target="saveAgent"
                            class="loading loading-spinner loading-md relative z-10"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
