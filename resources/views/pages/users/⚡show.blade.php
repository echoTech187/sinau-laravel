<?php
use App\Models\User;
use App\Helpers\AuditLogger;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $columns = [
        [
            'name' => 'name',
            'label' => 'Nama Lengkap',
            'sortable' => true,
        ],
        [
            'name' => 'email',
            'label' => 'Email',
            'sortable' => true,
        ],
        [
            'name' => 'role.role',
            'label' => 'Role',
            'sortable' => false,
        ],
        [
            'name' => 'created_at',
            'label' => 'Tanggal Dibuat',
            'sortable' => true,
            'format' => 'datetime',
        ],
    ];

    public string $sortColumn = '';

    public string $sortDirection = 'asc';

    public string $search = '';

    public string $perPage = '10';

    #[Computed]
    public function users()
    {
        return User::select('id', 'name', 'email', 'created_at', 'role_id')
            ->with('role')
            ->when($this->search, fn($q) => $q->where(fn($sub) => $sub->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")))
            ->tap(fn($query) => $this->sortColumn ? $query->orderBy($this->sortColumn, $this->sortDirection) : $query)

            ->paginate($this->perPage == 'Semua' ? null : $this->perPage);
    }
    public function create()
    {
        return redirect()->route('user.create');
    }

    public function edit(User $user)
    {
        return redirect()->route('user.edit', $user);
    }

    public function sort($column)
    {
        $this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
        $this->sortColumn = $column;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function datetime($value)
    {
        return date('d M Y', strtotime($value));
    }

    public function delete(User $user): void
    {
        // Cegah menghapus diri sendiri
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', type: 'error', title: 'Tidak Diizinkan', message: 'Anda tidak dapat menghapus akun sendiri.');
            return;
        }

        AuditLogger::record('user_deleted', null, [
            'user_name' => $user->name,
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        $user->delete();

        $this->dispatch('notify', type: 'success', title: 'Berhasil', message: "Pengguna {$user->name} berhasil dihapus.");
    }

    public function import(): void
    {
        $this->dispatch('notify', type: 'info', title: 'Segera Hadir', message: 'Fitur import pengguna sedang dalam pengembangan.');
    }
};
?>

<div class="container relative min-h-screen pb-10">
    <x-slot:title>{{ __('Kelola User') }}</x-slot:title>

    <!-- Decorative Background Blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-150 h-150 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-1/4 w-125 h-125 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-6">
        <!-- Header -->
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/50 pb-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-linear-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/30">
                            <x-heroicon-o-users class="w-6 h-6 text-white" />
                        </div>
                        {{ __('Kelola Pengguna & Izin') }}
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Kelola seluruh data pengguna, peran mereka di sistem, dan izinkan akses fitur secara granular.
                    </p>
                </div>
            </header>
        </div>

        <!-- Table Component -->
        <x-pages::users.table.index :data="$this->users" :columns="$columns" :sortColumn="$sortColumn" :sortDirection="$sortDirection" />
    </div>
</div>
