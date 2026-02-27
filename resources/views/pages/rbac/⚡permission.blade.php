<?php
use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Roles;
use App\Models\Modules;
use Illuminate\Database\Eloquent\Collection;

new class extends Component {
    public Roles $roles;

    public function mount(Roles $roles)
    {
        $this->roles = $roles;
    }
    #[Computed]
    public function permissions(): Collection
    {
        return Modules::select('modules.*')->distinct()->join('permissions', 'permissions.module_id', '=', 'modules.id')->join('role_has_permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')->with('permissions')->where('role_has_permissions.role_id', $this->roles->id)->get();
    }
};
?>

@if ($this->roles != null)
    <div class="container space-y-6 h-full">
        @include('partials.heading', [
            'title' => 'Manage Roles & Permissions',
            'description' => 'Kelola seluruh data akses pengguna dan izin akses fitur-fitur yang tersedia.',
        ])
        <flux:heading class="sr-only">Role Based Access Control Manager</flux:heading>

        @foreach ($this->permissions as $groupModule)
            <div class="collapse collapse-arrow bg-base-100 border-0 border-base-300 mb-3">
                <input type="radio" name="my-accordion-3" />
                <div class="collapse-title font-semibold">
                    <div class="flex items-center justify-start w-full! gap-2">
                        <div class="flex flex-col flex-1">
                            <h5 class="font-bold">{{ $groupModule->name }}</h5>
                            <p class="text-sm text-gray-700">{{ $groupModule->description }}</p>
                        </div>
                    </div>
                </div>
                <div class="collapse-content text-sm">

                    @foreach ($groupModule->permissions as $permission)
                        <div class="flex gap-2 items-center bg-white rounded-md p-3">
                            {{ $permission->name }}
                        </div>
                    @endforeach
                </div>
        @endforeach
    </div>
@endif
