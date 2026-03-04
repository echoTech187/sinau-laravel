@props([
    'title',
    'description' => null,
    'icon' => null,
    'iconGradient' => 'from-sky-500 to-indigo-600',
    'iconShadow' => 'shadow-sky-500/20',
])

<header
    class="{{ $attributes->get('class') }} flex flex-wrap items-center justify-between gap-6 border-b border-zinc-200 dark:border-zinc-800 pb-6 animate-fade-in-up">
    <div class="flex-1 min-w-[280px]">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
            @if ($icon)
                <div
                    class="p-2.5 rounded-2xl bg-linear-to-br {{ $iconGradient }} shadow-lg {{ $iconShadow }} text-white">
                    <x-dynamic-component :component="$icon" class="w-6 h-6" />
                </div>
            @endif
            {{ $title }}
        </h1>
        @if ($description)
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                {{ $description }}
            </p>
        @endif
    </div>

    @if ($slot->isNotEmpty())
        <div class="flex flex-wrap items-center justify-end gap-3 w-full sm:w-auto">
            {{ $slot }}
        </div>
    @endif
</header>
