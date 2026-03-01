<?php
use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Roles;
use App\Models\Modules;
use App\Helpers\AuditLogger;

new class extends Component {
    public Roles $roles;

    public function mount(Roles $roles)
    {
        $this->roles = $roles;
    }

    #[Computed]
    public function allModulesWithPermissions()
    {
        return Modules::with(['permissions' => fn($q) => $q->orderBy('name')])
            ->orderBy('order')
            ->get();
    }

    #[Computed]
    public function assignedPermissionIds(): array
    {
        return $this->roles->permissions()->pluck('permissions.id')->toArray();
    }

    public function togglePermission(int $permissionId): void
    {
        $wasAssigned = in_array($permissionId, $this->assignedPermissionIds);
        if ($wasAssigned) {
            $this->roles->permissions()->detach($permissionId);
        } else {
            $this->roles->permissions()->attach($permissionId);
        }
        unset($this->allModulesWithPermissions, $this->assignedPermissionIds);

        // Audit trail
        $perm = \App\Models\Permissions::find($permissionId);
        AuditLogger::record(AuditLogger::PERMISSION_TOGGLED, null, [
            'role' => $this->roles->role,
            'role_id' => $this->roles->id,
            'permission' => $perm?->name,
            'slug' => $perm?->slug,
            'action' => $wasAssigned ? 'revoked' : 'granted',
        ]);
    }

    public function toggleModule(int $moduleId): void
    {
        $module = Modules::findOrFail($moduleId);
        $permIds = $module->permissions->pluck('id')->toArray();
        $assigned = $this->assignedPermissionIds;
        $allAssigned = count($permIds) > 0 && !array_diff($permIds, $assigned);
        if ($allAssigned) {
            $this->roles->permissions()->detach($permIds);
        } else {
            $this->roles->permissions()->syncWithoutDetaching($permIds);
        }
        unset($this->allModulesWithPermissions, $this->assignedPermissionIds);

        // Audit trail
        AuditLogger::record(AuditLogger::MODULE_PERMISSIONS_SET, null, [
            'role' => $this->roles->role,
            'role_id' => $this->roles->id,
            'module' => $module->name,
            'action' => $allAssigned ? 'all_revoked' : 'all_granted',
            'count' => count($permIds),
        ]);
    }
};
?>

<div class="container space-y-5 h-full">
    <x-slot:title>Izin Akses Role</x-slot:title>
    @include('partials.heading', [
        'title' => 'Kelola Akses: ' . $this->roles->role,
        'description' => 'Atur izin akses fitur yang dapat digunakan oleh peran ini.',
    ])

    {{-- Breadcrumb --}}
    <nav class="flex items-center flex-wrap gap-2 text-sm text-zinc-500 dark:text-zinc-400 -mt-4">
        <a href="{{ route('rbac.show') }}" class="hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-1">
            <x-heroicon-o-shield-check class="size-4" />
            RBAC Manager
        </a>
        <x-heroicon-o-chevron-right class="size-4 text-zinc-300 dark:text-zinc-600" />
        <span class="text-zinc-800 dark:text-zinc-200 font-medium">{{ $this->roles->role }}</span>
    </nav>

    {{-- Role Info Card --}}
    <div
        class="flex flex-col sm:flex-row sm:items-center gap-4 p-4
                bg-white/60 dark:bg-zinc-800/60
                backdrop-blur-xl
                border border-white/30 dark:border-white/10
                rounded-2xl shadow-sm
                animate-fade-in-up">
        <div
            class="p-3 rounded-xl w-fit
                    bg-gradient-to-br from-blue-500 to-indigo-600
                    shadow-[0_4px_12px_rgba(59,130,246,0.4)]">
            <x-heroicon-o-user-group class="size-6 text-white" />
        </div>
        <div class="flex-1">
            <h2 class="font-bold text-zinc-900 dark:text-zinc-100">{{ $this->roles->role }}</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ $this->roles->description ?? 'Tidak ada deskripsi.' }}</p>
        </div>
        <div
            class="text-left sm:text-right border-t sm:border-t-0 sm:border-l border-blue-100 dark:border-blue-800/50 pt-3 sm:pt-0 sm:pl-4">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ count($this->assignedPermissionIds) }}
            </div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400">Izin Aktif</div>
        </div>
    </div>

    {{-- Modules & Permissions --}}
    @if ($this->allModulesWithPermissions->isEmpty())
        <x-empty-state message="Belum ada modul atau izin yang tersedia." />
    @else
        <div class="space-y-3">
            @foreach ($this->allModulesWithPermissions as $module)
                @php
                    $modulePermIds = $module->permissions->pluck('id')->toArray();
                    $assignedInModule = array_intersect($modulePermIds, $this->assignedPermissionIds);
                    $allModuleAssigned =
                        count($modulePermIds) > 0 && count($assignedInModule) === count($modulePermIds);
                @endphp
                <div
                    class="border border-zinc-200 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-900 shadow-sm overflow-hidden">
                    {{-- Module Header --}}
                    <div
                        class="flex flex-col sm:flex-row sm:items-center gap-3 px-4 py-3 bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-100 dark:border-zinc-700">
                        <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg w-fit shrink-0">
                            <x-heroicon-o-shield-check class="size-4 text-blue-500 dark:text-blue-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">{{ $module->name }}</h4>
                            @if ($module->description)
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $module->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-3">
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">
                                {{ count($assignedInModule) }}/{{ count($modulePermIds) }} izin
                            </span>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <span
                                    class="text-xs font-medium text-zinc-500 dark:text-zinc-400 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                    {{ $allModuleAssigned ? 'Cabut Semua' : 'Pilih Semua' }}
                                </span>
                                <input type="checkbox" class="toggle toggle-primary toggle-sm"
                                    {{ $allModuleAssigned ? 'checked' : '' }}
                                    wire:click="toggleModule({{ $module->id }})" />
                            </label>
                        </div>
                    </div>

                    {{-- Permissions Grid --}}
                    @if ($module->permissions->isEmpty())
                        <div class="px-4 py-5 text-sm text-zinc-400 dark:text-zinc-500 text-center">
                            Tidak ada izin di modul ini.
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 p-3">
                            @foreach ($module->permissions as $permission)
                                @php $isChecked = in_array($permission->id, $this->assignedPermissionIds); @endphp
                                <label
                                    class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-all duration-200 group/perm
                                    {{ $isChecked
                                        ? 'bg-blue-50/80 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700/50 shadow-sm'
                                        : 'bg-white/50 dark:bg-zinc-800/50 backdrop-blur-sm border-white/40 dark:border-zinc-700/40 hover:bg-white/70 dark:hover:bg-zinc-800/70 hover:border-zinc-300 dark:hover:border-zinc-600 hover:-translate-y-0.5' }}">
                                    <input type="checkbox" class="checkbox checkbox-primary checkbox-sm mt-0.5 shrink-0"
                                        {{ $isChecked ? 'checked' : '' }}
                                        wire:click="togglePermission({{ $permission->id }})" />
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium leading-tight text-zinc-800 dark:text-zinc-200">
                                            {{ $permission->name }}</p>
                                        <p class="text-xs text-zinc-400 dark:text-zinc-500 font-mono mt-0.5 truncate">
                                            {{ $permission->slug }}</p>
                                        @if ($permission->group_name)
                                            <span
                                                class="badge badge-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 border-0 mt-1">{{ $permission->group_name }}</span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Footer Actions --}}
    <div
        class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
        <a href="{{ route('rbac.show') }}"
            class="btn btn-ghost text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800">
            <x-heroicon-o-arrow-left class="size-4" />
            Kembali
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('rbac.field.security', $this->roles) }}"
                class="btn btn-outline border-rose-300 text-rose-600 hover:bg-rose-50 dark:border-rose-700 dark:text-rose-400 dark:hover:bg-rose-900/20 btn-sm gap-1.5">
                <x-heroicon-o-eye-slash class="size-4" />
                Field Security
                @php $fCount = \App\Models\FieldPermission::where('role_id', $this->roles->id)->count(); @endphp
                @if ($fCount > 0)
                    <span class="badge badge-xs bg-rose-500 text-white border-0">{{ $fCount }}</span>
                @endif
            </a>
            <a href="{{ route('rbac.add.teams', $this->roles) }}"
                class="btn btn-outline dark:border-zinc-600 dark:text-zinc-300 dark:hover:bg-zinc-800 btn-sm">
                <x-heroicon-o-user-plus class="size-4" />
                Kelola Anggota
            </a>
        </div>
    </div>
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
