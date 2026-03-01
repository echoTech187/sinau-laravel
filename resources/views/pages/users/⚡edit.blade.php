<?php
use App\Models\User;
use App\Models\Roles;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use Mary\Traits\Toast;
use Livewire\Attributes\Title;

new class extends Component {
    use Toast;
    public User $user;

    public $name = '';
    public $email = '';
    public $role_id = '';
    public $role = '';

    public function mount(User $user): void
    {
        $this->user = $user;

        if ($this->user->exists) {
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->role_id = $this->user->role_id;
        }
    }
    #[Computed]
    public function roles(): Collection
    {
        return Roles::select('id', 'role')->get();
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)],
            'role_id' => ['required', 'exists:roles,id'],
        ]);
        $this->user->slug = Uuid::uuid4()->toString();
        $this->user->name = $this->name;
        $this->user->email = $this->email;
        $this->user->role_id = $this->role_id;

        if (!$this->user->exists || !$this->user->password) {
            $this->user->password = bcrypt('password'); // Default password untuk user baru
        }

        $result = $this->user->save();

        if (!$result) {
            $this->dispatch('notify', type: 'error', title: 'Terjadi Kesalahan', message: 'Gagal menyimpan data. Silakan coba lagi.');
            return;
        } else {
            $this->dispatch('notify', type: 'success', title: 'Berhasil!', message: 'Data pengguna berhasil disimpan.');
            return redirect()->route('users.show');
        }
    }

    public function createRole(): void
    {
        $this->validate([
            'role' => ['required', 'string', 'max:255', 'unique:roles,role'],
        ]);

        Roles::create([
            'slug' => strtolower(str_replace(' ', '_', $this->role)),
            'role' => $this->role,
        ]);

        $this->dispatch('notify', type: 'success', title: 'Berhasil!', message: 'Role baru berhasil ditambahkan.');

        $this->role = '';
        $this->role = '';
        $this->dispatch('close-modal', 'create-role');
    }
};

?>

<div class="container relative min-h-screen">
    <x-slot:title>{{ $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</x-slot:title>
    <!-- Decorative Background Blob behind layout -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div
            class="absolute -top-1/4 -right-1/4 w-[800px] h-[800px] bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-[120px]">
        </div>
        <div
            class="absolute -bottom-1/4 -left-1/4 w-[800px] h-[800px] bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[120px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8">
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-zinc-200 dark:border-zinc-700/50 pb-2">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/30">
                            @if ($user->exists)
                                <x-heroicon-o-pencil-square class="w-6 h-6 text-white" />
                            @else
                                <x-heroicon-o-user-plus class="w-6 h-6 text-white" />
                            @endif
                        </div>
                        {{ $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ $user->exists ? 'Perbarui informasi profil dan hak akses pengguna ini.' : 'Lengkapi informasi di bawah ini untuk membuat akun pengguna baru.' }}
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
            <div class="sr-only">{{ $user->exists ? 'Edit User' : 'Create New User' }}</div>
        </div>

        <x-pages::users.layout>
            <x-pages::users.form.index :user="$user" :roles="$this->roles" />
        </x-pages::users.layout>
    </div>
</div>
