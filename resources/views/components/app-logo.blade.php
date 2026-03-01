@props([
    'sidebar' => false,
])

@if ($sidebar)
    <a {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
        <x-app-logo-icon class="size-8 text-blue-600" />
        <span class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">
            Sinau<span class="text-blue-600">Laravel</span>
        </span>
    </a>
@else
    <a {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
        <x-app-logo-icon class="size-8 text-blue-600" />
    </a>
@endif
