@props([
    'role' => null,
])

<div x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false"
    class="rbac-card group relative overflow-hidden rounded-2xl border border-white/20 dark:border-white/10
           bg-white/70 dark:bg-zinc-800/60
           backdrop-blur-xl
           shadow-[0_4px_24px_-4px_rgba(0,0,0,0.08)] dark:shadow-[0_4px_24px_-4px_rgba(0,0,0,0.4)]
           hover:shadow-[0_12px_40px_-8px_rgba(59,130,246,0.25)] dark:hover:shadow-[0_12px_40px_-8px_rgba(59,130,246,0.3)]
           hover:-translate-y-1 hover:border-blue-200/60 dark:hover:border-blue-500/30
           transition-all duration-300 ease-out
           p-5 flex flex-col gap-4
           animate-fade-in-up">

    {{-- Glow blob (background gradient decoration) --}}
    <div
        class="pointer-events-none absolute -top-10 -right-10 w-32 h-32 rounded-full
                bg-blue-400/10 dark:bg-blue-500/10
                group-hover:bg-blue-400/20 dark:group-hover:bg-blue-500/20
                blur-2xl transition-all duration-500">
    </div>
    <div
        class="pointer-events-none absolute -bottom-8 -left-8 w-24 h-24 rounded-full
                bg-indigo-400/10 dark:bg-indigo-500/10
                group-hover:bg-indigo-400/20 dark:group-hover:bg-indigo-500/20
                blur-2xl transition-all duration-500 delay-75">
    </div>

    {{-- Status Badge --}}
    <div class="absolute top-4 right-4 z-10">
        <span
            class="badge badge-xs font-semibold
            {{ $role->is_active
                ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-700/50'
                : 'bg-zinc-100 dark:bg-zinc-700/50 text-zinc-500 dark:text-zinc-400 border-zinc-200 dark:border-zinc-600/50' }}">
            {{ $role->is_active ? '● Aktif' : '○ Nonaktif' }}
        </span>
    </div>

    {{-- Header --}}
    <div class="flex items-start gap-3 pr-16 relative z-10">
        <div
            class="p-2.5 rounded-xl shrink-0
                    bg-linear-to-br from-blue-500 to-indigo-600
                    shadow-[0_4px_12px_rgba(59,130,246,0.4)]
                    group-hover:shadow-[0_6px_20px_rgba(59,130,246,0.5)]
                    group-hover:scale-110 transition-all duration-300">
            <x-heroicon-o-user-group class="size-5 text-white" />
        </div>
        <div>
            <h3 class="font-bold text-base text-zinc-900 dark:text-zinc-50 leading-tight tracking-tight">
                {{ $role->role }}
            </h3>
            <p class="text-xs text-zinc-400 dark:text-zinc-500 font-mono mt-0.5">
                {{ $role->slug ?? \Illuminate\Support\Str::slug($role->role) }}
            </p>
        </div>
    </div>

    {{-- Description --}}
    <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed line-clamp-2 relative z-10">
        {{ $role->description ?? 'Tidak ada deskripsi untuk peran ini.' }}
    </p>

    {{-- Divider --}}
    <div class="h-px bg-linear-to-r from-transparent via-zinc-200 dark:via-zinc-600/50 to-transparent relative z-10">
    </div>

    {{-- Stats --}}
    <div class="flex items-center gap-5 relative z-10">
        <div class="flex flex-col items-center gap-0.5">
            <span
                class="text-xl font-bold text-zinc-800 dark:text-zinc-100 leading-none">{{ $role->users_count }}</span>
            <span class="text-xs text-zinc-400 dark:text-zinc-500 flex items-center gap-1">
                <x-heroicon-o-users class="size-3" />Anggota
            </span>
        </div>
        @if (isset($role->permissions_count))
            <div class="w-px h-8 bg-zinc-200 dark:bg-zinc-700"></div>
            <div class="flex flex-col items-center gap-0.5">
                <span
                    class="text-xl font-bold text-zinc-800 dark:text-zinc-100 leading-none">{{ $role->permissions_count }}</span>
                <span class="text-xs text-zinc-400 dark:text-zinc-500 flex items-center gap-1">
                    <x-heroicon-o-shield-check class="size-3" />Izin
                </span>
            </div>
        @endif
    </div>

    {{-- Members Avatar Stack --}}
    <div class="flex items-center gap-2 relative z-10">
        @if ($role->users->isNotEmpty())
            <div class="flex items-center -space-x-2">
                @foreach ($role->users->take(5) as $user)
                    <img class="rounded-full w-7 h-7 object-cover ring-2 ring-white dark:ring-zinc-800/80
                                hover:scale-110 hover:z-10 transition-transform duration-200 cursor-pointer"
                        src="{{ $user->getAvatarUrlAttribute() }}" alt="{{ $user->name }}"
                        title="{{ $user->name }}" />
                @endforeach
                @if ($role->users_count > 5)
                    <div
                        class="rounded-full w-7 h-7 bg-blue-100 dark:bg-blue-900/50
                                ring-2 ring-white dark:ring-zinc-800/80
                                flex items-center justify-center">
                        <span
                            class="text-xs font-bold text-blue-600 dark:text-blue-400">+{{ $role->users_count - 5 }}</span>
                    </div>
                @endif
            </div>
            <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $role->users_count }} anggota</span>
        @else
            <span class="text-xs text-zinc-400 dark:text-zinc-500 italic flex items-center gap-1">
                <x-heroicon-o-user-plus class="size-3.5" />
                Belum ada anggota
            </span>
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-2 relative z-10 mt-auto pt-1">
        <button
            class="btn btn-sm flex-1 relative overflow-hidden
                   bg-linear-to-r from-blue-500 to-indigo-600
                   hover:from-blue-600 hover:to-indigo-700
                   text-white border-0
                   shadow-[0_2px_10px_rgba(59,130,246,0.35)]
                   hover:shadow-[0_4px_16px_rgba(59,130,246,0.5)]
                   transition-all duration-200
                   active:scale-95"
            wire:click="editRole({{ $role->id }})">
            <x-heroicon-o-shield-check class="size-4" />
            <span class="hidden sm:inline">Kelola Izin</span>
            <span class="sm:hidden">Izin</span>
        </button>
        <button
            class="btn btn-sm flex-1
                   bg-white/50 dark:bg-zinc-700/50
                   hover:bg-white/80 dark:hover:bg-zinc-700/80
                   border border-zinc-200/80 dark:border-zinc-600/50
                   text-zinc-700 dark:text-zinc-300
                   hover:text-zinc-900 dark:hover:text-zinc-100
                   backdrop-blur-sm
                   transition-all duration-200
                   active:scale-95"
            wire:click="manageTeam({{ $role->id }})">
            <x-heroicon-o-user-plus class="size-4" />
            <span class="hidden sm:inline">Anggota</span>
            <span class="sm:hidden">Tim</span>
        </button>
    </div>
</div>

{{-- Keyframes + inline styles --}}
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(16px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.4s ease-out both;
    }

    .rbac-card:nth-child(1) {
        animation-delay: 0.05s;
    }

    .rbac-card:nth-child(2) {
        animation-delay: 0.1s;
    }

    .rbac-card:nth-child(3) {
        animation-delay: 0.15s;
    }

    .rbac-card:nth-child(4) {
        animation-delay: 0.2s;
    }

    .rbac-card:nth-child(5) {
        animation-delay: 0.25s;
    }

    .rbac-card:nth-child(6) {
        animation-delay: 0.3s;
    }
</style>
