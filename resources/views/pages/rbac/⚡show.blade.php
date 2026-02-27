<?php
use App\Models\Modules;
use App\Models\Permissions;
use App\Models\Roles;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    //
    public string $selectedTab = 'roles-tab';

    public string $group = '';

    public string $search = '';

    public string $name = '';

    public string $label = '';

    public string $icon = '';

    public string $sort = '';

    public bool $is_active = true;

    public function mount()
    {
        $this->selectedTab = 'roles-tab';
        $this->search = '';
    }

    #[Computed]
    public function roles()
    {
        return Roles::withCount('users')->when($this->search, fn($q) => $q->where('role', 'like', '%' . $this->search . '%')->orWhere('slug', 'like', '%' . $this->search . '%'))->get();
    }

    #[Computed]
    public function modules()
    {
        return Modules::with('permissions')->get();
    }

    public function create() {}

    public function createModule()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:modules,name'],
            'label' => ['required', 'string', 'max:255', 'unique:modules,label'],
            'icon' => ['required', 'string', 'max:255'],
            'sort' => ['required', 'integer'],
            'is_active' => ['required'],
        ]);
        Modules::create([
            'name' => $this->name,
            'label' => $this->label,
            'icon' => $this->icon,
            'order' => $this->sort,
            'is_active' => $this->is_active,
        ]);
        $this->name = '';
        $this->label = '';
        $this->icon = '';
        $this->sort = '';
        $this->is_active = true;
        Flux::modals()->close();
    }

    public function edit(Roles $role)
    {
        //
    }

    public function editRole(Roles $role)
    {
        return redirect()->route('rbac.permission.edit', $role);
    }

    public function addNewMember(Roles $role)
    {
        return redirect()->route('rbac.add.teams', $role);
    }
};
?>
<div class="container space-y-6 h-full">
    @include('partials.heading', [
        'title' => 'Roles & Permissions Manager',
        'description' => 'Kelola seluruh data akses pengguna dan izin akses fitur-fitur yang tersedia.',
    ])
    <flux:heading class="sr-only">Role Based Access Control Manager</flux:heading>
    <div class="tabs tabs-lift">
        <label class="tab gap-2 flex items-center" for="roles-tab">
            <input type="radio" name="my_tabs_2" class="tab" aria-label="Roles" checked />
            <x-heroicon-o-users class="size-5" />
            Pengaturan Peran
        </label>
        <div class="tab-content border-base-300 bg-base-100 p-4" id="roles-tab">
            <div class="flex items-center gap-2 justify-between mb-6">
                <label
                    class="input input-sm w-full border-zinc-200 bg-zinc-100 focus-within:border-zinc-400 dark:border-zinc-700 dark:bg-zinc-800 dark:focus-within:border-zinc-600 dark:text-base-100 rounded outline-0 focus:outline-0">
                    <x-heroicon-o-magnifying-glass class="size-4 text-zinc-500" />
                    <input type="search" required placeholder="Cari..." wire:model.live.debounce.300ms="search" />
                </label>

                <button class="btn btn-sm bg-[#1A77F2] text-white border-[#005fd8]" wire:click="create">
                    <x-heroicon-o-plus class="size-4" /> <span class="hidden sm:inline">Tambah Peran</span>
                </button>
            </div>
            @if ($this->roles->isEmpty())
                <x-empty-state message="No roles found.">
                    <x-slot:action>
                        <button class="btn btn-primary">Create Roles</button>
                    </x-slot:action>
                </x-empty-state>
            @else
                <x-rbac.card />
            @endif
        </div>
        <label class="tab gap-2 flex items-center" for="permission-tab">
            <input type="radio" name="my_tabs_2" class="tab" aria-label="Permission" />
            <x-heroicon-o-users class="size-5" />
            Pengaturan Izin
        </label>
        <div class="tab-content border-base-300 bg-base-100 p-4" id="permission-tab">
            <div class="flex items-center gap-2 justify-between mb-6">
                <label
                    class="input input-sm w-full border-zinc-200 bg-zinc-100 focus-within:border-zinc-400 dark:border-zinc-700 dark:bg-zinc-800 dark:focus-within:border-zinc-600 dark:text-base-100 rounded outline-0 focus:outline-0">
                    <x-heroicon-o-magnifying-glass class="size-4 text-zinc-500" />
                    <input type="search" required placeholder="Cari..."
                        wire:model.live.debounce.300ms="searchPermission" />
                </label>

                <button class="btn btn-sm bg-[#1A77F2] text-white border-[#005fd8]"
                    onclick="document.getElementById('create-modules').showModal()">
                    <x-heroicon-o-plus class="size-4" /> <span class="hidden sm:inline">Tambah Izin</span>
                </button>
            </div>
            @if ($this->modules->isNotEmpty())
                @foreach ($this->modules as $module)
                    <div class="collapse collapse-arrow bg-base-100 border border-base-300 mb-3">
                        <input type="radio" name="my-accordion-3" />
                        <div class="collapse-title font-semibold">
                            <div class="flex items-center justify-start w-full! gap-2">
                                <x-heroicon-o-shield-check class="size-12 text-gray-400" />
                                <div class="flex flex-col flex-1">
                                    <h5 class="font-bold">{{ $module->name }}</h5>
                                    @if ($module->description !== null)
                                        <p class="text-sm text-gray-700">{{ $module->description }}</p>
                                    @endif
                                    <div class="flex items-center gap-2 mt-2">
                                        <button class="btn btn-xs btn-square bg-transparent"
                                            wire:click="editPermission({{ $module->id }})">
                                            <x-heroicon-o-pencil class="size-3" />
                                        </button>
                                        <button class="btn btn-xs btn-square bg-transparent"
                                            wire:click="deletePermission({{ $module->id }})">
                                            <x-heroicon-o-trash class="size-3" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="collapse-content text-sm">
                            @if ($module->permissions->isEmpty())
                                <x-empty-state message="No permissions found.">
                                    <x-slot:action>
                                        <button class="btn btn-primary">Create Permissions</button>
                                    </x-slot:action>
                                </x-empty-state>
                            @else
                                <div class="grid gap-4 grid-cols-1 " wire:loading.lazy="true">
                                    @foreach ($module->permissions as $permission)
                                        <div key="{{ $permission->id }}"
                                            class="flex gap-2 items-center bg-white rounded-md p-3 shadow-md">
                                            {{-- <x-icon name="{{ $permission->icon }}" class="size-12 text-gray-400" /> --}}
                                            <div class="flex flex-col flex-1">
                                                <span class="font-bold">{{ $permission->label }}</span>
                                                <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <x-empty-state message="No modules found.">
                    <x-slot:action>
                        <button class="btn btn-primary" wire:click="create">Tambah Modul</button>
                    </x-slot:action>
                </x-empty-state>

            @endif
        </div>

        <input type="radio" name="my_tabs_2" class="tab" aria-label="Tab 3" />
        <div class="tab-content border-base-300 bg-base-100 p-4">Tab content 3</div>
    </div>

    <dialog id="create-modules" class="modal">
        <div class="modal-box p-8">
            <h3 class="text-lg font-bold">Tambah Modul Baru</h3>
            <p class="mb-8 text-sm text-zinc-600">Lengkapi data berikut untuk menambahkan modul baru.</p>
            <form id="create-module-form" wire:submit="createModule" class="space-y-6">
                <fieldset class="fieldset">
                    <legend class="legend text-sm text-zinc-900 font-semibold">Nama Modul</legend>
                    <div
                        class="input input-bordered w-full focus-within:border-zinc-400 dark:border-zinc-700 dark:bg-zinc-800 dark:focus-within:border-zinc-600 dark:text-base-100 rounded outline-0 focus:outline-0">
                        <input type="text" wire:model="name" placeholder="ex. Master Data" required autofocus />
                    </div>
                </fieldset>
                <fieldset class="fieldset">
                    <legend class="legend text-sm text-zinc-900 font-semibold">Slug</legend>
                    <div
                        class="input input-bordered w-full focus-within:border-zinc-400 dark:border-zinc-700 dark:bg-zinc-800 dark:focus-within:border-zinc-600 dark:text-base-100 rounded outline-0 focus:outline-0">
                        <input type="text" wire:model="label" placeholder="ex. master-data" required />
                    </div>
                </fieldset>
                <fieldset class="fieldset">
                    <legend class="legend text-sm text-zinc-900 font-semibold">Icon</legend>
                    <div
                        class="input input-bordered w-full focus-within:border-zinc-400 dark:border-zinc-700 dark:bg-zinc-800 dark:focus-within:border-zinc-600 dark:text-base-100 rounded outline-0 focus:outline-0">
                        <input type="text" wire:model="icon" placeholder="ex. heroicon-o-users" required />
                    </div>
                </fieldset>
                <fieldset class="fieldset">
                    <legend class="legend text-sm text-zinc-900 font-semibold">Nomor Urut</legend>
                    <div
                        class="input input-bordered w-full focus-within:border-zinc-400 dark:border-zinc-700 dark:bg-zinc-800 dark:focus-within:border-zinc-600 dark:text-base-100 rounded outline-0 focus:outline-0">
                        <input type="number" wire:model="sort" placeholder="1" required />
                    </div>
                </fieldset>
                <fieldset class="fieldset">
                    <legend class="legend text-sm text-zinc-900 font-semibold">Keterangan</legend>
                    <div
                        class="textarea input-bordered w-full focus-within:border-zinc-400 dark:border-zinc-700 dark:bg-zinc-800 dark:focus-within:border-zinc-600 dark:text-base-100 rounded outline-0 focus:outline-0">
                        <textarea wire:model="description" class="size-full" placeholder="ex. Pengelolaan Produk dan Kategori."></textarea>
                    </div>
                </fieldset>

                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="is_active" class="checkbox" />
                    <span>Is Active</span>
                </div>
            </form>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn">Batal</button>
                </form>
                <button type="submit" form="create-module-form" class="btn btn-primary">Tambah</button>

            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
    {{-- <flux:modal name="create-modules" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Modul Baru</flux:heading>
                <flux:text class="mt-2">Lengkapi data berikut untuk menambahkan modul baru.
                </flux:text>
            </div>
            <form wire:submit="createModule" class="space-y-6">
                <flux:field>
                    <flux:label badge="Required" class="mb-2!">Nama Module</flux:label>

                    <flux:input wire:model="name" type="text" required autofocus
                        :placeholder="__('Nama Module')" />
                    <flux:error name="name">
                        {{ $errors->first('name') }}
                    </flux:error>
                </flux:field>
                <flux:field>
                    <flux:label badge="Required" class="mb-2!">Label</flux:label>

                    <flux:input wire:model="label" type="text" required :placeholder="__('Label Module')" />
                    <flux:error name="label">
                        {{ $errors->first('label') }}
                    </flux:error>
                </flux:field>
                <flux:field>
                    <flux:label badge="Required" class="mb-2!">Icon</flux:label>

                    <flux:input wire:model="icon" type="text" required :placeholder="__('icon-example')" />
                    <flux:error name="icon">
                        {{ $errors->first('icon') }}
                    </flux:error>
                </flux:field>
                <flux:field>
                    <flux:label badge="Required" class="mb-2!">Sort Number </flux:label>

                    <flux:input wire:model="sort" type="number" required :placeholder="__('sort-example')" />
                    <flux:error name="sort">
                        {{ $errors->first('sort') }}
                    </flux:error>
                </flux:field>
                <flux:radio.group variant="buttons" class="w-full *:flex-1" label="Is Active"
                    wire:model="is_active">
                    <flux:radio icon="check-circle" checked>Ya</flux:radio>
                    <flux:radio icon="x-circle">Tidak</flux:radio>
                </flux:radio.group>
                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">Tambah Modul</flux:button>
                </div>
            </form>
        </div>
    </flux:modal> --}}

</div>
