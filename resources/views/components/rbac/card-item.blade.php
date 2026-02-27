@props([
    'role' => null,
])
<x-card key="{{ $role->id }}"
    class="relative shadow-xl border border-gray-100 bg-white rounded-xl dark:border-black dark:bg-zinc-800! dark:text-white! p-4!">
    <x-slot name="title" class="flex items-stretch gap-2">
        <span>{{ $role->role }}</span>
        <span
            class="text-xs  flex items-center gap-2 w-fit font-semibold {{ $role->is_active ? 'text-green-600' : 'text-gray-600' }}">
            {{ $role->is_active ? 'Active' : 'Inactive' }}
        </span>
    </x-slot>
    <x-slot name="subtitle" class="flex items-stretch gap-2">
        @if ($role->users->isNotEmpty())
            @php
                $user = $role->users->first();
                $avatarUrl = $role->getUserAvatarUrl($user);
            @endphp
            @if ($avatarUrl)
                <div class="py-1 h-8">
                    <img class="object-cover rounded-full h-8 w-8 border-4 border-white dark:border-neutral-700"
                        src="{{ $avatarUrl }}" alt="{{ $user->name }}">
                </div>
            @endif
        @else
            <div class="py-1 h-8">
                No user found.
            </div>
        @endif

    </x-slot>
    <div
        class="absolute top-4 right-4 text-sm flex items-center gap-2 w-fit px-3 py-1 bg-gray-100 rounded-full dark:bg-zinc-800">
        <x-icons name="heroicon-o-user-group" class="size-4" />{{ $role->users_count }}
        Users
    </div>

    <x-slot name="separator" color="blue"></x-slot>
    <div class="py-4 text-sm text-zinc-500 dark:text-zinc-400">
        {{ $role->description ?? '-' }}
    </div>
    <div class="flex justify-between items-center gap-4">
        <x-button variant="outline" size="xs" icon="o-cog-6-tooth"
            class="w-fit text-xs! bg-transparent! text-zinc-900! hover:bg-zinc-100! hover:text-zinc-900! hover:border-zinc-900! dark:text-white! dark:hover:bg-zinc-800! dark:hover:text-white! dark:hover:border-zinc-800! border! border-zinc-200! dark:border-zinc-700!"
            wire:click="editRole({{ $role->id }})">
            {{ __('Kelola Akses') }}
        </x-button>
    </div>
</x-card>
