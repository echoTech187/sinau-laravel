<?php

use App\Concerns\PasswordValidationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

new class extends Component {
    use PasswordValidationRules;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => $this->currentPasswordRules(),
                'password' => $this->passwordRules(),
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Password Settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Update password')" :subheading="__('Ensure your account is using a long, random password to stay secure')">
        <form method="POST" wire:submit="updatePassword" class="mt-6 space-y-6">
            <div class="space-y-2">
                <label for="current_password"
                    class="block text-sm font-bold text-zinc-700 dark:text-zinc-300">{{ __('Current password') }}</label>
                <input id="current_password" wire:model="current_password" type="password" required
                    autocomplete="current-password"
                    class="input input-bordered w-full bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 rounded-xl focus:ring-2 focus:ring-indigo-500/20" />
                @error('current_password')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="password"
                    class="block text-sm font-bold text-zinc-700 dark:text-zinc-300">{{ __('New password') }}</label>
                <input id="password" wire:model="password" type="password" required autocomplete="new-password"
                    class="input input-bordered w-full bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 rounded-xl focus:ring-2 focus:ring-indigo-500/20" />
                @error('password')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="password_confirmation"
                    class="block text-sm font-bold text-zinc-700 dark:text-zinc-300">{{ __('Confirm Password') }}</label>
                <input id="password_confirmation" wire:model="password_confirmation" type="password" required
                    autocomplete="new-password"
                    class="input input-bordered w-full bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 rounded-xl focus:ring-2 focus:ring-indigo-500/20" />
                @error('password_confirmation')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit" class="btn btn-primary sm:w-auto px-8 rounded-xl shadow-lg shadow-indigo-500/30"
                    data-test="update-password-button">
                    {{ __('Save') }}
                </button>

                <x-action-message class="me-3" on="password-updated">
                    <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                        <x-heroicon-o-check-circle class="w-5 h-5" />
                        {{ __('Saved.') }}
                    </span>
                </x-action-message>
            </div>
        </form>
    </x-pages::settings.layout>
</section>

<x-slot:title>{{ __('Update Password') }}</x-slot:title>
