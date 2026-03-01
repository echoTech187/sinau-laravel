<?php

use App\Concerns\PasswordValidationRules;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    use PasswordValidationRules;

    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => $this->currentPasswordRules(),
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ __('Delete Account') }}</h2>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Delete your account and all of its resources') }}
        </p>
    </div>

    <div x-data="{ confirmDeletion: {{ $errors->isNotEmpty() ? 'true' : 'false' }} }" @keydown.escape.window="confirmDeletion = false">
        <button type="button" @click="confirmDeletion = true"
            class="btn btn-error px-6 sm:w-auto rounded-xl shadow-lg shadow-red-500/30 font-bold"
            data-test="delete-user-button">
            {{ __('Delete account') }}
        </button>

        <div x-show="confirmDeletion" style="display: none;"
            class="fixed inset-0 z-100 flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div x-show="confirmDeletion" x-transition.opacity class="fixed inset-0 bg-zinc-900/60 backdrop-blur-sm"
                @click="confirmDeletion = false"></div>

            <!-- Panel -->
            <div x-show="confirmDeletion" x-transition
                class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-2xl p-6 sm:p-8 overflow-hidden text-left align-middle transition-all">

                <form method="POST" wire:submit="deleteUser" class="space-y-6">
                    <div>
                        <div class="p-3 bg-red-50 dark:bg-red-500/10 rounded-2xl w-fit mb-4">
                            <x-heroicon-o-exclamation-triangle class="w-8 h-8 text-red-500" />
                        </div>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Are you sure you want to delete your account?') }}</h2>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label for="delete_password"
                            class="block text-sm font-bold text-zinc-700 dark:text-zinc-300">{{ __('Password') }}</label>
                        <input id="delete_password" wire:model="password" type="password" required autofocus
                            class="input input-bordered w-full bg-zinc-50 dark:bg-zinc-950/50 border-zinc-200 dark:border-zinc-800 rounded-xl focus:ring-2 focus:ring-red-500/20"
                            placeholder="{{ __('Password') }}" />
                        @error('password')
                            <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <button type="button" @click="confirmDeletion = false"
                            class="btn btn-ghost px-6 rounded-xl font-bold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                            {{ __('Cancel') }}
                        </button>

                        <button type="submit"
                            class="btn btn-error px-6 rounded-xl shadow-lg shadow-red-500/30 font-bold"
                            data-test="confirm-delete-user-button">
                            {{ __('Delete account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
