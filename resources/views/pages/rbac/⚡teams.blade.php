<?php
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Roles;
use App\Models\User;
use App\Helpers\AuditLogger;

new class extends Component {
    use WithPagination;

    public Roles $roles;
    public string $search = '';
    public string $searchUser = '';
    public bool $showAddModal = false;

    public function mount(Roles $roles)
    {
        $this->roles = $roles;
    }

    #[Computed]
    public function members()
    {
        return $this->roles->users()->when($this->search, fn($q) => $q->where(fn($sub) => $sub->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")))->paginate(10);
    }

    #[Computed]
    public function availableUsers()
    {
        return User::where(fn($q) => $q->whereNull('role_id')->orWhere('role_id', '!=', $this->roles->id))->when($this->searchUser, fn($q) => $q->where(fn($sub) => $sub->where('name', 'like', "%{$this->searchUser}%")->orWhere('email', 'like', "%{$this->searchUser}%")))->limit(20)->get();
    }

    public function addUser(int $userId): void
    {
        $user = User::find($userId);
        $oldRoleId = $user?->role_id;
        User::where('id', $userId)->update(['role_id' => $this->roles->id]);
        unset($this->members, $this->availableUsers);
        $this->dispatch('notify', type: 'success', message: 'Pengguna berhasil ditambahkan!');

        // Audit trail
        $oldRole = $oldRoleId ? \App\Models\Roles::find($oldRoleId) : null;
        AuditLogger::record(AuditLogger::ROLE_ASSIGNED, $userId, [
            'user_name' => $user?->name,
            'role' => $this->roles->role,
            'role_id' => $this->roles->id,
            'old_role' => $oldRole?->role,
        ]);
    }

    public function removeUser(int $userId): void
    {
        $user = User::find($userId);
        User::where('id', $userId)->update(['role_id' => null]);
        unset($this->members, $this->availableUsers);
        $this->dispatch('notify', type: 'success', message: 'Pengguna berhasil dicopot!');

        // Audit trail
        AuditLogger::record(AuditLogger::ROLE_REVOKED, $userId, [
            'user_name' => $user?->name,
            'role' => $this->roles->role,
            'role_id' => $this->roles->id,
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
};
?>

<div class="container space-y-6 h-full pb-10">
    <x-slot:title>Anggota Tim</x-slot:title>
    @include('partials.heading', [
        'title' => 'Kelola Anggota: ' . $this->roles->role,
        'description' => 'Atur pengguna yang memiliki peran ini.',
    ])

    {{-- Breadcrumb --}}
    <nav class="flex items-center flex-wrap gap-2 text-sm text-zinc-500 dark:text-zinc-400 -mt-4">
        <a href="{{ route('rbac.show') }}" class="hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-1">
            <x-heroicon-o-shield-check class="size-4" />RBAC Manager
        </a>
        <x-heroicon-o-chevron-right class="size-4 text-zinc-300 dark:text-zinc-600" />
        <a href="{{ route('rbac.permission.edit', $this->roles) }}"
            class="hover:text-blue-600 dark:hover:text-blue-400">{{ $this->roles->role }}</a>
        <x-heroicon-o-chevron-right class="size-4 text-zinc-300 dark:text-zinc-600" />
        <span class="text-zinc-800 dark:text-zinc-200 font-medium">Anggota</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- ===== Left: Member List ===== --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <label
                    class="input input-sm flex-1 border-zinc-200 dark:border-zinc-600 bg-zinc-100 dark:bg-zinc-800 focus-within:border-blue-400 dark:text-zinc-100 rounded outline-0">
                    <x-heroicon-o-magnifying-glass class="size-4 text-zinc-400 dark:text-zinc-500" />
                    <input type="search" placeholder="Cari anggota..." wire:model.live.debounce.300ms="search"
                        class="dark:placeholder-zinc-500" />
                </label>
                <button class="btn btn-sm bg-blue-600 hover:bg-blue-700 text-white border-blue-700 w-full sm:w-auto"
                    wire:click="$set('showAddModal', true)">
                    <x-heroicon-o-user-plus class="size-4" />Tambah Anggota
                </button>
            </div>

            {{-- Role Stats --}}
            <div
                class="flex flex-col sm:flex-row sm:items-center gap-3 p-4
                        bg-white/60 dark:bg-zinc-800/60
                        backdrop-blur-xl
                        border border-white/30 dark:border-white/10
                        rounded-2xl shadow-sm animate-fade-in-up">
                <div
                    class="p-2 rounded-xl w-fit
                            bg-gradient-to-br from-blue-500 to-indigo-600
                            shadow-[0_4px_12px_rgba(59,130,246,0.35)]">
                    <x-heroicon-o-user-group class="size-5 text-white" />
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $this->roles->role }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $this->roles->description ?? 'Tidak ada deskripsi.' }}</p>
                </div>
                <div class="text-left sm:text-right border-t sm:border-t-0 pt-2 sm:pt-0">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $this->members->total() }}</div>
                    <div class="text-xs text-zinc-400 dark:text-zinc-500">Anggota</div>
                </div>
            </div>

            {{-- Members Table --}}
            @if ($this->members->isEmpty())
                <x-empty-state message="Belum ada anggota dalam peran ini." />
            @else
                <div
                    class="overflow-x-auto rounded-2xl border border-white/30 dark:border-white/10
                            bg-white/60 dark:bg-zinc-800/60 backdrop-blur-xl shadow-sm">
                    <table class="table table-sm w-full min-w-[420px]">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <th class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">Pengguna
                                </th>
                                <th
                                    class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase hidden sm:table-cell">
                                    Email</th>
                                <th
                                    class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase hidden md:table-cell">
                                    Bergabung</th>
                                <th class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                            @foreach ($this->members as $user)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <img class="rounded-full w-8 h-8 object-cover shrink-0"
                                                src="{{ $user->getAvatarUrlAttribute() }}" alt="{{ $user->name }}" />
                                            <div>
                                                <p class="font-medium text-sm text-zinc-800 dark:text-zinc-200">
                                                    {{ $user->name }}</p>
                                                <p class="text-xs text-zinc-400 dark:text-zinc-500 sm:hidden">
                                                    {{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-sm text-zinc-500 dark:text-zinc-400 hidden sm:table-cell">
                                        {{ $user->email }}</td>
                                    <td class="text-xs text-zinc-400 dark:text-zinc-500 hidden md:table-cell">
                                        {{ $user->created_at?->format('d M Y') }}</td>
                                    <td>
                                        <button
                                            class="btn btn-xs btn-ghost text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"
                                            wire:click="removeUser({{ $user->id }})"
                                            wire:confirm="Yakin ingin mencopot {{ $user->name }}?">
                                            <x-heroicon-o-user-minus class="size-3.5" />
                                            <span class="hidden sm:inline">Copot</span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $this->members->links() }}</div>
            @endif
        </div>

        {{-- ===== Right: Quick Add Panel ===== --}}
        <div class="space-y-4">
            <div
                class="rounded-2xl border border-white/30 dark:border-white/10
                        bg-white/60 dark:bg-zinc-800/60 backdrop-blur-xl
                        shadow-sm p-4 space-y-3">
                <h3 class="font-semibold text-sm text-zinc-800 dark:text-zinc-200 flex items-center gap-2">
                    <div class="p-1.5 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600">
                        <x-heroicon-o-magnifying-glass class="size-3.5 text-white" />
                    </div>
                    Cari & Tambah Pengguna
                </h3>
                <label
                    class="input input-sm w-full border-zinc-200 dark:border-zinc-600 bg-zinc-100 dark:bg-zinc-800 focus-within:border-blue-400 dark:text-zinc-100 rounded outline-0">
                    <x-heroicon-o-magnifying-glass class="size-4 text-zinc-400 dark:text-zinc-500" />
                    <input type="search" placeholder="Nama atau email..." wire:model.live.debounce.300ms="searchUser"
                        class="dark:placeholder-zinc-500" />
                </label>
                @if ($this->availableUsers->isEmpty())
                    <p class="text-xs text-center text-zinc-400 dark:text-zinc-500 py-3">
                        {{ $this->searchUser ? 'Tidak ditemukan.' : 'Ketik untuk mencari pengguna.' }}
                    </p>
                @else
                    <div class="space-y-1.5 max-h-72 overflow-y-auto -mx-1 px-1">
                        @foreach ($this->availableUsers as $user)
                            <div
                                class="flex items-center gap-3 p-2 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 group transition-colors">
                                <img class="rounded-full w-8 h-8 object-cover shrink-0"
                                    src="{{ $user->getAvatarUrlAttribute() }}" alt="{{ $user->name }}" />
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200 truncate">
                                        {{ $user->name }}</p>
                                    <p class="text-xs text-zinc-400 dark:text-zinc-500 truncate">{{ $user->email }}
                                    </p>
                                </div>
                                <button
                                    class="btn btn-xs btn-ghost text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 opacity-0 group-hover:opacity-100 transition-opacity shrink-0"
                                    wire:click="addUser({{ $user->id }})">
                                    <x-heroicon-o-plus class="size-3.5" />
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex flex-col gap-2">
                <a href="{{ route('rbac.permission.edit', $this->roles) }}"
                    class="btn btn-outline btn-sm w-full dark:border-zinc-600 dark:text-zinc-300 dark:hover:bg-zinc-800">
                    <x-heroicon-o-shield-check class="size-4" />Kelola Izin Peran
                </a>
                <a href="{{ route('rbac.show') }}"
                    class="btn btn-ghost btn-sm w-full text-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800">
                    <x-heroicon-o-arrow-left class="size-4" />Kembali ke RBAC
                </a>
            </div>
        </div>
    </div>

    {{-- MODAL: TAMBAH ANGGOTA --}}
    @if ($showAddModal)
        <dialog open class="modal modal-open z-50">
            <div
                class="modal-box bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 max-w-lg w-full">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 mb-1">Tambah Anggota</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4">Pilih pengguna untuk ditambahkan ke peran
                    <strong class="text-zinc-800 dark:text-zinc-200">{{ $this->roles->role }}</strong>.
                </p>
                <label
                    class="input input-sm w-full border-zinc-200 dark:border-zinc-600 bg-zinc-100 dark:bg-zinc-800 dark:text-zinc-100 rounded outline-0 mb-3">
                    <x-heroicon-o-magnifying-glass class="size-4 text-zinc-400 dark:text-zinc-500" />
                    <input type="search" placeholder="Cari pengguna..." wire:model.live.debounce.300ms="searchUser"
                        autofocus class="dark:placeholder-zinc-500" />
                </label>

                @if ($this->availableUsers->isEmpty())
                    <div class="text-center py-8 text-zinc-400 dark:text-zinc-500 text-sm">
                        {{ $this->searchUser ? 'Tidak ada pengguna ditemukan.' : 'Ketik nama atau email untuk mencari.' }}
                    </div>
                @else
                    <div class="space-y-2 max-h-72 overflow-y-auto">
                        @foreach ($this->availableUsers as $user)
                            <div
                                class="flex items-center gap-3 p-3 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 border border-transparent hover:border-zinc-200 dark:hover:border-zinc-700 transition-colors">
                                <img class="rounded-full w-9 h-9 object-cover shrink-0"
                                    src="{{ $user->getAvatarUrlAttribute() }}" alt="{{ $user->name }}" />
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm text-zinc-800 dark:text-zinc-200">
                                        {{ $user->name }}</p>
                                    <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ $user->email }}</p>
                                    @if ($user->role)
                                        <span
                                            class="badge badge-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-300 border-0">Peran:
                                            {{ $user->role->role }}</span>
                                    @endif
                                </div>
                                <button
                                    class="btn btn-xs bg-blue-600 hover:bg-blue-700 text-white border-blue-700 shrink-0"
                                    wire:click="addUser({{ $user->id }})">
                                    <x-heroicon-o-plus class="size-3" />Tambah
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="modal-action">
                    <button class="btn btn-ghost dark:text-zinc-400 dark:hover:text-zinc-200"
                        wire:click="$set('showAddModal', false)">Tutup</button>
                </div>
            </div>
            <div class="modal-backdrop bg-black/40 dark:bg-black/60" wire:click="$set('showAddModal', false)"></div>
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

    .animate-fade-in-up {
        animation: fadeInUp 0.4s ease-out both;
    }
</style>
