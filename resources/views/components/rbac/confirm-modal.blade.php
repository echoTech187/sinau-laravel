@props([
    'title' => 'Konfirmasi Hapus',
    'message' => 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
    'confirmAction' => '',
    'cancelAction' => '',
    'type' => 'danger', // danger, warning, info
])

<dialog open class="modal modal-open z-[100]">
    <div
        class="modal-box bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 max-w-sm w-full p-6 text-center animate-fade-in-up">
        {{-- Icon --}}
        <div
            class="mx-auto flex h-14 w-14 items-center justify-center rounded-full 
                    {{ $type === 'danger' ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : '' }}
                    {{ $type === 'warning' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : '' }}
                    mb-4">
            @if ($type === 'danger')
                <x-heroicon-o-trash class="h-8 w-8" />
            @elseif($type === 'warning')
                <x-heroicon-o-exclamation-triangle class="h-8 w-8" />
            @endif
        </div>

        <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 mb-2">{{ $title }}</h3>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-8 leading-relaxed">
            {{ $message }}
        </p>

        <div class="flex items-center gap-3">
            <button type="button" wire:click="{{ $cancelAction }}"
                class="btn btn-ghost flex-1 dark:text-zinc-400 dark:hover:text-zinc-200 font-bold border-zinc-200 dark:border-zinc-700">
                Batal
            </button>
            <button type="button" wire:click="{{ $confirmAction }}"
                class="btn {{ $type === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-amber-600 hover:bg-amber-700' }} text-white border-0 flex-1 shadow-lg font-bold">
                Ya, Lanjutkan
            </button>
        </div>
    </div>
    <div class="modal-backdrop bg-black/40 dark:bg-black/60 backdrop-blur-sm" wire:click="{{ $cancelAction }}"></div>
</dialog>
