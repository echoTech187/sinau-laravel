@props(['title' => ''])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
    sidebarOpen: false,
    sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
    tooltipText: '',
    tooltipY: 0,
    showTooltip(e, text) {
        if (!this.sidebarCollapsed) return;
        this.tooltipText = text;
        const rect = e.currentTarget.getBoundingClientRect();
        this.tooltipY = rect.top + (rect.height / 2) - 14;
    },
    hideTooltip() {
        this.tooltipText = '';
    }
}" x-init="$watch('sidebarCollapsed', val => { localStorage.setItem('sidebarCollapsed', val); if (!val) hideTooltip(); })"
    @scroll.window="hideTooltip()">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-zinc-100 dark:bg-zinc-950 font-sans antialiased text-zinc-900 dark:text-zinc-100">
    <!-- Dynamic Fixed Tooltip for Mini Sidebar -->
    <div x-show="tooltipText !== '' && sidebarCollapsed" style="display: none;"
        class="fixed z-100 left-18 px-2.5 py-1.5 bg-zinc-800 dark:bg-zinc-700 text-white text-[11px] font-semibold tracking-wide rounded-lg shadow-lg pointer-events-none whitespace-nowrap"
        :style="`top: ${tooltipY}px;`" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-x-[-10px]" x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-[-10px]">
        <span x-text="tooltipText"></span>
        <!-- Tooltip Arrow -->
        <div
            class="absolute top-1/2 -left-1 -mt-1 border-4 border-transparent border-r-zinc-800 dark:border-r-zinc-700">
        </div>
    </div>

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-zinc-900/50 backdrop-blur-sm lg:hidden" style="display: none;"></div>

    <!-- Sidebar Container -->
    <aside
        :class="{
            'translate-x-0 shadow-2xl z-50': sidebarOpen,
            '-translate-x-full lg:translate-x-0 lg:z-40': !sidebarOpen,
            'w-20': sidebarCollapsed,
            'w-72': !sidebarCollapsed
        }"
        class="fixed inset-y-0 left-0 bg-white dark:bg-zinc-950 border-e border-zinc-200 dark:border-zinc-800 transition-all duration-300 ease-in-out flex flex-col h-screen z-50">

        <!-- Sidebar Header -->
        <div class="flex items-center h-20 shrink-0 border-b border-zinc-100 dark:border-zinc-800/50 overflow-hidden"
            :class="sidebarCollapsed ? 'justify-center px-0' : 'justify-between px-6'">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2" wire:navigate>
                <div class="shrink-0 flex items-center justify-center p-1.5 rounded-xl bg-blue-600 text-white shadow-sm"
                    :class="sidebarCollapsed ? 'size-10' : 'size-8'">
                    <x-heroicon-s-code-bracket class="size-5" />
                </div>
                <!-- Logo Text matching user graphic -->
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.300ms
                    class="text-[1.35rem] font-bold tracking-tight text-zinc-900 dark:text-white whitespace-nowrap">
                    Sinau<span class="text-blue-500 font-black">
                        <DEV />
                    </span>
                </span>
            </a>
            <button @click="sidebarOpen = false" x-show="!sidebarCollapsed"
                class="lg:hidden p-2 text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-900 rounded-xl transition-colors shrink-0">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>

        <!-- Navigation Scroll Area & User Menu Container -->
        <div class="flex flex-col flex-1 overflow-hidden w-full">
            <!-- Navigation Scroll Area -->
            <div class="flex-1 overflow-y-auto overflow-x-hidden custom-scrollbar w-full">
                <nav class="py-6 space-y-8" :class="sidebarCollapsed ? 'px-2' : 'px-4'">
                    @php
                        $rootMenus = \App\Models\Menus::with([
                            'children' => function ($q) {
                                $q->where('is_active', true)->orderBy('order');
                            },
                            'permission',
                        ])
                            ->whereNull('parent_id')
                            ->where('is_active', true)
                            ->orderBy('order')
                            ->get();
                    @endphp

                    @foreach ($rootMenus as $root)
                        @php
                            $canViewRoot = !$root->permission_id || auth()->user()->can($root->permission->slug);
                            if (!$canViewRoot) {
                                continue;
                            }

                            $rootIcon = str_replace(['heroicon-o-', 'heroicon-s-'], '', $root->icon ?: 'circle-stack');
                            $isActiveRoot = $root->route && request()->routeIs($root->route);
                        @endphp

                        @if ($root->children->isNotEmpty())
                            @php
                                $visibleChildren = $root->children->filter(function ($child) {
                                    return !$child->permission_id || auth()->user()->can($child->permission->slug);
                                });
                            @endphp

                            @if ($visibleChildren->isNotEmpty())
                                <div class="space-y-1">
                                    <h3 x-show="!sidebarCollapsed" x-transition.opacity
                                        class="px-3 text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-400 dark:text-zinc-500 whitespace-nowrap overflow-hidden">
                                        {{ __($root->name) }}
                                    </h3>
                                    <div x-show="sidebarCollapsed" class="flex justify-center mb-1"
                                        style="display: none;">
                                        <div class="h-0.5 w-4 bg-zinc-200 dark:bg-zinc-800 rounded-full"></div>
                                    </div>
                                    <div class="space-y-1 mt-3">
                                        @foreach ($visibleChildren as $child)
                                            @php
                                                $childIcon = str_replace(
                                                    ['heroicon-o-', 'heroicon-s-'],
                                                    '',
                                                    $child->icon ?: 'document',
                                                );
                                                $isActive = $child->route && request()->routeIs($child->route);
                                                $iconComponent = 'heroicon-o-' . $childIcon;
                                            @endphp
                                            <a href="{{ $child->route && \Illuminate\Support\Facades\Route::has($child->route) ? route($child->route) : '#' }}"
                                                wire:navigate
                                                @mouseenter="showTooltip($event, '{{ __($child->name) }}')"
                                                @mouseleave="hideTooltip()"
                                                :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3 py-2'"
                                                class="flex items-center text-sm font-medium rounded-xl transition-all duration-200 group relative
                                               {{ $isActive
                                                   ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400'
                                                   : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-zinc-950 dark:hover:text-zinc-100' }}">
                                                @if ($childIcon === 'document')
                                                    <x-heroicon-o-document-text
                                                        class="w-5 h-5 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" />
                                                @elseif($childIcon === 'shield-check')
                                                    <x-heroicon-o-shield-check
                                                        class="w-5 h-5 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" />
                                                @elseif($childIcon === 'user-group')
                                                    <x-heroicon-o-user-group
                                                        class="w-5 h-5 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" />
                                                @elseif($childIcon === 'clock')
                                                    <x-heroicon-o-clock
                                                        class="w-5 h-5 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" />
                                                @elseif($childIcon === 'inbox-arrow-down')
                                                    <x-heroicon-o-inbox-arrow-down
                                                        class="w-5 h-5 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" />
                                                @elseif($childIcon === 'archive-box')
                                                    <x-heroicon-o-archive-box
                                                        class="w-5 h-5 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" />
                                                @elseif($childIcon === 'tag')
                                                    <x-heroicon-o-tag
                                                        class="w-5 h-5 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" />
                                                @elseif($childIcon === 'truck')
                                                    <x-heroicon-o-truck
                                                        class="w-5 h-5 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" />
                                                @else
                                                    <x-heroicon-o-document-text
                                                        class="w-5 h-5 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" />
                                                @endif
                                                <span x-show="!sidebarCollapsed" class="whitespace-nowrap"
                                                    x-transition.opacity>{{ __($child->name) }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @else
                            <a href="{{ $root->route && \Illuminate\Support\Facades\Route::has($root->route) ? route($root->route) : '#' }}"
                                wire:navigate @mouseenter="showTooltip($event, '{{ __($root->name) }}')"
                                @mouseleave="hideTooltip()"
                                :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3 py-2'"
                                class="flex items-center text-sm font-medium rounded-xl transition-all duration-200 group relative
                                               {{ $isActiveRoot
                                                   ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400'
                                                   : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-zinc-950 dark:hover:text-zinc-100' }}">
                                @if ($rootIcon === 'home')
                                    <x-heroicon-o-home
                                        class="w-5 h-5 {{ $isActiveRoot ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" />
                                @elseif($rootIcon === 'key')
                                    <x-heroicon-o-key
                                        class="w-5 h-5 {{ $isActiveRoot ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" />
                                @elseif($rootIcon === 'cube')
                                    <x-heroicon-o-cube
                                        class="w-5 h-5 {{ $isActiveRoot ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" />
                                @elseif($rootIcon === 'cog-6-tooth')
                                    <x-heroicon-o-cog-6-tooth
                                        class="w-5 h-5 {{ $isActiveRoot ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" />
                                @elseif($rootIcon === 'adjustments-horizontal')
                                    <x-heroicon-o-adjustments-horizontal
                                        class="w-5 h-5 {{ $isActiveRoot ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" />
                                @else
                                    <x-heroicon-o-circle-stack
                                        class="w-5 h-5 {{ $isActiveRoot ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" />
                                @endif
                                <span x-show="!sidebarCollapsed" class="whitespace-nowrap"
                                    x-transition.opacity>{{ __($root->name) }}</span>
                            </a>
                        @endif
                    @endforeach
                </nav>
            </div>

            <!-- User Menu Area -->
            <div
                class="p-4 shrink-0 border-t border-zinc-100 dark:border-zinc-800/50 flex flex-col gap-2 transition-all duration-300">
                <!-- Expand/Collapse Toggle Button -->
                <button @click="sidebarCollapsed = !sidebarCollapsed" title="Toggle Sidebar"
                    class="hidden lg:flex items-center justify-center w-full py-2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800/50 rounded-xl transition-colors">
                    <x-heroicon-o-bars-3-center-left class="size-5" x-show="!sidebarCollapsed" />
                    <x-heroicon-o-bars-3 class="size-5" x-show="sidebarCollapsed" style="display: none;" />
                </button>
                <div x-show="!sidebarCollapsed" x-transition.opacity.duration.300ms>
                    <x-desktop-user-menu />
                </div>
                <div x-show="sidebarCollapsed" class="flex justify-center" x-transition.opacity.duration.300ms
                    style="display: none;">
                    <div class="size-10 rounded-xl bg-linear-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-sm font-bold shadow-sm cursor-pointer hover:opacity-90 ring-2 ring-transparent transition-all"
                        title="{{ auth()->user()->name }}">
                        {{ auth()->user()->initials() ?? 'US' }}
                    </div>
                </div>


            </div>
        </div>
    </aside>

    <!-- Content Area -->
    <div :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-72'"
        class="transition-all duration-300 flex flex-col min-h-screen">
        <!-- Dashboard Header / Topbar -->
        <header
            class="h-20 shrink-0 flex items-center justify-between px-4 lg:px-8 bg-white/70 dark:bg-zinc-950/70 backdrop-blur-xl sticky top-0 z-30 border-b border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true"
                    class="p-2 text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-900 rounded-xl lg:hidden">
                    <x-heroicon-o-bars-3 class="w-6 h-6" />
                </button>
                <div class="hidden lg:flex items-center text-sm font-medium text-zinc-500 dark:text-zinc-400">
                    <a href="{{ route('dashboard') }}"
                        class="hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">
                        Dashboard
                    </a>
                    @if (isset($title) && $title !== 'Dashboard' && $title !== '')
                        <x-heroicon-s-chevron-right class="w-4 h-4 mx-2 text-zinc-400 dark:text-zinc-600 shrink-0" />
                        <span class="text-zinc-900 dark:text-zinc-100 font-semibold">{{ $title }}</span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Quick Actions or Search can go here -->
                <div
                    class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-zinc-100 dark:bg-zinc-900 text-[10px] font-bold text-zinc-500 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-800">
                    <span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    SYSTEM ONLINE
                </div>
            </div>
        </header>

        <!-- Main Slot -->
        <main class="flex-1 focus:outline-none p-4 lg:p-8">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #1e1e1e;
        }
    </style>
</body>

</html>
