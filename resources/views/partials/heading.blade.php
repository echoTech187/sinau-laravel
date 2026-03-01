@props([
    'title' => '',
    'description' => '',
])
<div class="relative mb-8 w-full">
    <h1 class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white">{{ $title }}</h1>
    @if ($description)
        <p class="mt-2 text-lg text-zinc-500 dark:text-zinc-400 font-medium">{{ $description }}</p>
    @endif
    <div class="mt-6 h-px bg-zinc-200 dark:bg-zinc-800"></div>
</div>
