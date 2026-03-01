<div x-data="{ roleModalOpen: false }" @close-modal.window="if($event.detail === 'create-role') roleModalOpen = false"
    class="space-y-6">
    <form wire:submit="save" class="my-6 w-full space-y-8 animate-fade-in-up" style="animation-delay: 0.2s">
        <!-- Basic Info Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="form-control w-full">
                <label class="label pb-0">
                    <span class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                        Nama Lengkap*
                    </span>
                </label>
                <input wire:model="name" type="text" required autofocus placeholder="Nama Lengkap"
                    class="input input-bordered w-full bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500/20" />
                @error('name')
                    <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-control w-full">
                <label class="label pb-0">
                    <span class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                        Email*
                    </span>
                </label>
                <input wire:model="email" type="email" required placeholder="email@example.com"
                    class="input input-bordered w-full bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500/20" />
                @error('email')
                    <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-control w-full">
            <label class="label pb-0">
                <span class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                    Hak Akses (Role Utama)*
                </span>
            </label>
            <div class="flex gap-3">
                <div class="flex-1">
                    <select wire:model="role_id" required
                        class="select select-bordered w-full bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500/20">
                        <option value="">Pilih Hak Akses</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->role }}</option>
                        @endforeach
                    </select>
                </div>
                @can('rbac.roles.manage')
                    <button type="button" @click="roleModalOpen = true"
                        class="btn btn-square rounded-xl bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 border-zinc-200 dark:border-zinc-700">
                        <x-heroicon-o-plus class="size-5 text-zinc-600 dark:text-zinc-400" />
                    </button>
                @endcan
            </div>
            @error('role_id')
                <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
            @enderror
        </div>

        <!-- Security Section -->
        <div
            class="p-8 rounded-3xl bg-blue-50/30 dark:bg-blue-900/10 border border-blue-100/50 dark:border-blue-800/20 backdrop-blur-sm">
            <div class="flex items-center gap-4 mb-8">
                <div
                    class="p-3 bg-blue-100 dark:bg-blue-900/40 rounded-2xl text-blue-600 dark:text-blue-400 shadow-sm shadow-blue-500/10">
                    <x-heroicon-o-key class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 leading-tight">
                        {{ $user->id ? 'Keamanan & Sandi' : 'Buat Sandi Baru' }}
                    </h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 font-medium">
                        {{ $user->id ? 'Kosongkan jika tidak ingin mengganti sandi saat ini.' : 'Sandi wajib diisi minimal 8 karakter.' }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="form-control w-full">
                    <label class="label pb-0">
                        <span class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                            Katasandi {{ $user->id ? '(Optional)' : '*' }}
                        </span>
                    </label>
                    <input wire:model="password" type="password" autocomplete="new-password" placeholder="••••••••"
                        :required="!@js($user->id)"
                        class="input input-bordered w-full bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500/20" />
                    @error('password')
                        <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control w-full">
                    <label class="label pb-0">
                        <span class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                            Konfirmasi Sandi {{ $user->id ? '(Optional)' : '*' }}
                        </span>
                    </label>
                    <input wire:model="confirm_password" type="password" autocomplete="new-password"
                        placeholder="••••••••" :required="!@js($user->id)"
                        class="input input-bordered w-full bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500/20" />
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end w-full mt-10 pt-8 border-t border-zinc-100 dark:border-zinc-800/50">
            <button type="submit"
                class="btn bg-linear-to-br from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white border-none shadow-xl shadow-blue-500/20 rounded-2xl px-12 h-14 transition-all hover:-translate-y-1 font-black uppercase tracking-widest text-[10px]">
                {{ $user->id ? 'Simpan Perubahan' : 'Daftarkan Pengguna' }}
            </button>
        </div>
    </form>

    <!-- Create Role Modal -->
    <div x-show="roleModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">

        <div @click.outside="roleModalOpen = false" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-8 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            class="w-full max-w-md bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800">

            <form wire:submit="createRole">
                <div class="p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl text-indigo-600 dark:text-indigo-400">
                            <x-heroicon-o-shield-check class="w-7 h-7" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-zinc-900 dark:text-zinc-100 leading-tight">Tambah Hak
                                Akses</h3>
                            <p class="text-sm text-zinc-500 mt-1">Buat level akses baru di sistem.</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest ml-1">Nama Hak
                            Akses</label>
                        <input wire:model="role" type="text" placeholder="e.g. 'Supervisor'" required
                            class="input input-bordered w-full bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl focus:ring-2 focus:ring-indigo-500/20" />
                        @error('role')
                            <span class="text-xs text-red-500 ml-1 font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div
                    class="px-8 py-6 bg-zinc-50 dark:bg-zinc-950/50 flex justify-end gap-3 border-t border-zinc-100 dark:border-zinc-800">
                    <button type="button" @click="roleModalOpen = false"
                        class="btn btn-ghost hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded-xl text-zinc-600 dark:text-zinc-400 font-bold px-6">
                        Batal
                    </button>
                    <button type="submit"
                        class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-none rounded-xl font-bold px-8 shadow-lg shadow-indigo-600/20">
                        Simpan Hak Akses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
