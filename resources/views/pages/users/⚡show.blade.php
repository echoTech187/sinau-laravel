<?php
use App\Models\User;
use Livewire\Attributes\Computed;
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
        return User::query()
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
};
?>

<div class="container">
    @include('partials.heading', [
        'title' => 'Kelola Pengguna & Izin',
        'description' => 'Kelola seluruh data pengguna dan izin akses fitur-fitur yang tersedia.',
    ])
    <flux:heading class="sr-only">{{ __('Users Management') }}</flux:heading>
    <x-pages::users.layout>
        <x-pages::users.table.index :data="$this->users" :columns="$columns" :sortColumn="$sortColumn" :sortDirection="$sortDirection" />
    </x-pages::users.layout>
</div>
