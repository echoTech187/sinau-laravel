@props([
    'icon' => 'heroicon-o-archive-box-x-mark',
    'message' => 'No items found.',
])

<div {{ $attributes->merge(['class' => 'w-full h-full flex flex-col gap-4 items-center justify-center h-32']) }}>
    <x-icons :name="$icon" class="size-24 text-gray-400" />
    <span class="ms-2 text-gray-400">{{ $message }}</span>

    @if (isset($action))
        <div class="mt-4">
            {{ $action }}
        </div>
    @endif
</div>
