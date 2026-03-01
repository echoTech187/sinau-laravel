@props([
    'name' => auth()->user()->name,
    'initials' => auth()->user()->initials(),
])

<div x-data="{ open: false }" class="relative w-full">
    <!-- Trigger Button -->
    <button @click="open = !open"
        class="w-full flex items-center gap-3 p-2 rounded-xl transition-all duration-200 
                   hover:bg-zinc-50 dark:hover:bg-zinc-900 border border-transparent 
                   hover:border-zinc-200 dark:hover:border-zinc-800"
        :class="open ? 'bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 shadow-sm' : ''">
        <div
            class="size-9 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
            {{ $initials }}
        </div>
        <div class="flex-1 text-left min-w-0">
            <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 truncate leading-tight">{{ $name }}
            </p>
            <p class="text-[10px] font-medium text-zinc-400 dark:text-zinc-500 truncate mt-0.5">
                {{ auth()->user()->role->role ?? 'User' }}</p>
        </div>
        <x-heroicon-o-chevron-up-down class="size-4 text-zinc-400" />
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
        class="absolute bottom-full left-0 w-full mb-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl z-50 overflow-hidden backdrop-blur-xl"
        style="display: none;">

        <!-- User Info Header -->
        <div
            class="p-4 bg-zinc-50/50 dark:bg-zinc-800/20 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-3">
            <div
                class="size-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                <x-heroicon-o-user class="size-5" />
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100 truncate">{{ $name }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>

        <div class="p-2 space-y-0.5">
            <!-- Settings Link -->
            <a href="{{ route('profile.edit') }}" wire:navigate
                class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-100 rounded-xl transition-colors group">
                <x-heroicon-o-cog-6-tooth
                    class="size-4 text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300" />
                {{ __('Pengaturan Profil') }}
            </a>

            <div class="my-1 h-px bg-zinc-100 dark:bg-zinc-800"></div>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-2 px-3 py-2 text-sm font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 rounded-xl transition-colors group">
                    <x-heroicon-o-arrow-right-start-on-rectangle class="size-4 text-red-400 group-hover:text-red-500" />
                    {{ __('Keluar Sistem') }}
                </button>
            </form>
        </div>
    </div>
</div>
