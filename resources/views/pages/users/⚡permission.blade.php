<?php
use App\Models\Branch;
use App\Models\Permissions;
use App\Models\Roles;
use App\Models\User;
use App\Helpers\AuditLogger;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new class extends Component {
    public User $user;

    public $userId;

    public array $userPermissions = [];

    public string $search = '';

    public string $activeTab = 'roles';

    // Properti untuk Manajemen Role Tambahan
    public $selectedRole = '';

    public $startsAt = null;

    public $expiresAt = null;

    public $dataScope = '';

    public array $selectedBranches = [];

    public array $additionalRoles = [];

    // State untuk mode edit
    public $editingRoleId = null; // role_id yang sedang diedit

    public function mount(User $user)
    {
        $this->user = $user;
        $this->userId = $user->id;

        // Load Direct Overrides
        if (method_exists($this->user, 'permissions')) {
            $this->userPermissions = $this->user->permissions()->pluck('permissions.id')->toArray();
        }

        $this->loadAdditionalRoles();
    }

    public function loadAdditionalRoles()
    {
        if (method_exists($this->user, 'roles')) {
            $this->additionalRoles = DB::table('user_has_roles')->join('roles', 'user_has_roles.role_id', '=', 'roles.id')->where('user_id', $this->user->id)->select('roles.role as role_name', 'roles.slug', 'user_has_roles.*')->get()->toArray();
        }
    }

    #[Computed]
    public function groupedPermissions()
    {
        return Permissions::query()->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('slug', 'like', "%{$this->search}%"))->get()->groupBy('module_id');
    }

    #[Computed]
    public function availableRoles()
    {
        // Jangan tampilkan role utama pengguna di dropdown
        return Roles::where('id', '!=', $this->user->role_id)->get();
    }

    #[Computed]
    public function availableBranches()
    {
        return Branch::orderBy('name')->get();
    }

    public function togglePermission($permissionId)
    {
        if (in_array($permissionId, $this->userPermissions)) {
            $this->userPermissions = array_diff($this->userPermissions, [$permissionId]);
        } else {
            $this->userPermissions[] = $permissionId;
        }
    }

    // Aksi: Tambahkan Role Ekstra
    public function addRole()
    {
        $this->validate([
            'selectedRole' => 'required|exists:roles,id',
            'startsAt' => 'nullable|date',
            'expiresAt' => 'nullable|date|after_or_equal:startsAt',
        ]);

        $scopeData = null;

        // Prioritaskan pilihan Checkbox Branches
        if (!empty($this->selectedBranches)) {
            // Konversi dari array string id menjadi array integer
            $branchIds = array_map('intval', $this->selectedBranches);
            $scopeData = json_encode(['branch_id' => $branchIds]);
        }
        // Fallback jika pengguna masih mengetik JSON manual
        elseif (!empty($this->dataScope)) {
            $scopeArray = json_decode($this->dataScope, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $scopeData = $this->dataScope;
            } else {
                $this->addError('dataScope', 'Format JSON Data Scope tidak valid.');

                return;
            }
        }

        DB::table('user_has_roles')->updateOrInsert(
            ['user_id' => $this->userId, 'role_id' => $this->selectedRole],
            [
                'starts_at' => $this->startsAt ?: null,
                'expires_at' => $this->expiresAt ?: null,
                'data_scope' => $scopeData,
            ],
        );

        // Audit trail
        $addedRole = Roles::find($this->selectedRole);
        AuditLogger::record(AuditLogger::USER_ROLE_ASSIGNED, $this->userId, [
            'user_name' => $this->user->name,
            'role' => $addedRole?->role,
            'role_id' => $this->selectedRole,
            'starts_at' => $this->startsAt,
            'expires_at' => $this->expiresAt,
            'data_scope' => $scopeData,
        ]);

        $this->reset(['selectedRole', 'startsAt', 'expiresAt', 'dataScope', 'selectedBranches']);
        $this->editingRoleId = null;
        $this->loadAdditionalRoles();

        if (method_exists($this->user, 'clearPermissionCache')) {
            $this->user->clearPermissionCache();
        }

        $this->dispatch('notify', type: 'success', title: 'Berhasil', message: 'Peran tambahan berhasil ditugaskan.');
    }

    // Aksi: Buka mode Edit untuk Role Ekstra
    public function editRole($roleId): void
    {
        $existing = DB::table('user_has_roles')->where('user_id', $this->userId)->where('role_id', $roleId)->first();

        if (!$existing) {
            return;
        }

        $this->editingRoleId = $roleId;
        $this->selectedRole = $roleId;
        $this->startsAt = $existing->starts_at ? \Carbon\Carbon::parse($existing->starts_at)->format('Y-m-d\TH:i') : null;
        $this->expiresAt = $existing->expires_at ? \Carbon\Carbon::parse($existing->expires_at)->format('Y-m-d\TH:i') : null;
        $this->dataScope = $existing->data_scope ?? '';

        // Populate selected branches dari data_scope
        $this->selectedBranches = [];
        if ($existing->data_scope) {
            $decoded = json_decode($existing->data_scope, true);
            if (isset($decoded['branch_id'])) {
                $this->selectedBranches = array_map('strval', (array) $decoded['branch_id']);
            }
        }
    }

    // Aksi: Simpan perubahan Role Ekstra
    public function updateRole(): void
    {
        $this->validate([
            'startsAt' => 'nullable|date',
            'expiresAt' => 'nullable|date|after_or_equal:startsAt',
        ]);

        $scopeData = null;
        if (!empty($this->selectedBranches)) {
            $branchIds = array_map('intval', $this->selectedBranches);
            $scopeData = json_encode(['branch_id' => $branchIds]);
        } elseif (!empty($this->dataScope)) {
            $scopeArray = json_decode($this->dataScope, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $scopeData = $this->dataScope;
            } else {
                $this->addError('dataScope', 'Format JSON Data Scope tidak valid.');
                return;
            }
        }

        DB::table('user_has_roles')
            ->where('user_id', $this->userId)
            ->where('role_id', $this->editingRoleId)
            ->update([
                'starts_at' => $this->startsAt ?: null,
                'expires_at' => $this->expiresAt ?: null,
                'data_scope' => $scopeData,
            ]);

        // Audit trail
        $updatedRole = Roles::find($this->editingRoleId);
        AuditLogger::record('user_role_scope_updated', $this->userId, [
            'user_name' => $this->user->name,
            'role' => $updatedRole?->role,
            'role_id' => $this->editingRoleId,
            'starts_at' => $this->startsAt,
            'expires_at' => $this->expiresAt,
            'data_scope' => $scopeData,
        ]);

        $this->cancelEditRole();
        $this->loadAdditionalRoles();

        if (method_exists($this->user, 'clearPermissionCache')) {
            $this->user->clearPermissionCache();
        }

        $this->dispatch('notify', type: 'success', title: 'Berhasil', message: 'Peran tambahan berhasil diperbarui.');
    }

    // Reset mode edit
    public function cancelEditRole(): void
    {
        $this->editingRoleId = null;
        $this->reset(['selectedRole', 'startsAt', 'expiresAt', 'dataScope', 'selectedBranches']);
    }

    // Aksi: Cabut Role Ekstra
    public function removeRole($roleId)
    {
        // Ambil nama role sebelum dihapus untuk log
        $removedRole = Roles::find($roleId);

        DB::table('user_has_roles')->where('user_id', $this->userId)->where('role_id', $roleId)->delete();

        $this->loadAdditionalRoles();

        if (method_exists($this->user, 'clearPermissionCache')) {
            $this->user->clearPermissionCache();
        }

        // Audit trail
        AuditLogger::record(AuditLogger::USER_ROLE_REVOKED, $this->userId, [
            'user_name' => $this->user->name,
            'role' => $removedRole?->role,
            'role_id' => $roleId,
        ]);

        $this->dispatch('notify', type: 'success', title: 'Dicabut', message: 'Peran tambahan berhasil dihapus.');
    }

    public function save()
    {
        // Simpan direct overrides ke tabel pivot permission_user (sebenarny user_has_permissions)
        if (method_exists($this->user, 'permissions')) {
            $oldCount = $this->user->permissions()->count();
            $this->user->permissions()->sync($this->userPermissions);
            if (method_exists($this->user, 'clearPermissionCache')) {
                $this->user->clearPermissionCache();
            }

            // Audit trail
            AuditLogger::record(AuditLogger::DIRECT_PERMISSION_GRANT, $this->userId, [
                'user_name' => $this->user->name,
                'permission_ids' => $this->userPermissions,
                'count' => count($this->userPermissions),
                'previous_count' => $oldCount,
            ]);

            $this->dispatch('notify', type: 'success', title: 'Berhasil!', message: 'Izin akses langsung berhasil diperbarui.');
        } else {
            $this->dispatch('notify', type: 'error', title: 'Gagal', message: 'Relasi permissions tidak ditemukan di model user.');
        }
    }
};
?>

<div class="container relative min-h-screen">
    <style>
        /* Folder Tab Styles */
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

        /* Sloped right edge using clip-path for inactive, or pseudo-element */
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

        /* Active Tab State */
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

        /* Hide the bottom border of active tab to merge with content container */
        .folder-tab-container {
            border-bottom: 2px solid #ffffff;
        }

        :is(.dark .folder-tab-container) {
            border-bottom: 2px solid #27272a;
        }
    </style>
    <x-slot:title>Izin Akses Spesifik</x-slot:title>
    <!-- Decorative Background Blob -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div
            class="absolute top-0 right-0 w-[600px] h-[600px] bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-1/4 w-[500px] h-[500px] bg-purple-500/10 dark:bg-purple-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8">
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-zinc-200 dark:border-zinc-700/50 pb-2">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 shadow-lg shadow-indigo-500/30">
                            <x-heroicon-o-shield-check class="w-6 h-6 text-white" />
                        </div>
                        Manajemen Izin Spesifik
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Kelola hak akses spesial (Direct Overrides) untuk pengguna <strong
                            class="text-zinc-800 dark:text-white">{{ $user->name }}</strong> di luar dari peran (Role)
                        bawaannya.
                    </p>
                </div>
                <div>
                    <a href="{{ route('users.show') }}" wire:navigate
                        class="btn btn-sm btn-ghost gap-2 rounded-xl text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all hover:-translate-x-1">
                        <x-heroicon-o-arrow-left class="size-4" />
                        Kembali ke Daftar
                    </a>
                </div>
            </header>
            <div class="sr-only">Manage User Permissions</div>
        </div>

        <div class="space-y-6 animate-fade-in-up" style="animation-delay: 0.1s">
            <x-pages::users.layout>
                {{-- Folder Style Tab Navigation --}}
                <div class="folder-tab-container flex items-end mb-6 pl-4">
                    <button type="button" wire:click="$set('activeTab', 'roles')"
                        class="folder-tab {{ $activeTab === 'roles' ? 'active' : '' }}">
                        <x-heroicon-o-user-group class="w-4 h-4" />
                        Role Tambahan
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'overrides')"
                        class="folder-tab {{ $activeTab === 'overrides' ? 'active' : '' }}">
                        <x-heroicon-o-key class="w-4 h-4" />
                        Izin Khusus
                    </button>
                </div>

                @if ($activeTab === 'roles')
                    <div class="space-y-6">
                        <!-- Form Tambah Role -->
                        <div
                            class="bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 mb-6 flex items-center gap-2">
                                @if ($editingRoleId)
                                    <x-heroicon-o-pencil-square class="w-5 h-5 text-amber-500" />
                                    Edit Peran Tambahan
                                @else
                                    <x-heroicon-o-plus-circle class="w-5 h-5 text-indigo-500" />
                                    Tugaskan Peran Baru
                                @endif
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                                @if ($editingRoleId)
                                    @php
                                        $editingRoleName =
                                            collect($additionalRoles)->firstWhere('role_id', $editingRoleId)
                                                ?->role_name ?? 'Role';
                                    @endphp
                                    <div
                                        class="flex items-center gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-2xl px-4 py-4">
                                        <x-heroicon-o-shield-check class="w-6 h-6 text-amber-500 shrink-0" />
                                        <div>
                                            <p
                                                class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                                Sedang Mengedit:</p>
                                            <p class="font-bold text-zinc-900 dark:text-zinc-100">
                                                {{ $editingRoleName }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-1.5">
                                        <label class="text-sm font-bold text-zinc-700 dark:text-zinc-300 ml-1">Pilih
                                            Role</label>
                                        <select wire:model="selectedRole"
                                            class="select select-bordered w-full bg-zinc-50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20">
                                            <option value="">Pilih Peran Tambahan...</option>
                                            @foreach ($this->availableRoles as $role)
                                                <option value="{{ $role->id }}">{{ $role->role }}
                                                    ({{ $role->slug }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <label
                                            class="text-xs font-bold text-zinc-500 dark:text-zinc-400 ml-1 uppercase">Waktu
                                            Mulai</label>
                                        <input type="datetime-local" wire:model="startsAt"
                                            class="input input-bordered w-full bg-zinc-50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-xl text-sm" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label
                                            class="text-xs font-bold text-zinc-500 dark:text-zinc-400 ml-1 uppercase">Kedaluwarsa</label>
                                        <input type="datetime-local" wire:model="expiresAt"
                                            class="input input-bordered w-full bg-zinc-50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-xl text-sm" />
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8">
                                <h4
                                    class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-[0.2em] mb-4 ml-1">
                                    Batasi Akses Cabang (Data Scope)
                                </h4>
                                <div
                                    class="grid grid-cols-2 lg:grid-cols-4 gap-3 bg-zinc-50/50 dark:bg-zinc-900/40 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                                    @if (count($this->availableBranches) > 0)
                                        @foreach ($this->availableBranches as $branch)
                                            <label
                                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer">
                                                <input type="checkbox" wire:model="selectedBranches"
                                                    value="{{ $branch->id }}"
                                                    class="checkbox checkbox-sm checkbox-primary rounded-md" />
                                                <span
                                                    class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $branch->name }}</span>
                                            </label>
                                        @endforeach
                                    @else
                                        <div class="col-span-full py-4 text-center">
                                            <p class="text-xs text-zinc-400 italic">Tidak ada data cabang tersedia.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 border-t border-zinc-200/50 dark:border-zinc-700/50 pt-4">
                                <div x-data="{ open: false }">
                                    <button type="button" @click="open = !open"
                                        class="text-[10px] font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                                        <x-heroicon-o-adjustments-horizontal class="size-3.5" />
                                        Advanced: Custom JSON Scope
                                    </button>
                                    <div x-show="open" x-collapse class="mt-4">
                                        <div class="space-y-2">
                                            <label
                                                class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest ml-1">Custom
                                                JSON (Ditimpa jika Cabang dicentang)</label>
                                            <textarea wire:model="dataScope" placeholder='{"department_id": [1, 5], "max_budget": 5000000}' rows="3"
                                                class="textarea textarea-bordered w-full bg-zinc-50 dark:bg-zinc-950/50 border-zinc-200 dark:border-zinc-800 rounded-xl focus:ring-2 focus:ring-indigo-500/20 font-mono text-xs leading-relaxed"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end gap-3">
                                @if ($editingRoleId)
                                    <button type="button" wire:click="cancelEdit"
                                        class="btn btn-sm px-6 rounded-xl border-zinc-200 dark:border-zinc-700 font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all">
                                        Batal Edit
                                    </button>
                                    <button type="button" wire:click="updateRole"
                                        class="btn btn-sm px-6 rounded-xl bg-amber-500 hover:bg-amber-600 text-white border-0 shadow-lg shadow-amber-500/30 transition-all hover:-translate-y-0.5 font-bold">
                                        Simpan Perubahan
                                    </button>
                                @else
                                    <button type="button" wire:click="addRole"
                                        class="btn btn-sm px-8 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/30 transition-all hover:-translate-y-0.5 font-bold">
                                        Perbarui Hak Akses
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Daftar Role Tambahan -->
                        <div class="mt-12">
                            <h3
                                class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-[0.2em] mb-6 ml-1">
                                Peran Tambahan Aktif
                            </h3>
                            @if (empty($additionalRoles))
                                <div
                                    class="text-center py-12 bg-zinc-50/50 dark:bg-zinc-900/40 rounded-3xl border border-dashed border-zinc-200 dark:border-zinc-800">
                                    <x-heroicon-o-user-group
                                        class="w-12 h-12 text-zinc-300 dark:text-zinc-700 mx-auto mb-4" />
                                    <p class="text-zinc-500 dark:text-zinc-400 font-medium">Pengguna belum memiliki
                                        peran tambahan lain.</p>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                                    @foreach ($additionalRoles as $ar)
                                        <div
                                            class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 flex flex-col group hover:shadow-xl transition-all duration-300">
                                            <div class="flex justify-between items-start mb-4">
                                                <div class="space-y-1">
                                                    <h4
                                                        class="font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                                        <div class="size-2 rounded-full bg-indigo-500"></div>
                                                        {{ $ar->role_name }}
                                                    </h4>
                                                    <span
                                                        class="inline-block text-[10px] font-bold font-mono text-indigo-500 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 px-2 py-0.5 rounded-md border border-indigo-100 dark:border-indigo-500/20 uppercase tracking-widest">{{ $ar->slug }}</span>
                                                </div>
                                                <div
                                                    class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button wire:click="editRole({{ $ar->role_id }})"
                                                        class="btn btn-ghost btn-xs btn-square hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg text-amber-500">
                                                        <x-heroicon-o-pencil-square class="size-4" />
                                                    </button>
                                                    <button wire:click="removeRole({{ $ar->role_id }})"
                                                        wire:confirm="Yakin ingin mencabut peran ini?"
                                                        class="btn btn-ghost btn-xs btn-square hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg text-red-500">
                                                        <x-heroicon-o-trash class="size-4" />
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mt-2 space-y-2 text-xs text-zinc-500 dark:text-zinc-400">
                                                <div
                                                    class="flex justify-between items-center bg-zinc-50 dark:bg-zinc-800/50 p-2 rounded-xl">
                                                    <span
                                                        class="font-bold uppercase tracking-tighter opacity-50">Mulai</span>
                                                    <span
                                                        class="font-medium text-zinc-700 dark:text-zinc-300">{{ $ar->starts_at ? \Carbon\Carbon::parse($ar->starts_at)->format('d M Y') : 'Selamanya' }}</span>
                                                </div>
                                                <div
                                                    class="flex justify-between items-center bg-zinc-50 dark:bg-zinc-800/50 p-2 rounded-xl">
                                                    <span
                                                        class="font-bold uppercase tracking-tighter opacity-50">Selesai</span>
                                                    <span
                                                        class="font-medium {{ $ar->expires_at && \Carbon\Carbon::parse($ar->expires_at)->isPast() ? 'text-red-500' : 'text-zinc-700 dark:text-zinc-300' }}">
                                                        {{ $ar->expires_at ? \Carbon\Carbon::parse($ar->expires_at)->format('d M Y') : 'Tanpa Batas' }}
                                                    </span>
                                                </div>
                                                @if ($ar->data_scope)
                                                    <div
                                                        class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                                                        <span
                                                            class="block mb-2 font-bold text-[10px] uppercase tracking-wider text-zinc-400 flex items-center gap-1">
                                                            <x-heroicon-o-map-pin
                                                                class="w-3.5 h-3.5 text-indigo-500" />
                                                            Batas Regional Cabang
                                                        </span>
                                                        @php
                                                            $scopeDecoded = json_decode($ar->data_scope, true);
                                                            $branchesText =
                                                                '<div class="text-[10px] text-zinc-400 italic">Format data manual</div>';

                                                            if (isset($scopeDecoded['branch_id'])) {
                                                                $bIds = is_array($scopeDecoded['branch_id'])
                                                                    ? $scopeDecoded['branch_id']
                                                                    : [$scopeDecoded['branch_id']];
                                                                $branchNames = collect($this->availableBranches)
                                                                    ->whereIn('id', $bIds)
                                                                    ->pluck('name')
                                                                    ->toArray();

                                                                if (count($branchNames) > 0) {
                                                                    $branchesText =
                                                                        '<div class="flex flex-wrap gap-1.5 mt-1">';
                                                                    foreach ($branchNames as $bn) {
                                                                        $branchesText .=
                                                                            '<span class="bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 px-2 py-1 rounded text-[10px] font-bold border border-indigo-100 dark:border-indigo-500/20 uppercase tracking-tighter">' .
                                                                            htmlspecialchars($bn) .
                                                                            '</span>';
                                                                    }
                                                                    $branchesText .= '</div>';
                                                                }
                                                            }
                                                        @endphp
                                                        <div>
                                                            {!! $branchesText !!}
                                                        </div>
                                                        <details class="group mt-3">
                                                            <summary
                                                                class="text-[9px] font-bold uppercase tracking-widest text-zinc-400 hover:text-indigo-500 cursor-pointer transition-colors list-none flex items-center gap-1">
                                                                <x-heroicon-o-chevron-right
                                                                    class="size-2.5 transition-transform group-open:rotate-90" />
                                                                View Raw JSON
                                                            </summary>
                                                            <div
                                                                class="mt-2 bg-zinc-950 rounded-xl p-3 border border-zinc-800">
                                                                <code
                                                                    class="block overflow-x-auto text-[9px] font-mono text-zinc-400 leading-relaxed">{{ $ar->data_scope }}</code>
                                                            </div>
                                                        </details>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($activeTab === 'overrides')
                    <form wire:submit="save">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 mt-4">
                            <div>
                                <h3
                                    class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-[0.2em] mb-2 ml-1">
                                    Izin Langsung (Direct Overrides)</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400 ml-1">Pilih izin tambahan yang
                                    diberikan khusus untuk pengguna ini.</p>
                            </div>
                            <div class="relative w-full md:w-80 group">
                                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                                    <x-heroicon-o-magnifying-glass
                                        class="size-4 text-zinc-400 group-focus-within:text-indigo-500 transition-colors" />
                                </div>
                                <input type="text" placeholder="Cari Izin..."
                                    wire:model.live.debounce.300ms="search"
                                    class="input input-sm w-full pl-11 bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20" />
                            </div>
                        </div>

                        @if ($this->groupedPermissions->isEmpty())
                            <div
                                class="text-center py-12 bg-zinc-50/50 dark:bg-zinc-900/40 rounded-3xl border border-dashed border-zinc-200 dark:border-zinc-800">
                                <x-heroicon-o-shield-exclamation
                                    class="w-12 h-12 text-zinc-300 dark:text-zinc-700 mx-auto mb-4" />
                                <p class="text-zinc-500 dark:text-zinc-400 font-medium">Tidak ada izin yang ditemukan.
                                </p>
                            </div>
                        @else
                            <div class="columns-1 md:columns-2 xl:columns-3 gap-6 space-y-6">
                                @foreach ($this->groupedPermissions as $moduleId => $permissions)
                                    @php
                                        $moduleName = $permissions->first()->module->name ?? 'Lainnya';
                                    @endphp
                                    <div
                                        class="break-inside-avoid bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-white/40 dark:border-zinc-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-300">
                                        <div
                                            class="flex items-center gap-3 mb-4 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                                            <div
                                                class="p-1.5 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
                                                <x-heroicon-o-folder class="w-4 h-4" />
                                            </div>
                                            <h3
                                                class="font-bold text-sm text-zinc-900 dark:text-zinc-100 tracking-tight">
                                                {{ $moduleName }}
                                            </h3>
                                        </div>
                                        <div class="grid grid-cols-1 gap-2">
                                            @foreach ($permissions as $permission)
                                                <label
                                                    class="flex items-start gap-3 p-3 rounded-xl border border-zinc-200/50 dark:border-zinc-700/50 bg-zinc-50/50 dark:bg-zinc-800/30 hover:bg-white dark:hover:bg-zinc-800 transition-all cursor-pointer hover:shadow-sm">
                                                    <div class="flex-shrink-0 mt-0.5">
                                                        <input type="checkbox" wire:model="userPermissions"
                                                            value="{{ $permission->id }}"
                                                            class="checkbox checkbox-sm checkbox-primary rounded-md" />
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div
                                                            class="font-bold text-sm text-zinc-900 dark:text-zinc-100 leading-tight">
                                                            {{ $permission->name }}</div>
                                                        @if ($permission->slug)
                                                            <div
                                                                class="text-[10px] font-bold font-mono text-zinc-400 dark:text-zinc-500 mt-1 uppercase tracking-widest truncate">
                                                                {{ $permission->slug }}</div>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-8 flex justify-end sticky bottom-6 z-40">
                                <button type="submit"
                                    class="btn px-8 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white border-none shadow-xl shadow-indigo-600/30 transition-all hover:-translate-y-0.5 font-bold">
                                    <x-heroicon-o-check class="size-5 mr-1" />
                                    Simpan Semua Izin
                                </button>
                            </div>
                        @endif
                    </form>
                @endif
            </x-pages::users.layout>
        </div>
    </div>
</div>
