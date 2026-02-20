@props([
    'user' => null,
    'roles' => null,
])

<form wire:submit="save" class="my-6 w-full space-y-6">

    <flux:field>
        <flux:label badge="Required" class="mb-2!">Nama Lengkap</flux:label>

        <flux:input wire:model="name" type="text" required autofocus :placeholder="__('Nama Lengkap')" />
        <flux:error name="name">
            {{ $errors->first('name') }}
        </flux:error>
    </flux:field>
    <flux:field>
        <flux:label badge="Required" class="mb-2!">Email</flux:label>

        <flux:input wire:model="email" type="email" required :placeholder="__('email@example.com')" />
        <flux:error name="email">
            {{ $errors->first('email') }}
        </flux:error>
    </flux:field>
    <flux:field>
        <flux:label class="mb-2!" badge="Required">Hak Akses</flux:label>

        <flux:input.group class="items-end!">
            <flux:select searchable wire:model="role_id" required :placeholder="__('Pilih Hak Akses')">
                @foreach ($roles as $role)
                    <flux:select.option value="{{ $role->id }}">{{ $role->role }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:modal.trigger name="create-role">
                <flux:button icon="plus" class="mb-0">Tambah</flux:button>
            </flux:modal.trigger>
        </flux:input.group>
        <flux:error name="role_id">
            {{ $errors->first('role_id') }}
        </flux:error>
    </flux:field>
    <flux:spacer />
    <flux:heading size="lg" class="mb-0!"> {{ $user->id ? 'Ganti Sandi' : 'Buat Sandi Baru' }}</flux:heading>
    <flux:text class="mb-4">
        {{ $user->id ? 'Kosongkan jika tidak ingin mengganti sandi.' : 'Sandi minimal 8 karakter.' }}</flux:text>
    <flux:separator />
    <flux:spacer />
    <flux:field>
        <flux:label class="mb-2!" badge="{{ $user->id ? 'Optional' : 'Required' }}">Sandi</flux:label>
        <flux:input wire:model="password" type="password" autocomplete="new-password" :placeholder="__('Sandi Baru')"
            :required="!$user->id" viewable />
        <flux:error name="password">
            {{ $errors->first('password') }}
        </flux:error>
    </flux:field>
    <flux:field>
        <flux:label class="mb-2!" badge="{{ $user->id ? 'Optional' : 'Required' }}">Konfirmasi Sandi</flux:label>
        <flux:input wire:model="confirm_password" type="password" autocomplete="new-password" :required="!$user->id"
            :placeholder="__('Konfirmasi Sandi')" viewable />
        <flux:error name="password_confirmation">
            {{ $errors->first('password_confirmation') }}
        </flux:error>
    </flux:field>
    <div class="flex items-center gap-4 w-full my-6">
        <div class="flex items-center justify-end w-full">
            <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                {{ __('Save') }}
            </flux:button>
        </div>
    </div>
</form>
<flux:modal name="create-role" class="md:w-96">
    <form wire:submit="createRole" class="space-y-6">
        <div>
            <flux:heading size="lg">Tambah Hak Akses Baru</flux:heading>
            <flux:text class="mt-2">Masukan Nama Hak Akses Baru.</flux:text>
        </div>
        <flux:input wire:model="role" label="Hak Akses" type="text" placeholder="e.g. 'Admin'" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Tambah</flux:button>
        </div>
    </form>
</flux:modal>
