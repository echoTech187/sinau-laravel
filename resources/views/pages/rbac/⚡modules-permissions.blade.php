<?php
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use App\Models\Modules;
use App\Models\Permissions;
use App\Helpers\AuditLogger;
use Illuminate\Database\Eloquent\Collection;

new class extends Component {
    public Modules $modules;
    public bool $showCreatePermissionModal = false;

    #[Validate('required|string|max:255')]
    public string $permName = '';
    public string $permSlug = '';
    public string $permGroupName = '';

    public function mount(Modules $modules)
    {
        $this->modules = $modules;
    }

    #[Computed]
    public function permissions(): Collection
    {
        return Permissions::where('module_id', $this->modules->id)->orderBy('name')->get();
    }

    public function updatedPermName(): void
    {
        $this->permSlug = \Illuminate\Support\Str::slug(str_replace('.', '-', $this->permName));
    }

    public function createPermission(): void
    {
        $this->validateOnly('permName');
        $perm = Permissions::create([
            'module_id' => $this->modules->id,
            'name' => $this->permName,
            'slug' => $this->permSlug ?: \Illuminate\Support\Str::slug($this->permName),
            'group_name' => $this->permGroupName ?: null,
        ]);
        AuditLogger::record('permission_created', null, [
            'module' => $this->modules->name,
            'permission' => $perm->name,
            'slug' => $perm->slug,
        ]);
        $this->reset(['permName', 'permSlug', 'permGroupName', 'showCreatePermissionModal']);
        unset($this->permissions);
        $this->dispatch('notify', type: 'success', message: 'Izin berhasil ditambahkan!');
    }

    public function deletePermission(int $id): void
    {
        $perm = Permissions::findOrFail($id);
        AuditLogger::record('permission_deleted', null, [
            'module' => $this->modules->name,
            'permission' => $perm->name,
            'slug' => $perm->slug,
        ]);
        $perm->delete();
        unset($this->permissions);
        $this->dispatch('notify', type: 'success', message: 'Izin berhasil dihapus!');
    }
};
?>

<div class="container space-y-6 h-full pb-10">
    <x-slot:title>Izin Modul</x-slot:title>
    @include('partials.heading', [
        'title' => 'Izin Modul: ' . $this->modules->name,
        'description' => $this->modules->description ?? 'Kelola daftar izin yang tersedia di modul ini.',
    ])

    {{-- Breadcrumb --}}
    <nav class="flex items-center flex-wrap gap-2 text-sm text-zinc-500 dark:text-zinc-400 -mt-4">
        <a href="{{ route('rbac.show') }}" class="hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-1">
            <x-heroicon-o-shield-check class="size-4" />RBAC Manager
        </a>
        <x-heroicon-o-chevron-right class="size-4 text-zinc-300 dark:text-zinc-600" />
        <span class="text-zinc-800 dark:text-zinc-200 font-medium">{{ $this->modules->name }}</span>
    </nav>

    {{-- Module Info --}}
    <div
        class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 rounded-xl">
        <div class="p-3 bg-blue-100 dark:bg-blue-900/50 rounded-lg w-fit">
            <x-heroicon-o-shield-check class="size-5 text-blue-600 dark:text-blue-400" />
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="font-bold text-zinc-900 dark:text-zinc-100">{{ $this->modules->name }}</h2>
            <p class="text-xs text-zinc-400 dark:text-zinc-500 font-mono">{{ $this->modules->slug }}</p>
        </div>
        <span
            class="badge {{ $this->modules->is_active ? 'badge-success' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400 border-0' }} w-fit">
            {{ $this->modules->is_active ? 'Aktif' : 'Nonaktif' }}
        </span>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">
            {{ $this->permissions->count() }} Izin Terdaftar
        </p>
        <button class="btn btn-sm bg-blue-600 hover:bg-blue-700 text-white border-blue-700 w-full sm:w-auto"
            wire:click="$set('showCreatePermissionModal', true)">
            <x-heroicon-o-plus class="size-4" />Tambah Izin
        </button>
    </div>

    {{-- Permission List --}}
    @if ($this->permissions->isEmpty())
        <x-empty-state message="Belum ada izin di modul ini." />
    @else
        <div class="grid gap-3 grid-cols-1 sm:grid-cols-2">
            @foreach ($this->permissions as $permission)
                <div
                    class="flex items-center gap-3 bg-white dark:bg-zinc-900 rounded-xl p-4 border border-zinc-100 dark:border-zinc-700 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="w-2.5 h-2.5 rounded-full bg-blue-400 dark:bg-blue-500 shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">{{ $permission->name }}</p>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 font-mono truncate">{{ $permission->slug }}
                        </p>
                        @if ($permission->group_name)
                            <span
                                class="badge badge-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 border-0 mt-1">{{ $permission->group_name }}</span>
                        @endif
                    </div>
                    <button
                        class="btn btn-xs btn-ghost text-red-400 dark:text-red-400/70 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity"
                        wire:click="deletePermission({{ $permission->id }})"
                        wire:confirm="Yakin ingin menghapus izin '{{ $permission->name }}'?">
                        <x-heroicon-o-trash class="size-3.5" />
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    <div class="flex pt-4 border-t border-zinc-200 dark:border-zinc-700">
        <a href="{{ route('rbac.show') }}"
            class="btn btn-ghost btn-sm text-zinc-600 dark:text-zinc-400 dark:hover:bg-zinc-800">
            <x-heroicon-o-arrow-left class="size-4" />Kembali ke RBAC
        </a>
    </div>

    {{-- Modal Tambah Izin --}}
    @if ($showCreatePermissionModal)
        <dialog open class="modal modal-open z-50">
            <div
                class="modal-box bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 max-w-md w-full">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 mb-1">Tambah Izin Baru</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-5">Tambahkan izin baru ke modul <strong
                        class="text-zinc-800 dark:text-zinc-200">{{ $this->modules->name }}</strong>.</p>
                <form wire:submit="createPermission" class="space-y-4">
                    <fieldset class="fieldset">
                        <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Nama Izin <span
                                class="text-red-500">*</span></legend>
                        <div
                            class="input input-bordered w-full dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100">
                            <input type="text" wire:model.live="permName" placeholder="Contoh: Lihat Data Produk"
                                autofocus class="dark:placeholder-zinc-500" />
                        </div>
                        @error('permName')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Slug</legend>
                        <div
                            class="input input-bordered w-full bg-zinc-50 dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100">
                            <input type="text" wire:model="permSlug" placeholder="Contoh: product.view"
                                class="dark:placeholder-zinc-500" />
                        </div>
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Grup</legend>
                        <div
                            class="input input-bordered w-full dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100">
                            <input type="text" wire:model="permGroupName" placeholder="Contoh: Produk"
                                class="dark:placeholder-zinc-500" />
                        </div>
                    </fieldset>
                    <div class="modal-action gap-2">
                        <button type="button" class="btn btn-ghost dark:text-zinc-400 dark:hover:text-zinc-200"
                            wire:click="$set('showCreatePermissionModal', false)">Batal</button>
                        <button type="submit" class="btn bg-blue-600 hover:bg-blue-700 text-white border-blue-700">
                            <x-heroicon-o-plus class="size-4" />Tambah Izin
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-backdrop bg-black/40 dark:bg-black/60"
                wire:click="$set('showCreatePermissionModal', false)"></div>
        </dialog>
    @endif
</div>
