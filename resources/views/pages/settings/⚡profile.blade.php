<?php

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && !Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return !Auth::user() instanceof MustVerifyEmail || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile Settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="space-y-2">
                <label for="name"
                    class="block text-sm font-bold text-zinc-700 dark:text-zinc-300">{{ __('Name') }}</label>
                <input id="name" wire:model="name" type="text" required autofocus autocomplete="name"
                    class="input input-bordered w-full bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 rounded-xl focus:ring-2 focus:ring-indigo-500/20" />
                @error('name')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="email"
                    class="block text-sm font-bold text-zinc-700 dark:text-zinc-300">{{ __('Email') }}</label>
                <input id="email" wire:model="email" type="email" required autocomplete="email"
                    class="input input-bordered w-full bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 rounded-xl focus:ring-2 focus:ring-indigo-500/20" />
                @error('email')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror

                @if ($this->hasUnverifiedEmail)
                    <div
                        class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/50 dark:bg-amber-900/20">
                        <p class="text-sm text-amber-800 dark:text-amber-200">
                            {{ __('Your email address is unverified.') }}

                            <button type="button"
                                class="font-bold underline cursor-pointer hover:text-amber-900 dark:hover:text-amber-100"
                                wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit" class="btn btn-primary sm:w-auto px-8 rounded-xl shadow-lg shadow-indigo-500/30"
                    data-test="update-profile-button">
                    {{ __('Save') }}
                </button>

                <x-action-message class="me-3" on="profile-updated">
                    <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                        <x-heroicon-o-check-circle class="w-5 h-5" />
                        {{ __('Saved.') }}
                    </span>
                </x-action-message>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>

<x-slot:title>{{ __('Profile Information') }}</x-slot:title>
