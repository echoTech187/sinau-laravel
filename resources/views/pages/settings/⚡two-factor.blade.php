<?php

use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

new class extends Component {
    #[Locked]
    public bool $twoFactorEnabled;

    #[Locked]
    public bool $requiresConfirmation;

    #[Locked]
    public string $qrCodeSvg = '';

    #[Locked]
    public string $manualSetupKey = '';

    public bool $showModal = false;

    public bool $showVerificationStep = false;

    #[Validate('required|string|size:6', onUpdate: false)]
    public string $code = '';

    /**
     * Mount the component.
     */
    public function mount(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        abort_unless(Features::enabled(Features::twoFactorAuthentication()), Response::HTTP_FORBIDDEN);

        if (Fortify::confirmsTwoFactorAuthentication() && is_null(auth()->user()->two_factor_confirmed_at)) {
            $disableTwoFactorAuthentication(auth()->user());
        }

        $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        $this->requiresConfirmation = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
    }

    /**
     * Enable two-factor authentication for the user.
     */
    public function enable(EnableTwoFactorAuthentication $enableTwoFactorAuthentication): void
    {
        $enableTwoFactorAuthentication(auth()->user());

        if (!$this->requiresConfirmation) {
            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        }

        $this->loadSetupData();

        $this->showModal = true;
    }

    /**
     * Load the two-factor authentication setup data for the user.
     */
    private function loadSetupData(): void
    {
        $user = auth()->user();

        try {
            $this->qrCodeSvg = $user?->twoFactorQrCodeSvg();
            $this->manualSetupKey = decrypt($user->two_factor_secret);
        } catch (Exception) {
            $this->addError('setupData', 'Failed to fetch setup data.');

            $this->reset('qrCodeSvg', 'manualSetupKey');
        }
    }

    /**
     * Show the two-factor verification step if necessary.
     */
    public function showVerificationIfNecessary(): void
    {
        if ($this->requiresConfirmation) {
            $this->showVerificationStep = true;

            $this->resetErrorBag();

            return;
        }

        $this->closeModal();
    }

    /**
     * Confirm two-factor authentication for the user.
     */
    public function confirmTwoFactor(ConfirmTwoFactorAuthentication $confirmTwoFactorAuthentication): void
    {
        $this->validate();

        $confirmTwoFactorAuthentication(auth()->user(), $this->code);

        $this->closeModal();

        $this->twoFactorEnabled = true;
    }

    /**
     * Reset two-factor verification state.
     */
    public function resetVerification(): void
    {
        $this->reset('code', 'showVerificationStep');

        $this->resetErrorBag();
    }

    /**
     * Disable two-factor authentication for the user.
     */
    public function disable(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        $disableTwoFactorAuthentication(auth()->user());

        $this->twoFactorEnabled = false;
    }

    /**
     * Close the two-factor authentication modal.
     */
    public function closeModal(): void
    {
        $this->reset('code', 'manualSetupKey', 'qrCodeSvg', 'showModal', 'showVerificationStep');

        $this->resetErrorBag();

        if (!$this->requiresConfirmation) {
            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        }
    }

    /**
     * Get the current modal configuration state.
     */
    public function getModalConfigProperty(): array
    {
        if ($this->twoFactorEnabled) {
            return [
                'title' => __('Two-Factor Authentication Enabled'),
                'description' => __('Two-factor authentication is now enabled. Scan the QR code or enter the setup key in your authenticator app.'),
                'buttonText' => __('Close'),
            ];
        }

        if ($this->showVerificationStep) {
            return [
                'title' => __('Verify Authentication Code'),
                'description' => __('Enter the 6-digit code from your authenticator app.'),
                'buttonText' => __('Continue'),
            ];
        }

        return [
            'title' => __('Enable Two-Factor Authentication'),
            'description' => __('To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app.'),
            'buttonText' => __('Continue'),
        ];
    }
}; ?>

<section class="w-full">
    <x-pages::settings.layout :heading="__('Two Factor Authentication')" :subheading="__('Manage your two-factor authentication settings')">
        <div class="flex flex-col w-full mx-auto space-y-6 text-sm" wire:cloak>
            @if ($twoFactorEnabled)
                <div
                    class="space-y-4 p-6 bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200 dark:border-zinc-800 rounded-3xl relative overflow-hidden backdrop-blur-xl">
                    <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
                        <x-heroicon-o-shield-check class="w-32 h-32 text-emerald-500" />
                    </div>

                    <div class="flex items-center gap-3">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 font-bold text-xs uppercase tracking-wider border border-emerald-200 dark:border-emerald-500/20 shadow-sm relative z-10">
                            <x-heroicon-o-check-circle class="size-4" />
                            {{ __('Enabled') }}
                        </span>
                    </div>

                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed font-medium relative z-10">
                        {{ __('With two-factor authentication enabled, you will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.') }}
                    </p>

                    <div class="relative z-10">
                        <livewire:pages::settings.two-factor.recovery-codes :$requiresConfirmation />
                    </div>

                    <div class="flex justify-start relative z-10 pt-4">
                        <button type="button" wire:click="disable"
                            class="btn btn-error px-6 rounded-xl shadow-lg shadow-red-500/30 flex items-center gap-2 font-bold text-white transition-all hover:-translate-y-0.5">
                            <x-heroicon-o-shield-exclamation class="size-5" />
                            {{ __('Disable 2FA') }}
                        </button>
                    </div>
                </div>
            @else
                <div
                    class="space-y-4 p-6 bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200 dark:border-zinc-800 rounded-3xl relative overflow-hidden backdrop-blur-xl">
                    <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
                        <x-heroicon-o-shield-exclamation class="w-32 h-32 text-red-500" />
                    </div>

                    <div class="flex items-center gap-3">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400 font-bold text-xs uppercase tracking-wider border border-red-200 dark:border-red-500/20 shadow-sm relative z-10">
                            <x-heroicon-o-x-circle class="size-4" />
                            {{ __('Disabled') }}
                        </span>
                    </div>

                    <p class="text-zinc-500 dark:text-zinc-400 leading-relaxed relative z-10">
                        {{ __('When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.') }}
                    </p>

                    <div class="pt-4 relative z-10">
                        <button type="button" wire:click="enable"
                            class="btn btn-primary px-6 rounded-xl shadow-lg shadow-indigo-500/30 flex items-center gap-2 font-bold transition-all hover:-translate-y-0.5">
                            <x-heroicon-o-shield-check class="size-5" />
                            {{ __('Enable 2FA') }}
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </x-pages::settings.layout>

    <div x-data="{ showModal: @entangle('showModal') }" @keydown.escape.window="showModal = false; $wire.closeModal()">
        <div x-show="showModal" style="display: none;"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-zinc-900/60 backdrop-blur-sm"
                @click="showModal = false; $wire.closeModal()"></div>

            <!-- Panel -->
            <div x-show="showModal" x-transition
                class="relative w-full max-w-md rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-2xl p-6 sm:p-8 overflow-hidden text-left align-middle transition-all">

                <button @click="showModal = false; $wire.closeModal()" type="button"
                    class="absolute top-4 right-4 p-2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 rounded-full transition-colors focus:outline-none">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>

                <div class="space-y-6">
                    <div class="flex flex-col items-center space-y-4">
                        <div
                            class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-full shadow-inner border border-zinc-200 dark:border-zinc-700 shrink-0">
                            <x-heroicon-o-qr-code class="w-8 h-8 text-zinc-600 dark:text-zinc-300" />
                        </div>

                        <div class="space-y-2 text-center">
                            <h2 class="text-xl font-bold text-zinc-900 dark:text-white leading-tight">
                                {{ $this->modalConfig['title'] }}</h2>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-[280px] mx-auto">
                                {{ $this->modalConfig['description'] }}</p>
                        </div>
                    </div>

                    @if ($showVerificationStep)
                        <div class="space-y-6">
                            <div class="flex flex-col items-center space-y-3 justify-center">
                                <label for="otp_code" class="sr-only">OTP Code</label>
                                <input id="otp_code" type="text" wire:model="code" maxlength="6"
                                    class="input input-bordered text-center tracking-[0.5em] text-2xl font-mono w-48 bg-zinc-50 dark:bg-zinc-950/50 border-zinc-300 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20"
                                    placeholder="······" />
                                @error('code')
                                    <span class="text-xs font-bold text-red-500 font-mono">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col-reverse sm:flex-row items-center gap-3 mt-8">
                                <button type="button"
                                    class="btn btn-ghost w-full sm:flex-1 rounded-xl font-bold text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                    wire:click="resetVerification">
                                    {{ __('Back') }}
                                </button>

                                <button type="button"
                                    class="btn btn-primary w-full sm:flex-1 rounded-xl font-bold shadow-lg shadow-indigo-500/30"
                                    wire:click="confirmTwoFactor" x-bind:disabled="$wire.code.length < 6">
                                    {{ __('Confirm') }}
                                </button>
                            </div>
                        </div>
                    @else
                        @error('setupData')
                            <div
                                class="p-3 bg-red-50 dark:bg-red-500/10 rounded-xl flex gap-3 text-red-800 dark:text-red-200 text-sm font-bold border border-red-200 dark:border-red-500/20">
                                <x-heroicon-o-x-circle class="w-5 h-5 shrink-0" />
                                <span>{{ $message }}</span>
                            </div>
                        @enderror

                        <div class="flex justify-center mt-2">
                            <div class="p-3 bg-white rounded-2xl border border-zinc-200 shadow-sm">
                            @empty($qrCodeSvg)
                                <div
                                    class="w-[180px] h-[180px] flex items-center justify-center bg-zinc-50 dark:bg-zinc-100 rounded-xl">
                                    <x-heroicon-o-arrow-path class="w-8 h-8 text-zinc-300 animate-spin" />
                                </div>
                            @else
                                <div
                                    class="w-[180px] h-[180px] flex items-center justify-center [&>svg]:w-full [&>svg]:h-full">
                                    {!! $qrCodeSvg !!}
                                </div>
                            @endempty
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="button" :disabled="$errors->has('setupData')"
                            class="btn btn-primary w-full rounded-xl font-bold shadow-lg shadow-indigo-500/30"
                            wire:click="showVerificationIfNecessary">
                            {{ $this->modalConfig['buttonText'] }}
                        </button>
                    </div>

                    <div class="space-y-4 mt-6">
                        <div class="relative flex items-center justify-center w-full">
                            <div class="absolute inset-0 w-full h-px top-1/2 bg-zinc-200 dark:bg-zinc-800"></div>
                            <span
                                class="relative px-3 text-[10px] font-bold uppercase tracking-widest text-zinc-400 bg-white dark:bg-zinc-900">
                                {{ __('or, enter the code manually') }}
                            </span>
                        </div>

                        <div class="flex items-center" x-data="{
                            copied: false,
                            async copy() {
                                try {
                                    await navigator.clipboard.writeText('{{ $manualSetupKey }}');
                                    this.copied = true;
                                    setTimeout(() => this.copied = false, 1500);
                                } catch (e) {
                                    console.warn('Could not copy to clipboard');
                                }
                            }
                        }">
                            <div
                                class="flex items-stretch w-full border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950/50 rounded-xl overflow-hidden group focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                            @empty($manualSetupKey)
                                <div class="flex items-center justify-center w-full p-3 h-[46px]">
                                    <x-heroicon-o-arrow-path class="w-4 h-4 text-zinc-400 animate-spin" />
                                </div>
                            @else
                                <input type="text" readonly value="{{ $manualSetupKey }}"
                                    class="w-full p-3 bg-transparent outline-none text-zinc-900 dark:text-zinc-100 font-mono text-sm tracking-[0.2em] text-center border-0 focus:ring-0" />

                                <button @click="copy()" type="button"
                                    class="px-4 transition-colors border-l cursor-pointer border-zinc-200 dark:border-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-800 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 focus:outline-none flex items-center justify-center">
                                    <x-heroicon-o-document-duplicate x-show="!copied" class="w-4 h-4" />
                                    <x-heroicon-s-check x-show="copied" style="display: none;"
                                        class="w-4 h-4 text-emerald-500" />
                                </button>
                            @endempty
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</div>
</section>
