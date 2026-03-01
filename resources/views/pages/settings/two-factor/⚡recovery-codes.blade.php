<?php

use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component {
    #[Locked]
    public array $recoveryCodes = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->loadRecoveryCodes();
    }

    /**
     * Generate new recovery codes for the user.
     */
    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generateNewRecoveryCodes): void
    {
        $generateNewRecoveryCodes(auth()->user());

        $this->loadRecoveryCodes();
    }

    /**
     * Load the recovery codes for the user.
     */
    private function loadRecoveryCodes(): void
    {
        $user = auth()->user();

        if ($user->hasEnabledTwoFactorAuthentication() && $user->two_factor_recovery_codes) {
            try {
                $this->recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
            } catch (Exception) {
                $this->addError('recoveryCodes', 'Failed to load recovery codes');

                $this->recoveryCodes = [];
            }
        }
    }
}; ?>

<div class="py-6 space-y-6 border border-zinc-200 dark:border-zinc-800 shadow-sm rounded-3xl bg-white dark:bg-zinc-900/50"
    wire:cloak x-data="{ showRecoveryCodes: false }">
    <div class="px-6 sm:px-8 space-y-2">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl">
                <x-heroicon-o-lock-closed class="size-5 text-indigo-600 dark:text-indigo-400" />
            </div>
            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ __('2FA Recovery Codes') }}</h3>
        </div>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed sm:pl-12">
            {{ __('Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.') }}
        </p>
    </div>

    <div class="px-6 sm:px-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <button type="button" x-show="!showRecoveryCodes" @click="showRecoveryCodes = true;"
                class="btn btn-primary sm:w-auto px-6 rounded-xl shadow-lg shadow-indigo-500/30 flex items-center justify-center gap-2 font-bold transition-all hover:-translate-y-0.5"
                aria-expanded="false" aria-controls="recovery-codes-section">
                <x-heroicon-o-eye class="size-5" />
                {{ __('View Recovery Codes') }}
            </button>

            <button type="button" x-show="showRecoveryCodes" style="display: none;" @click="showRecoveryCodes = false"
                class="btn btn-outline border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 sm:w-auto px-6 rounded-xl flex items-center justify-center gap-2 font-bold transition-all"
                aria-expanded="true" aria-controls="recovery-codes-section">
                <x-heroicon-o-eye-slash class="size-5" />
                {{ __('Hide Recovery Codes') }}
            </button>

            @if (filled($recoveryCodes))
                <button type="button" x-show="showRecoveryCodes" style="display: none;"
                    wire:click="regenerateRecoveryCodes"
                    class="btn btn-ghost text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 sm:w-auto px-6 rounded-xl flex items-center justify-center gap-2 font-bold transition-all sm:ml-auto">
                    <x-heroicon-o-arrow-path class="size-5" />
                    {{ __('Regenerate Codes') }}
                </button>
            @endif
        </div>

        <div x-show="showRecoveryCodes" style="display: none;" x-transition id="recovery-codes-section"
            class="relative overflow-hidden mt-6" x-bind:aria-hidden="!showRecoveryCodes">
            <div class="space-y-4">
                @error('recoveryCodes')
                    <div
                        class="p-4 bg-red-50 dark:bg-red-500/10 rounded-2xl flex gap-3 text-red-800 dark:text-red-200 text-sm font-bold border border-red-200 dark:border-red-500/20">
                        <x-heroicon-o-x-circle class="w-5 h-5 shrink-0" />
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                @if (filled($recoveryCodes))
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-6 font-mono text-sm rounded-2xl bg-zinc-50 dark:bg-zinc-950/50 border border-zinc-200 dark:border-zinc-800"
                        role="list" aria-label="{{ __('Recovery codes') }}">
                        @foreach ($recoveryCodes as $code)
                            <div role="listitem"
                                class="select-text py-2 px-3 bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 rounded-xl text-center font-bold tracking-[0.1em] text-zinc-700 dark:text-zinc-300 shadow-sm"
                                wire:loading.class="opacity-50 animate-pulse">
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-500 leading-relaxed max-w-xl">
                        {{ __('Each recovery code can be used once to access your account and will be removed after use. If you need more, click Regenerate Codes above.') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
