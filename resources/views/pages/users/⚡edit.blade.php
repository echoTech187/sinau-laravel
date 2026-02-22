<?php
use App\Models\User;
use App\Models\Roles;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    public User $user;

    public $name = '';
    public $email = '';
    public $role_id = '';
    public $role = '';

    public function mount(User $user)
    {
        $this->user = $user;

        if ($this->user->exists) {
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->role_id = $this->user->role_id;
        }
    }
    #[Computed]
    public function roles()
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
            $this->toast(type: 'error', title: 'Terjadi kesalahan', description: 'Gagal menyimpan data. Silakan coba lagi.', position: 'toast-top toast-end', icon: 'o-information-circle', css: 'alert-info', timeout: 3000, redirectTo: null);
            return;
        } else {
            $this->toast(type: 'success', title: 'It is done!', description: 'Data berhasil disimpan.', position: 'toast-top toast-end', icon: 'o-information-circle', css: 'alert-info', timeout: 3000, redirectTo: route('users.show'));
        }
    }

    public function createRole()
    {
        $this->validate([
            'role' => ['required', 'string', 'max:255', 'unique:roles,role'],
        ]);

        Roles::create([
            'slug' => strtolower(str_replace(' ', '_', $this->role)),
            'role' => $this->role,
        ]);

        $this->toast(type: 'success', title: 'It is done!', description: 'Data berhasil disimpan.', position: 'toast-top toast-end', icon: 'o-information-circle', css: 'alert-info', timeout: 3000, redirectTo: null);

        $this->role = '';
        Flux::modals()->close();
    }
};

?>

<div class="container space-y-6">
    @include('partials.heading', [
        'title' => $user->slug ? 'Edit Pengguna' : 'Tambah Pengguna Baru',
        'description' => 'Lengkapi informasi dibawah ini untuk membuat akun baru pengguna.',
    ])
    <flux:heading class="sr-only">Create New User</flux:heading>
    <x-pages::users.layout>
        <x-pages::users.form.index :user="$user" :roles="$this->roles" />
    </x-pages::users.layout>
</div>
