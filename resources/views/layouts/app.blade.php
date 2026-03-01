<x-layouts::app.sidebar :title="$title ?? ''">
    {{ $slot }}

    {{-- Global Toast Notification --}}
    <div x-data="{
        toasts: [],
        add(e) {
            const id = Date.now();
            const t = { id, type: e.detail.type ?? 'success', message: e.detail.message ?? '', title: e.detail.title ?? '' };
            this.toasts.push(t);
            setTimeout(() => this.remove(id), e.detail.timeout ?? 4000);
        },
        remove(id) { this.toasts = this.toasts.filter(t => t.id !== id); }
    }" x-on:notify.window="add($event)"
        class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 w-80 pointer-events-none" aria-live="polite">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="pointer-events-auto flex items-start gap-3 p-4 rounded-2xl shadow-2xl border backdrop-blur-xl"
                :class="{
                    'bg-emerald-50 dark:bg-emerald-900/80 border-emerald-200 dark:border-emerald-700/60 text-emerald-800 dark:text-emerald-100': toast
                        .type === 'success',
                    'bg-red-50 dark:bg-red-900/80 border-red-200 dark:border-red-700/60 text-red-800 dark:text-red-100': toast
                        .type === 'error',
                    'bg-blue-50 dark:bg-blue-900/80 border-blue-200 dark:border-blue-700/60 text-blue-800 dark:text-blue-100': toast
                        .type === 'info',
                    'bg-amber-50 dark:bg-amber-900/80 border-amber-200 dark:border-amber-700/60 text-amber-800 dark:text-amber-100': toast
                        .type === 'warning',
                }">
                {{-- Icon --}}
                <div class="shrink-0 mt-0.5">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </template>
                </div>
                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p x-show="toast.title" x-text="toast.title" class="text-sm font-semibold leading-tight"></p>
                    <p x-show="toast.message" x-text="toast.message" class="text-sm mt-0.5 opacity-90"></p>
                </div>
                {{-- Close button --}}
                <button @click="remove(toast.id)"
                    class="shrink-0 opacity-50 hover:opacity-100 transition-opacity mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>
</x-layouts::app.sidebar>
