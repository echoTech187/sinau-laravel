<?php
use App\Models\Menus;
use App\Models\Modules;
use App\Models\Permissions;
use App\Models\Roles;
use App\Helpers\AuditLogger;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public string $activeTab = 'roles';

    // Search states
    public string $search = '';

    public string $searchPermission = '';

    public string $searchMenu = '';

    public string $searchLog = '';

    // Create Role Modal
    public bool $showCreateRoleModal = false;

    #[Validate('required|string|max:255')]
    public string $roleName = '';

    #[Validate('nullable|string|max:500')]
    public string $roleDescription = '';

    public bool $roleIsActive = true;

    // Create Module Modal
    public bool $showCreateModuleModal = false;

    #[Validate('required|string|max:255')]
    public string $moduleName = '';

    public string $moduleSlug = '';

    public string $moduleIcon = '';

    public int $moduleOrder = 0;

    public bool $moduleIsActive = true;

    public string $moduleDescription = '';

    // Create Menu Modal
    public bool $showCreateMenuModal = false;

    #[Validate('required|string|max:255')]
    public string $menuName = '';

    public string $menuIcon = '';

    public string $menuRoute = '';

    public int $menuModuleId = 0;

    public int $menuParentId = 0;

    public int $menuPermissionId = 0;

    public int $menuOrder = 0;

    public bool $menuIsActive = true;

    public function mount()
    {
        $this->activeTab = 'roles';
    }

    #[Computed]
    public function roles()
    {
        return Roles::withCount('users')->when($this->search, fn($q) => $q->where('role', 'like', '%' . $this->search . '%')->orWhere('slug', 'like', '%' . $this->search . '%'))->get();
    }

    #[Computed]
    public function modules()
    {
        return Modules::with('permissions')->when($this->searchPermission, fn($q) => $q->where('name', 'like', '%' . $this->searchPermission . '%'))->withCount('permissions')->orderBy('order')->get();
    }

    #[Computed]
    public function menus()
    {
        return Menus::with(['module', 'parent', 'permission'])
            ->when($this->searchMenu, fn($q) => $q->where('name', 'like', '%' . $this->searchMenu . '%'))
            ->whereNull('parent_id')
            ->with('children.permission')
            ->orderBy('order')
            ->get();
    }

    #[Computed]
    public function allModules()
    {
        return Modules::orderBy('name')->get();
    }

    #[Computed]
    public function allPermissions()
    {
        return Permissions::with('module')->orderBy('name')->get();
    }

    #[Computed]
    public function allMenus()
    {
        return Menus::orderBy('name')->get();
    }

    #[Computed]
    public function rbacLogs()
    {
        return \App\Models\RbacLog::with(['actor', 'targetUser'])
            ->when($this->searchLog, fn($q) => $q->where('event', 'like', '%' . $this->searchLog . '%'))
            ->latest()
            ->paginate(20);
    }

    public function editRole(Roles $role)
    {
        return redirect()->route('rbac.permission.edit', $role);
    }

    public function manageTeam(Roles $role)
    {
        return redirect()->route('rbac.add.teams', $role);
    }

    public function createRole()
    {
        $this->validateOnly('roleName');
        $role = Roles::create([
            'role' => $this->roleName,
            'slug' => \Illuminate\Support\Str::slug($this->roleName),
            'description' => $this->roleDescription,
            'is_active' => $this->roleIsActive,
        ]);
        AuditLogger::record('role_created', null, [
            'role' => $role->role,
            'role_id' => $role->id,
            'description' => $role->description,
        ]);
        $this->reset(['roleName', 'roleDescription', 'roleIsActive', 'showCreateRoleModal']);
        $this->dispatch('notify', type: 'success', message: 'Peran berhasil ditambahkan!');
    }

    public function createModule()
    {
        $this->validateOnly('moduleName');
        Modules::create([
            'name' => $this->moduleName,
            'slug' => $this->moduleSlug ?: \Illuminate\Support\Str::slug($this->moduleName),
            'icon' => $this->moduleIcon,
            'order' => $this->moduleOrder,
            'is_active' => $this->moduleIsActive,
            'description' => $this->moduleDescription,
        ]);
        $this->reset(['moduleName', 'moduleSlug', 'moduleIcon', 'moduleOrder', 'moduleIsActive', 'moduleDescription', 'showCreateModuleModal']);
        $this->dispatch('notify', type: 'success', message: 'Modul berhasil ditambahkan!');
    }

    public function createMenu()
    {
        $this->validateOnly('menuName');
        Menus::create([
            'name' => $this->menuName,
            'icon' => $this->menuIcon,
            'route' => $this->menuRoute,
            'module_id' => $this->menuModuleId ?: null,
            'parent_id' => $this->menuParentId ?: null,
            'permission_id' => $this->menuPermissionId ?: null,
            'order' => $this->menuOrder,
            'is_active' => $this->menuIsActive,
        ]);
        $this->reset(['menuName', 'menuIcon', 'menuRoute', 'menuModuleId', 'menuParentId', 'menuPermissionId', 'menuOrder', 'menuIsActive', 'showCreateMenuModal']);
        $this->dispatch('notify', type: 'success', message: 'Menu berhasil ditambahkan!');
    }

    public function deleteRole(Roles $role)
    {
        AuditLogger::record('role_deleted', null, [
            'role' => $role->role,
            'role_id' => $role->id,
        ]);
        $role->delete();
        $this->dispatch('notify', type: 'success', message: 'Peran berhasil dihapus!');
    }

    public function deleteModule(Modules $module)
    {
        AuditLogger::record('module_deleted', null, [
            'module' => $module->name,
            'module_id' => $module->id,
        ]);
        $module->delete();
        $this->dispatch('notify', type: 'success', message: 'Modul berhasil dihapus!');
    }

    public function deleteMenu(Menus $menu)
    {
        AuditLogger::record('menu_deleted', null, [
            'menu' => $menu->name,
            'menu_id' => $menu->id,
        ]);
        $menu->delete();
        $this->dispatch('notify', type: 'success', message: 'Menu berhasil dihapus!');
    }

    public function updatedModuleName()
    {
        $this->moduleSlug = \Illuminate\Support\Str::slug($this->moduleName);
    }
};
?>

<div class="container space-y-6 h-full pb-10">
    <style>
        /* Folder Tab Styles (Reused) */
        .folder-tab {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            gap: 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #71717a;
            /* zinc-500 */
            transition: all 0.3s ease;
            z-index: 1;
            margin-right: 0.5rem;
            border-radius: 12px 12px 0 0;
            background-color: rgba(244, 244, 245, 0.5);
            /* zinc-100/50 */
        }

        :is(.dark .folder-tab) {
            color: #a1a1aa;
            /* zinc-400 */
            background-color: rgba(39, 39, 42, 0.5);
            /* zinc-800/50 */
        }

        .folder-tab:hover {
            color: #3f3f46;
            /* zinc-700 */
            background-color: rgba(255, 255, 255, 0.8);
        }

        :is(.dark .folder-tab:hover) {
            color: #d4d4d8;
            /* zinc-300 */
            background-color: rgba(63, 63, 70, 0.8);
        }

        .folder-tab::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: -12px;
            width: 24px;
            height: 100%;
            background-color: inherit;
            clip-path: polygon(0 0, 0% 100%, 100% 100%);
            border-bottom-right-radius: 6px;
            z-index: -1;
            transition: all 0.3s ease;
        }

        .folder-tab.active {
            color: #4f46e5;
            /* indigo-600 */
            background-color: #ffffff;
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05);
            z-index: 2;
        }

        :is(.dark .folder-tab.active) {
            color: #818cf8;
            /* indigo-400 */
            background-color: #27272a;
            /* zinc-800 */
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.2);
        }

        .folder-tab-container {
            border-bottom: 2px solid #ffffff;
        }

        :is(.dark .folder-tab-container) {
            border-bottom: 2px solid #27272a;
        }
    </style>
    <x-slot:title>Kelola Peran & Modul</x-slot:title>
    @include('partials.heading', [
        'title' => 'Roles & Permissions Manager',
        'description' => 'Kelola seluruh data peran, izin, menu, dan log akses pengguna sistem.',
    ])

    {{-- Folder Tab Navigation --}}
    <div class="folder-tab-container flex overflow-x-auto pl-4 mb-6">
        @foreach ([['key' => 'roles', 'label' => 'Peran', 'icon' => 'o-users'], ['key' => 'permissions', 'label' => 'Izin & Modul', 'icon' => 'o-shield-check'], ['key' => 'menus', 'label' => 'Menu', 'icon' => 'o-bars-3'], ['key' => 'logs', 'label' => 'Log', 'icon' => 'o-clock']] as $tab)
            <button wire:click="$set('activeTab', '{{ $tab['key'] }}')"
                class="folder-tab shrink-0 {{ $activeTab === $tab['key'] ? 'active' : '' }}">
                <x-heroicon-o-users class="size-4 {{ $tab['key'] === 'roles' ? '' : 'hidden' }}" />
                <x-heroicon-o-shield-check class="size-4 {{ $tab['key'] === 'permissions' ? '' : 'hidden' }}" />
                <x-heroicon-o-bars-3 class="size-4 {{ $tab['key'] === 'menus' ? '' : 'hidden' }}" />
                <x-heroicon-o-clock class="size-4 {{ $tab['key'] === 'logs' ? '' : 'hidden' }}" />
                {{ $tab['label'] }}
                @if ($tab['key'] === 'roles')
                    <span
                        class="hidden sm:inline badge badge-xs {{ $activeTab === 'roles' ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300' }}">
                        {{ $this->roles->count() }}
                    </span>
                @elseif($tab['key'] === 'permissions')
                    <span
                        class="hidden sm:inline badge badge-xs {{ $activeTab === 'permissions' ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300' }}">
                        {{ $this->modules->count() }}
                    </span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- ======================== TAB: ROLES ======================== --}}
    @if ($activeTab === 'roles')
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <label
                    class="input input-sm flex-1 border-zinc-200 dark:border-zinc-600 bg-zinc-100 dark:bg-zinc-800 focus-within:border-blue-400 dark:focus-within:border-blue-500 dark:text-zinc-100 rounded outline-0">
                    <x-heroicon-o-magnifying-glass class="size-4 text-zinc-400 dark:text-zinc-500" />
                    <input type="search" placeholder="Cari peran..." wire:model.live.debounce.300ms="search"
                        class="dark:placeholder-zinc-500" />
                </label>
                <button
                    class="btn btn-sm bg-blue-600 hover:bg-blue-700 text-white border-blue-700 shrink-0 w-full sm:w-auto"
                    wire:click="$set('showCreateRoleModal', true)">
                    <x-heroicon-o-plus class="size-4" />
                    Tambah Peran
                </button>
            </div>

            @if ($this->roles->isEmpty())
                <x-empty-state message="Belum ada peran yang dibuat." />
            @else
                <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($this->roles as $role)
                        <x-rbac.card-item :role="$role" />
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- ======================== TAB: PERMISSIONS & MODULES ======================== --}}
    @if ($activeTab === 'permissions')
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <label
                    class="input input-sm flex-1 border-zinc-200 dark:border-zinc-600 bg-zinc-100 dark:bg-zinc-800 focus-within:border-blue-400 dark:text-zinc-100 rounded outline-0">
                    <x-heroicon-o-magnifying-glass class="size-4 text-zinc-400 dark:text-zinc-500" />
                    <input type="search" placeholder="Cari modul..." wire:model.live.debounce.300ms="searchPermission"
                        class="dark:placeholder-zinc-500" />
                </label>
                <button
                    class="btn btn-sm bg-blue-600 hover:bg-blue-700 text-white border-blue-700 shrink-0 w-full sm:w-auto"
                    wire:click="$set('showCreateModuleModal', true)">
                    <x-heroicon-o-plus class="size-4" />
                    Tambah Modul
                </button>
            </div>

            @if ($this->modules->isEmpty())
                <x-empty-state message="Belum ada modul yang dibuat." />
            @else
                <div class="space-y-3">
                    @foreach ($this->modules as $loop_module)
                        <div class="collapse collapse-arrow group/mod
                                    border border-white/30 dark:border-white/10
                                    bg-white/60 dark:bg-zinc-800/50
                                    backdrop-blur-xl rounded-2xl
                                    shadow-sm hover:shadow-lg dark:hover:shadow-zinc-900/50
                                    transition-all duration-300
                                    hover:-translate-y-0.5"
                            style="animation: fadeInUp 0.35s ease-out both; animation-delay: {{ $loop->index * 0.06 }}s">
                            <input type="checkbox" name="accordion-modules-{{ $loop_module->id }}" />
                            <div class="collapse-title min-h-0 py-3.5 px-4">
                                <div class="flex items-center gap-3 pr-6">
                                    <div
                                        class="p-2 rounded-xl shrink-0
                                                bg-linear-to-br from-blue-500 to-indigo-600
                                                shadow-[0_4px_12px_rgba(59,130,246,0.35)]
                                                group-hover/mod:scale-105 transition-transform duration-300">
                                        <x-heroicon-o-shield-check class="size-4 text-white" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h5 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">
                                                {{ $loop_module->name }}</h5>
                                            <span
                                                class="badge badge-xs font-semibold
                                                {{ $loop_module->is_active
                                                    ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-700/50'
                                                    : 'bg-zinc-100 dark:bg-zinc-700/50 text-zinc-500 dark:text-zinc-400 border-zinc-200 dark:border-zinc-600/50' }}">
                                                {{ $loop_module->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                            <span
                                                class="badge badge-xs bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-0">{{ $loop_module->permissions_count }}
                                                izin</span>
                                        </div>
                                        @if ($loop_module->description)
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate mt-0.5">
                                                {{ $loop_module->description }}</p>
                                        @endif
                                        <span
                                            class="text-xs text-zinc-400 dark:text-zinc-500 font-mono">{{ $loop_module->slug }}</span>
                                    </div>
                                    <button
                                        class="btn btn-xs btn-ghost text-red-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 shrink-0"
                                        wire:click.stop="deleteModule({{ $loop_module->id }})"
                                        wire:confirm="Yakin ingin menghapus modul '{{ $loop_module->name }}'?">
                                        <x-heroicon-o-trash class="size-3.5" />
                                    </button>
                                </div>
                            </div>
                            <div
                                class="collapse-content bg-white/30 dark:bg-zinc-900/30 backdrop-blur-sm rounded-b-2xl">
                                @if ($loop_module->permissions->isEmpty())
                                    <p class="text-center py-5 text-sm text-zinc-400 dark:text-zinc-500">Belum ada izin
                                        di modul ini.</p>
                                @else
                                    <div class="grid gap-2 grid-cols-1 sm:grid-cols-2 pt-3">
                                        @foreach ($loop_module->permissions as $permission)
                                            <div
                                                class="flex items-center gap-3
                                                        bg-white/60 dark:bg-zinc-800/60
                                                        backdrop-blur-sm
                                                        border border-white/40 dark:border-zinc-700/50
                                                        rounded-xl px-3 py-2
                                                        hover:bg-white/80 dark:hover:bg-zinc-800/80
                                                        transition-colors duration-150">
                                                <div class="w-2 h-2 rounded-full bg-blue-400 dark:bg-blue-500 shrink-0">
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p
                                                        class="text-xs font-medium text-zinc-800 dark:text-zinc-200 truncate">
                                                        {{ $permission->name }}</p>
                                                    <p
                                                        class="text-xs text-zinc-400 dark:text-zinc-500 font-mono truncate">
                                                        {{ $permission->slug }}</p>
                                                </div>
                                                @if ($permission->group_name)
                                                    <span
                                                        class="badge badge-xs bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-0 shrink-0">{{ $permission->group_name }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- ======================== TAB: MENUS ======================== --}}
    @if ($activeTab === 'menus')
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <label
                    class="input input-sm flex-1 border-zinc-200 dark:border-zinc-600 bg-zinc-100 dark:bg-zinc-800 focus-within:border-blue-400 dark:text-zinc-100 rounded outline-0">
                    <x-heroicon-o-magnifying-glass class="size-4 text-zinc-400 dark:text-zinc-500" />
                    <input type="search" placeholder="Cari menu..." wire:model.live.debounce.300ms="searchMenu"
                        class="dark:placeholder-zinc-500" />
                </label>
                <button
                    class="btn btn-sm bg-blue-600 hover:bg-blue-700 text-white border-blue-700 shrink-0 w-full sm:w-auto"
                    wire:click="$set('showCreateMenuModal', true)">
                    <x-heroicon-o-plus class="size-4" />
                    Tambah Menu
                </button>
            </div>

            @if ($this->menus->isEmpty())
                <x-empty-state message="Belum ada menu yang dibuat." />
            @else
                <div class="space-y-3">
                    @foreach ($this->menus as $loop_menu)
                        <div class="collapse collapse-arrow group/menu
                                    border border-white/30 dark:border-white/10
                                    bg-white/60 dark:bg-zinc-800/50
                                    backdrop-blur-xl rounded-2xl
                                    shadow-sm hover:shadow-lg dark:hover:shadow-zinc-900/50
                                    transition-all duration-300
                                    hover:-translate-y-0.5"
                            style="animation: fadeInUp 0.35s ease-out both; animation-delay: {{ $loop->index * 0.06 }}s">
                            <input type="checkbox" name="accordion-menu-{{ $loop_menu->id }}" />
                            <div class="collapse-title min-h-0 py-3.5 px-4">
                                <div class="flex items-center gap-3 pr-6">
                                    <div
                                        class="p-2 rounded-xl shrink-0
                                                bg-linear-to-br from-purple-500 to-fuchsia-600
                                                shadow-[0_4px_12px_rgba(168,85,247,0.35)]
                                                group-hover/menu:scale-105 transition-transform duration-300">
                                        <x-heroicon-o-bars-3 class="size-4 text-white" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h5 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">
                                                {{ $loop_menu->name }}</h5>
                                            <span
                                                class="badge badge-xs font-semibold
                                                {{ $loop_menu->is_active
                                                    ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-700/50'
                                                    : 'bg-zinc-100 dark:bg-zinc-700/50 text-zinc-500 dark:text-zinc-400 border-0' }}">
                                                {{ $loop_menu->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                            @if ($loop_menu->children && $loop_menu->children->count() > 0)
                                                <span
                                                    class="badge badge-xs bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 border-0">{{ $loop_menu->children->count() }}
                                                    sub-menu</span>
                                            @endif
                                        </div>
                                        <div
                                            class="flex flex-wrap items-center gap-2 text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">
                                            @if ($loop_menu->route)
                                                <span class="font-mono">{{ $loop_menu->route }}</span>
                                            @endif
                                            @if ($loop_menu->module)
                                                <span
                                                    class="badge badge-xs bg-zinc-100 dark:bg-zinc-700/50 text-zinc-500 dark:text-zinc-300 border-0">{{ $loop_menu->module->name }}</span>
                                            @endif
                                            @if ($loop_menu->permission)
                                                <span class="text-orange-500 dark:text-orange-400">🔒
                                                    {{ $loop_menu->permission->slug }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <button
                                        class="btn btn-xs btn-ghost text-red-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 shrink-0"
                                        wire:click.stop="deleteMenu({{ $loop_menu->id }})"
                                        wire:confirm="Yakin ingin menghapus menu '{{ $loop_menu->name }}'?">
                                        <x-heroicon-o-trash class="size-3.5" />
                                    </button>
                                </div>
                            </div>
                            <div
                                class="collapse-content bg-white/30 dark:bg-zinc-900/30 backdrop-blur-sm rounded-b-2xl">
                                @if ($loop_menu->children && $loop_menu->children->isNotEmpty())
                                    <div class="space-y-2 pt-3">
                                        <p
                                            class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2">
                                            Sub-menu</p>
                                        @foreach ($loop_menu->children as $child)
                                            <div
                                                class="flex items-center gap-3
                                                        bg-white/60 dark:bg-zinc-800/60 backdrop-blur-sm
                                                        border border-white/40 dark:border-zinc-700/50
                                                        rounded-xl px-3 py-2 ml-4
                                                        hover:bg-white/80 dark:hover:bg-zinc-800/80 transition-colors">
                                                <x-heroicon-o-arrow-right
                                                    class="size-4 text-zinc-300 dark:text-zinc-600 shrink-0" />
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                                        {{ $child->name }}</p>
                                                    <div
                                                        class="flex flex-wrap items-center gap-2 text-xs text-zinc-400 dark:text-zinc-500">
                                                        @if ($child->route)
                                                            <span class="font-mono">{{ $child->route }}</span>
                                                        @endif
                                                        @if ($child->permission)
                                                            <span class="text-orange-500 dark:text-orange-400">🔒
                                                                {{ $child->permission->slug }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span
                                                    class="badge badge-xs {{ $child->is_active ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 border-0' : 'bg-zinc-100 dark:bg-zinc-700/50 text-zinc-500 dark:text-zinc-400 border-0' }}">
                                                    {{ $child->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-center text-sm text-zinc-400 dark:text-zinc-500 py-4">Tidak ada
                                        sub-menu.</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- ======================== TAB: LOGS ======================== --}}
    @if ($activeTab === 'logs')
        <div class="space-y-4">
            <label
                class="input input-sm w-full sm:max-w-xs border-zinc-200 dark:border-zinc-600 bg-zinc-100 dark:bg-zinc-800 focus-within:border-blue-400 dark:text-zinc-100 rounded outline-0">
                <x-heroicon-o-magnifying-glass class="size-4 text-zinc-400 dark:text-zinc-500" />
                <input type="search" placeholder="Cari log..." wire:model.live.debounce.300ms="searchLog"
                    class="dark:placeholder-zinc-500" />
            </label>

            @php $logs = $this->rbacLogs; @endphp

            @if ($logs->isEmpty())
                <x-empty-state message="Belum ada log aktivitas RBAC." />
            @else
                <div
                    class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                    <table class="table table-sm w-full min-w-140">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <th class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">#</th>
                                <th class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">Admin</th>
                                <th class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">Target
                                </th>
                                <th class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">Event</th>
                                <th
                                    class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase hidden md:table-cell">
                                    Detail</th>
                                <th class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                            @foreach ($logs as $log)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors">
                                    <td class="text-zinc-400 dark:text-zinc-500 text-xs">{{ $log->id }}</td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 text-xs font-bold flex items-center justify-center shrink-0">
                                                {{ substr($log->actor?->name ?? 'A', 0, 1) }}
                                            </div>
                                            <span
                                                class="text-sm text-zinc-700 dark:text-zinc-300">{{ $log->actor?->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $log->targetUser?->name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $eventColors = [
                                                'assigned_role' => 'badge-success',
                                                'revoked_role' => 'badge-error',
                                                'assigned_permission' => 'badge-info',
                                                'revoked_permission' => 'badge-warning',
                                            ];
                                            $color =
                                                $eventColors[$log->event] ??
                                                'bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 border-0';
                                        @endphp
                                        <span class="badge badge-sm {{ $color }}">{{ $log->event }}</span>
                                    </td>
                                    <td class="max-w-45 hidden md:table-cell">
                                        @if (is_array($log->properties))
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400 space-y-0.5">
                                                @foreach (array_slice($log->properties, 0, 2) as $key => $val)
                                                    <div><span class="font-medium">{{ $key }}:</span>
                                                        {{ is_array($val) ? json_encode($val) : $val }}</div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs text-zinc-400 dark:text-zinc-500">-</span>
                                        @endif
                                    </td>
                                    <td class="text-xs text-zinc-400 dark:text-zinc-500 whitespace-nowrap">
                                        {{ $log->created_at?->format('d M Y, H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $logs->links() }}</div>
            @endif
        </div>
    @endif

    {{-- =========== MODAL: TAMBAH PERAN =========== --}}
    @if ($showCreateRoleModal)
        <dialog open class="modal modal-open z-50">
            <div
                class="modal-box bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 max-w-md w-full">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 mb-1">Tambah Peran Baru</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6">Lengkapi data di bawah untuk membuat peran
                    baru.</p>
                <form wire:submit="createRole" class="space-y-4">
                    <fieldset class="fieldset">
                        <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Nama Peran <span
                                class="text-red-500">*</span></legend>
                        <div
                            class="input input-bordered w-full dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100">
                            <input type="text" wire:model="roleName" placeholder="Contoh: Administrator" autofocus
                                class="dark:placeholder-zinc-500" />
                        </div>
                        @error('roleName')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Deskripsi
                        </legend>
                        <div
                            class="textarea input-bordered w-full dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100">
                            <textarea wire:model="roleDescription" placeholder="Deskripsi singkat..." rows="3"
                                class="dark:placeholder-zinc-500"></textarea>
                        </div>
                    </fieldset>
                    <div class="flex items-center gap-2 p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                        <input type="checkbox" wire:model="roleIsActive"
                            class="checkbox checkbox-primary checkbox-sm" id="role-active" />
                        <label for="role-active"
                            class="text-sm cursor-pointer text-zinc-700 dark:text-zinc-300">Aktifkan peran ini</label>
                    </div>
                    <div class="modal-action mt-6 gap-2">
                        <button type="button" class="btn btn-ghost dark:text-zinc-400 dark:hover:text-zinc-200"
                            wire:click="$set('showCreateRoleModal', false)">Batal</button>
                        <button type="submit" class="btn bg-blue-600 hover:bg-blue-700 text-white border-blue-700">
                            <x-heroicon-o-plus class="size-4" />Tambah Peran
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-backdrop bg-black/40 dark:bg-black/60" wire:click="$set('showCreateRoleModal', false)">
            </div>
        </dialog>
    @endif

    {{-- =========== MODAL: TAMBAH MODUL =========== --}}
    @if ($showCreateModuleModal)
        <dialog open class="modal modal-open z-50">
            <div
                class="modal-box bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 max-w-lg w-full">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 mb-1">Tambah Modul Baru</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6">Modul mengelompokkan izin-izin yang saling
                    berhubungan.</p>
                <form wire:submit="createModule" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <fieldset class="fieldset col-span-full">
                            <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Nama Modul
                                <span class="text-red-500">*</span>
                            </legend>
                            <div
                                class="input input-bordered w-full dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100">
                                <input type="text" wire:model.live="moduleName" placeholder="Contoh: Master Data"
                                    autofocus class="dark:placeholder-zinc-500" />
                            </div>
                            @error('moduleName')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </fieldset>
                        <fieldset class="fieldset col-span-full">
                            <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Slug</legend>
                            <div
                                class="input input-bordered w-full bg-zinc-50 dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100">
                                <input type="text" wire:model="moduleSlug" placeholder="Contoh: master-data"
                                    class="dark:placeholder-zinc-500" />
                            </div>
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Icon</legend>
                            <div
                                class="input input-bordered w-full dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100">
                                <input type="text" wire:model="moduleIcon" placeholder="heroicon-o-cube"
                                    class="dark:placeholder-zinc-500" />
                            </div>
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Urutan
                            </legend>
                            <div
                                class="input input-bordered w-full dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100">
                                <input type="number" wire:model="moduleOrder" placeholder="0" min="0" />
                            </div>
                        </fieldset>
                    </div>
                    <fieldset class="fieldset">
                        <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Deskripsi
                        </legend>
                        <div
                            class="textarea input-bordered w-full dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100">
                            <textarea wire:model="moduleDescription" placeholder="Deskripsi singkat..." rows="2"
                                class="dark:placeholder-zinc-500"></textarea>
                        </div>
                    </fieldset>
                    <div class="flex items-center gap-2 p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                        <input type="checkbox" wire:model="moduleIsActive"
                            class="checkbox checkbox-primary checkbox-sm" id="module-active" />
                        <label for="module-active"
                            class="text-sm cursor-pointer text-zinc-700 dark:text-zinc-300">Aktifkan modul ini</label>
                    </div>
                    <div class="modal-action gap-2">
                        <button type="button" class="btn btn-ghost dark:text-zinc-400 dark:hover:text-zinc-200"
                            wire:click="$set('showCreateModuleModal', false)">Batal</button>
                        <button type="submit" class="btn bg-blue-600 hover:bg-blue-700 text-white border-blue-700">
                            <x-heroicon-o-plus class="size-4" />Tambah Modul
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-backdrop bg-black/40 dark:bg-black/60"
                wire:click="$set('showCreateModuleModal', false)"></div>
        </dialog>
    @endif

    {{-- =========== MODAL: TAMBAH MENU =========== --}}
    @if ($showCreateMenuModal)
        <dialog open class="modal modal-open z-50">
            <div
                class="modal-box bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 mb-1">Tambah Menu Baru</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-5">Menu tampil di sidebar berdasarkan izin
                    pengguna.</p>
                <form wire:submit="createMenu" class="space-y-4">
                    <fieldset class="fieldset">
                        <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Nama Menu <span
                                class="text-red-500">*</span></legend>
                        <div
                            class="input input-bordered w-full dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100">
                            <input type="text" wire:model="menuName" placeholder="Contoh: Data Produk" autofocus
                                class="dark:placeholder-zinc-500" />
                        </div>
                        @error('menuName')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </fieldset>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <fieldset class="fieldset">
                            <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Icon</legend>
                            <div
                                class="input input-bordered w-full dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100">
                                <input type="text" wire:model="menuIcon" placeholder="heroicon-o-cube"
                                    class="dark:placeholder-zinc-500" />
                            </div>
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Route
                            </legend>
                            <div
                                class="input input-bordered w-full dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100">
                                <input type="text" wire:model="menuRoute" placeholder="products.index"
                                    class="dark:placeholder-zinc-500" />
                            </div>
                        </fieldset>
                    </div>
                    <fieldset class="fieldset">
                        <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Modul</legend>
                        <select
                            class="select select-bordered w-full dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100"
                            wire:model="menuModuleId">
                            <option value="0">-- Pilih Modul --</option>
                            @foreach ($this->allModules as $mod)
                                <option value="{{ $mod->id }}">{{ $mod->name }}</option>
                            @endforeach
                        </select>
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Parent Menu
                        </legend>
                        <select
                            class="select select-bordered w-full dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100"
                            wire:model="menuParentId">
                            <option value="0">-- Menu Utama --</option>
                            @foreach ($this->allMenus as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Permission (Izin)
                        </legend>
                        <select
                            class="select select-bordered w-full dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100"
                            wire:model="menuPermissionId">
                            <option value="0">-- Tidak ada --</option>
                            @foreach ($this->allPermissions as $perm)
                                <option value="{{ $perm->id }}">{{ $perm->name }} ({{ $perm->slug }})
                                </option>
                            @endforeach
                        </select>
                    </fieldset>
                    <div class="grid grid-cols-2 gap-4 items-end">
                        <fieldset class="fieldset">
                            <legend class="legend text-sm font-semibold text-zinc-700 dark:text-zinc-300">Urutan
                            </legend>
                            <div
                                class="input input-bordered w-full dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100">
                                <input type="number" wire:model="menuOrder" placeholder="0" min="0" />
                            </div>
                        </fieldset>
                        <div class="flex items-center gap-2 pb-1">
                            <input type="checkbox" wire:model="menuIsActive"
                                class="checkbox checkbox-primary checkbox-sm" id="menu-active" />
                            <label for="menu-active"
                                class="text-sm cursor-pointer text-zinc-700 dark:text-zinc-300">Aktifkan menu</label>
                        </div>
                    </div>
                    <div class="modal-action gap-2">
                        <button type="button" class="btn btn-ghost dark:text-zinc-400 dark:hover:text-zinc-200"
                            wire:click="$set('showCreateMenuModal', false)">Batal</button>
                        <button type="submit" class="btn bg-blue-600 hover:bg-blue-700 text-white border-blue-700">
                            <x-heroicon-o-plus class="size-4" />Tambah Menu
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-backdrop bg-black/40 dark:bg-black/60" wire:click="$set('showCreateMenuModal', false)">
            </div>
        </dialog>
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
</style>
