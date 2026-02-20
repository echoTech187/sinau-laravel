<?php
use App\Models\Modules;
use App\Models\Permissions;
use App\Models\Roles;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

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

    public Collection $roles;

    public Collection $modules;

    public Collection $permissions;

    public function mount()
    {
        $this->selectedTab = 'roles-tab';
        $this->search = '';
        $this->roles = Roles::withCount('users')->get();
        $this->modules = Modules::all();
        $this->permissions = Permissions::all();
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
        $this->modules = Modules::all();
        $this->name = '';
        $this->label = '';
        $this->icon = '';
        $this->sort = '';
        $this->is_active = true;
        Flux::modals()->close();
    }

    public function edit(Roles $role)
    {
        return redirect()->route('rbac.edit', $role);
    }

    public function editRole(Roles $role)
    {
        return redirect()->route('rbac.permission.edit', $role);
    }

    public function addNewMember(Roles $role)
    {
        return redirect()->route('rbac.add.teams', $role);
    }

    public function search()
    {
        $this->roles = Roles::withCount('users')
            ->where('role', 'like', '%' . $this->search . '%')
            ->orWhere('slug', 'like', '%' . $this->search . '%')
            ->get();
    }
};
?>

<div class="container space-y-6 h-full">
    @include('partials.heading', [
        'title' => 'Roles & Permissions Manager',
        'description' => 'Kelola seluruh data akses pengguna dan izin akses fitur-fitur yang tersedia.',
    ])
    <flux:heading class="sr-only">{{ __('Role Based Access Control Manager') }}</flux:heading>
    <x-tabs wire:model="selectedTab" labelClass='flex! items-center! gap-4! px-2! py-1! border-0!'>
        <x-tab name="roles-tab" label="Roles" icon="carbon.group.security" :active="$selectedTab === 'roles-tab'" :loading="$selectedTab === 'roles-tab'"
            class="dark:text-white!">
            <div class="flex items-center gap-2 justify-between mb-6">
                <flux:input icon="magnifying-glass" size="sm" placeholder="Cari..." @style(['placeholder:text-zinc-400 placeholder:text-xs'])
                    wire:model.live.debounce.300ms="search" />
                <flux:button size="sm" class="max-md:gap-0!" variant="primary" icon="user-plus" wire:click="create"
                    title="Tambah Pengguna" data-test="create-user-button" wire:navigate>
                    <span class="hidden sm:inline">{{ __('Tambah Roles') }}</span>
                </flux:button>
            </div>
            <div class="grid gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 " wire:loading.lazy="true">
                @if ($roles->isEmpty())
                    <div class="w-full h-full flex flex-col gap-4 items-center justify-center my-24">
                        <x-icon name="fluentui-channel-dismiss-24" class="size-24 text-gray-400" />
                        <span class="ms-2 text-gray-400">No roles found.</span>
                        <button class="btn btn-primary" wire:click="create">Create Roles</button>
                    </div>
                @else
                    @foreach ($roles as $role)
                        <x-card key="{{ $role->id }}"
                            class="relative shadow-xl border border-gray-100 bg-white rounded-xl dark:border-black dark:bg-zinc-800! dark:text-white! p-4!">
                            <x-slot name="title">
                                {{ $role->role }}
                            </x-slot>
                            <x-slot name="subtitle" class="flex items-center  gap-2">
                                <span>{{ $role->slug }}</span>●<span
                                    class="text-sm flex items-center gap-2 w-fit font-bold {{ $role->is_active ? 'text-green-600' : 'text-gray-600' }}">
                                    {{ $role->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </x-slot>
                            <div
                                class="absolute top-4 right-4 text-sm flex items-center gap-2 w-fit px-3 py-1.5 bg-gray-100 rounded-full dark:bg-zinc-800">
                                <x-icon name="carbon.user.follow" class="size-4" />{{ $role->users_count }}
                                Users
                            </div>
                            <x-slot name="separator" color="blue"></x-slot>
                            <div class="pb-4">
                                Manage permission for {{ $role->role }}
                            </div>
                            <div class="flex justify-between items-center gap-4 ">
                                <x-button variant="outline" size="xs" icon="carbon.settings.check"
                                    class="w-fit text-xs! bg-transparent! text-zinc-900! hover:bg-zinc-100! hover:text-zinc-900! hover:border-zinc-900! dark:text-white! dark:hover:bg-zinc-800! dark:hover:text-white! dark:hover:border-zinc-800! border! border-zinc-200! dark:border-zinc-700!"
                                    wire:click="editRole({{ $role->id }})">
                                    {{ __('Kelola Akses') }}
                                </x-button>
                                <x-button variant="outline" size="xs" icon="carbon.user.follow"
                                    class="w-fit text-xs! border! border-cyan-500! bg-transparent! text-cyan-600! hover:bg-cyan-600! hover:text-white! hover:border-cyan-600!"
                                    wire:click="addNewMember({{ $role->id }})">
                                    <span class="hidden md:block">
                                        {{ __('Tambah Anggota') }}
                                    </span>
                                </x-button>
                            </div>
                        </x-card>
                    @endforeach
                @endif
            </div>
        </x-tab>
        <x-tab name="tricks-tab" label="Permissions" icon="carbon.ibm.cloud.app.id" :active="$selectedTab === 'tricks-tab'" :loading="$selectedTab === 'tricks-tab'"
            class="dark:text-white! h-full!">
            <div class="flex items-center gap-2 justify-between mb-6">
                <flux:input icon="magnifying-glass" size="sm" placeholder="Cari..." @style(['placeholder:text-zinc-400 placeholder:text-xs'])
                    wire:model.live.debounce.300ms="search" />
                <flux:modal.trigger name="create-modules">
                    <flux:button size="sm">Create Modules</flux:button>
                </flux:modal.trigger>
                <flux:modal name="create-modules" class="md:w-96">
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

                                <flux:input wire:model="label" type="text" required
                                    :placeholder="__('Label Module')" />
                                <flux:error name="label">
                                    {{ $errors->first('label') }}
                                </flux:error>
                            </flux:field>
                            <flux:field>
                                <flux:label badge="Required" class="mb-2!">Icon</flux:label>

                                <flux:input wire:model="icon" type="text" required
                                    :placeholder="__('icon-example')" />
                                <flux:error name="icon">
                                    {{ $errors->first('icon') }}
                                </flux:error>
                            </flux:field>
                            <flux:field>
                                <flux:label badge="Required" class="mb-2!">Sort Number </flux:label>

                                <flux:input wire:model="sort" type="number" required
                                    :placeholder="__('sort-example')" />
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
                </flux:modal>
            </div>
            @if ($modules->isEmpty())
                <div class="w-full h-full flex flex-col gap-4 items-center justify-center my-24">
                    <x-icon name="fluentui-channel-dismiss-24" class="size-24 text-gray-400" />
                    <span class="ms-2 text-gray-400">No modules found.</span>
                    <flux:modal.trigger name="create-modules">
                        <flux:button>Create Modules</flux:button>
                    </flux:modal.trigger>
                    <flux:modal name="create-modules" class="md:w-96">
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

                                    <flux:input wire:model="label" type="text" required
                                        :placeholder="__('Label Module')" />
                                    <flux:error name="label">
                                        {{ $errors->first('label') }}
                                    </flux:error>
                                </flux:field>
                                <flux:field>
                                    <flux:label badge="Required" class="mb-2!">Icon</flux:label>

                                    <flux:input wire:model="icon" type="text" required
                                        :placeholder="__('icon-example')" />
                                    <flux:error name="icon">
                                        {{ $errors->first('icon') }}
                                    </flux:error>
                                </flux:field>
                                <flux:field>
                                    <flux:label badge="Required" class="mb-2!">Sort Number </flux:label>

                                    <flux:input wire:model="sort" type="number" required
                                        :placeholder="__('sort-example')" />
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
                    </flux:modal>
                </div>
            @else
                <div class="grid gap-4 grid-cols-1 " wire:loading.lazy="true">

                    <x-accordion wire:model="group">
                        @foreach ($modules as $module)
                            <x-collapse name="group{{ $module->id }}" key="{{ $module->id }}">
                                <x-slot:heading>
                                    <div class="flex gap-2 items-center">
                                        <x-icon name="{{ $module->icon }}" class="size-12 text-gray-400" />
                                        <div class="flex flex-col flex-1">
                                            <span class="font-bold">{{ $module->label }}</span>
                                            <span class="text-sm text-gray-700">{{ $module->name }}</span>
                                        </div>
                                    </div>
                                </x-slot:heading>
                                <x-slot:content>Hello 1</x-slot:content>
                            </x-collapse>
                        @endforeach
                    </x-accordion>
                </div>
            @endif

</div>
</x-tab>
<x-tab name="musics-tab" label="Navigation Builder" icon="carbon.ibm.cloud.security.groups" :active="$selectedTab === 'musics-tab'"
    :loading="$selectedTab === 'musics-tab'" class="dark:text-white!">
    <div>Musics</div>
</x-tab>
</x-tabs>


</div>
