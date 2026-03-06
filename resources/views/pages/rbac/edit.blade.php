<?php
use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Roles;
use App\Helpers\AuditLogger;

new class extends Component {
    public Roles $roles;

    #[Validate('required|string|max:255')]
    public string $roleName = '';
    #[Validate('nullable|string|max:500')]
    public string $roleDescription = '';
    public bool $roleIsActive = true;
    public bool $confirmingDeletion = false;

    public function mount(Roles $roles)
    {
        $this->roles = $roles;
        $this->roleName = $roles->role;
        $this->roleDescription = $roles->description ?? '';
        $this->roleIsActive = (bool) $roles->is_active;
    }

    public function save(): void
    {
        $this->validate();
        $this->roles->update([
            'role' => $this->roleName,
            'slug' => \Illuminate\Support\Str::slug($this->roleName),
            'description' => $this->roleDescription,
            'is_active' => $this->roleIsActive,
        ]);
        AuditLogger::record(AuditLogger::ROLE_UPDATED, null, ['role' => $this->roleName, 'role_id' => $this->roles->id]);
        $this->dispatch('notify', type: 'success', message: 'Data peran berhasil diperbarui!');
    }

    public function confirmDelete(): void
    {
        $this->delete();
        $this->confirmingDeletion = false;
    }

    public function delete(): void
    {
        $roleName = $this->roles->role;
        $roleId = $this->roles->id;
        $this->roles->delete();
        AuditLogger::record(AuditLogger::ROLE_DELETED, null, ['role' => $roleName, 'role_id' => $roleId]);
        $this->redirect(route('rbac.show'));
    }
};
?>

<div class="container space-y-5 h-full">
    @include('partials.heading', [
        'title' => 'Edit Peran: ' . $this->roles->role,
        'description' => 'Perbarui nama, deskripsi, dan status peran.',
    ])

    {{-- Breadcrumb --}}
    <nav class="flex items-center flex-wrap gap-2 text-sm text-zinc-500 dark:text-zinc-400 -mt-4">
        <a href="{{ route('rbac.show') }}" class="hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-1">
            <x-heroicon-o-shield-check class="size-4" />RBAC Manager
        </a>
        <x-heroicon-o-chevron-right class="size-4 text-zinc-300 dark:text-zinc-600" />
        <span class="text-zinc-800 dark:text-zinc-200 font-medium">Edit: {{ $this->roles->role }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Form Edit --}}
        <div class="lg:col-span-2">
            <div
                class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-8 shadow-sm animate-fade-in-up">
                <form wire:submit="save" class="space-y-6">
                    <div class="form-control w-full">
                        <label class="label pb-0">
                            <span
                                class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                Nama Peran <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <input type="text" wire:model="roleName" placeholder="Contoh: Administrator"
                            class="input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold" />
                        @error('roleName')
                            <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label pb-0">
                            <span
                                class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1.5">
                                Deskripsi
                            </span>
                        </label>
                        <textarea wire:model="roleDescription" placeholder="Deskripsi singkat tentang peran ini..." rows="3"
                            class="textarea textarea-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-medium"></textarea>
                    </div>

                    <div
                        class="flex items-start sm:items-center gap-4 p-5 bg-zinc-50 dark:bg-zinc-950/50 border border-zinc-100 dark:border-zinc-800 rounded-2xl shadow-inner">
                        <input type="checkbox" wire:model="roleIsActive" class="checkbox checkbox-indigo mt-0.5 sm:mt-0"
                            id="edit-role-active" />
                        <label for="edit-role-active" class="cursor-pointer">
                            <p class="font-bold text-sm text-zinc-800 dark:text-zinc-200">Aktifkan Peran</p>
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Peran nonaktif tidak dapat
                                digunakan pengguna.</p>
                        </label>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4 pt-4">
                        <button type="button"
                            class="btn btn-ghost text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 btn-sm rounded-xl font-bold"
                            wire:click="$set('confirmingDeletion', true)">
                            <x-heroicon-o-trash class="size-4 mr-2" />
                            Hapus Peran
                        </button>
                        <div class="flex gap-3 w-full sm:w-auto">
                            <a href="{{ route('rbac.show') }}"
                                class="btn btn-sm bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl transition-all font-bold px-6">
                                Batal
                            </a>
                            <button type="submit"
                                class="btn btn-sm bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/20 rounded-xl px-8 font-bold transition-all hover:-translate-y-0.5">
                                <x-heroicon-o-check class="size-4 mr-2" />
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Info & Quick Actions --}}
        <div class="space-y-4">
            <div class="rounded-2xl border border-white/30 dark:border-white/10
                        bg-white/60 dark:bg-zinc-800/60 backdrop-blur-xl
                        <h4 class="text-[10px]
                font-black uppercase tracking-[0.2em] text-zinc-400 flex items-center gap-2 mb-4">
                <div class="p-1.5 rounded-lg bg-indigo-500">
                    <x-heroicon-o-shield-check class="size-3.5 text-white" />
                </div>
                Informasi Peran
                </h4>
                <div class="space-y-4">
                    @foreach ([['label' => 'Jumlah Anggota', 'value' => $this->roles->users()->count()], ['label' => 'Jumlah Izin', 'value' => $this->roles->permissions()->count()], ['label' => 'Dibuat', 'value' => $this->roles->created_at?->format('d M Y') ?? '-'], ['label' => 'Diperbarui', 'value' => $this->roles->updated_at?->format('d M Y') ?? '-']] as $info)
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-zinc-400 uppercase tracking-widest">{{ $info['label'] }}</span>
                            <span class="font-black text-zinc-800 dark:text-zinc-200">{{ $info['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div
                class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm space-y-4">
                <h4
                    class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 flex items-center gap-2 mb-3">
                    <div class="p-1.5 rounded-lg bg-indigo-500">
                        <x-heroicon-o-bolt class="size-3.5 text-white" />
                    </div>
                    Aksi Cepat
                </h4>
                <a href="{{ route('rbac.permission.edit', $this->roles) }}"
                    class="btn btn-sm w-full bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl transition-all font-bold">
                    <x-heroicon-o-shield-check class="size-4 mr-2" />
                    Kelola Izin
                </a>
                <a href="{{ route('rbac.add.teams', $this->roles) }}"
                    class="btn btn-sm w-full bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl transition-all font-bold">
                    <x-heroicon-o-user-group class="size-4 mr-2" />
                    Kelola Anggota
                </a>
            </div>
        </div>
    </div>

    @if ($confirmingDeletion)
        <x-rbac.confirm-modal title="Hapus Peran"
            message="Yakin ingin menghapus peran '{{ $this->roles->role }}'? Semua data terkait peran ini akan dihapus dan tindakan ini permanen."
            confirmAction="confirmDelete" cancelAction="$set('confirmingDeletion', false)" />
    @endif
</div>

<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.4s ease-out both;
    }
</style>
