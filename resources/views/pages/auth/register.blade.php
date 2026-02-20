<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Buat Akun Baru')" :description="__('Masukan informasi dibawah ini untuk membuat akun baru')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input name="name" :label="__('Nama Lengkap')" :value="old('name')" type="text" required
                autofocus autocomplete="name" :placeholder="__('John Doe')" />

            <!-- Email Address -->
            <flux:input name="email" :label="__('Email')" :value="old('email')" type="email" required
                autocomplete="email" placeholder="email@example.com" />

            <!-- Password -->
            <flux:input name="password" :label="__('Sandi')" type="password" required autocomplete="new-password"
                :placeholder="__('Sandi')" viewable />

            <!-- Confirm Password -->
            <flux:input name="password_confirmation" :label="__('Konfirmasi Sandi')" type="password" required
                autocomplete="new-password" :placeholder="__('Konfirmasi Sandi')" viewable />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Buat Akun dan Masuk') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Sudah punya akun?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Masuk') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
