<div class="w-full relative overflow-hidden bg-white/60 p-6 sm:p-8 rounded-3xl dark:bg-zinc-800/60 dark:text-white backdrop-blur-xl border border-white/30 dark:border-white/10 shadow-sm animate-fade-in-up"
    style="animation-delay: 0.1s">
    <!-- Decorative subtle glows inside layout -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10">
        {{ $slot }}
    </div>
</div>
