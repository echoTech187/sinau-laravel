<div class="flex items-start max-md:flex-col gap-10">
    <!-- Sidebar Nav -->
    <div class="w-full md:w-64 shrink-0">
        <nav class="space-y-1">
            <h3 class="px-3 text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-4">Pengaturan Akun</h3>

            <a href="{{ route('profile.edit') }}" wire:navigate
                class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold transition-all {{ request()->routeIs('profile.edit') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                <x-heroicon-o-user class="size-4" />
                {{ __('Profile') }}
            </a>

            <a href="{{ route('user-password.edit') }}" wire:navigate
                class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold transition-all {{ request()->routeIs('user-password.edit') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                <x-heroicon-o-lock-closed class="size-4" />
                {{ __('Password') }}
            </a>

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <a href="{{ route('two-factor.show') }}" wire:navigate
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold transition-all {{ request()->routeIs('two-factor.show') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                    <x-heroicon-o-shield-check class="size-4" />
                    {{ __('Two-Factor Auth') }}
                </a>
            @endif

            <a href="{{ route('appearance.edit') }}" wire:navigate
                class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold transition-all {{ request()->routeIs('appearance.edit') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                <x-heroicon-o-swatch class="size-4" />
                {{ __('Appearance') }}
            </a>
        </nav>
    </div>

    <!-- Mobile Separator -->
    <div class="w-full h-px bg-zinc-200 dark:border-zinc-800 md:hidden my-6"></div>

    <!-- Content Area -->
    <div class="flex-1 self-stretch">
        <div class="mb-8">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">{{ $heading ?? '' }}</h2>
            <p class="mt-1.5 text-sm text-zinc-500 dark:text-zinc-400 font-medium">{{ $subheading ?? '' }}</p>
        </div>

        <div class="w-full max-w-xl">
            {{ $slot }}
        </div>
    </div>
</div>
