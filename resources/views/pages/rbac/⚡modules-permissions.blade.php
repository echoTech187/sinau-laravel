<?php
use Livewire\Component;
use App\Models\Modules;
use App\Models\Permissions;
use Illuminate\Database\Eloquent\Collection;
new class extends Component {
    public Modules $modules;
    public Collection $permissions;

    public function mount(Modules $modules)
    {
        $this->modules = $modules;
    }
    #[Computed]
    public function permissions()
    {
        return Permissions::where('module_id', $this->modules->id)->get();
    }
};
?>

<div class="container">
    @include('partials.heading', [
        'title' => 'Modules Permissions Manager',
        'description' => 'Kelola seluruh data akses pengguna dan izin akses fitur-fitur yang tersedia.',
    ])
    <flux:heading class="sr-only">Role Based Access Control Manager</flux:heading>
    @if ($permissions->isEmpty())
        <div class="w-full h-full flex flex-col gap-4 items-center justify-center my-24">
            <x-icon name="fluentui-channel-dismiss-24" class="size-24 text-gray-400" />
            <span class="ms-2 text-gray-400">No permissions found.</span>
            <button class="btn btn-primary">Create Permissions</button>
        </div>
    @else
        <div class="grid gap-4 grid-cols-1 " wire:loading.lazy="true">
            @foreach ($permissions as $permission)
                <div key="{{ $permission->id }}" class="flex gap-2 items-center bg-white rounded-md p-3 shadow-md">
                    <x-icon name="{{ $permission->icon }}" class="size-12 text-gray-400" />
                    <div class="flex flex-col flex-1">
                        <span class="font-bold">{{ $permission->label }}</span>
                        <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
