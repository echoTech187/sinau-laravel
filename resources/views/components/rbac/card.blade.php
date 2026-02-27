@if ($this->roles->isEmpty())
    <x-empty-state message="No roles found.">
        <x-slot:action>
            <button class="btn btn-primary" wire:click="create">Create Roles</button>
        </x-slot:action>
    </x-empty-state>
@else
    <div class="grid gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 ">
        @foreach ($this->roles as $role)
            <x-rbac.card-item :role="$role" key="{{ $role->id }}" />
        @endforeach
    </div>
@endif
