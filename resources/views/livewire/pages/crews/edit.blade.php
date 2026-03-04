<div class="relative min-h-full">
    {{-- Background Decorative Elements --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-150 h-150 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-1/4 w-125 h-125 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8">
        {{-- Header Section --}}
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/50 pb-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-linear-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/30">
                            <x-heroicon-o-pencil-square class="w-5 h-5 text-white" />
                        </div>
                        Ubah Data Kru: <span
                            class="text-indigo-600 dark:text-indigo-400 font-black">{{ $crew->name }}</span>
                    </h1>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                        Perbarui informasi detail awak bus operasional.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a wire:navigate href="{{ route('crews.index') }}"
                        class="btn btn-sm bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl transition-all">
                        <x-heroicon-o-arrow-left class="w-4 h-4" />
                        Kembali
                    </a>
                </div>
            </header>
        </div>

        {{-- Form Card --}}
        <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl overflow-hidden shadow-sm animate-fade-in-up"
            style="animation-delay: 0.1s">
            <form wire:submit="saveCrew" class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                {{-- Global Photo Section --}}
                <div
                    class="p-8 bg-linear-to-r from-zinc-50/50 to-transparent dark:from-zinc-800/20 dark:to-transparent">
                    <div class="flex flex-col md:flex-row items-center gap-8">
                        <div class="relative group">
                            <div
                                class="w-40 h-40 rounded-3xl overflow-hidden border-4 border-white dark:border-zinc-800 shadow-xl bg-zinc-100 dark:bg-zinc-950 flex items-center justify-center transition-transform group-hover:scale-[1.02] duration-300">
                                @if ($form->photo)
                                    <img src="{{ $form->photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif ($form->photo_path)
                                    <img src="{{ asset('storage/' . $form->photo_path) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <x-heroicon-o-user class="w-16 h-16 text-zinc-300 dark:text-zinc-700" />
                                @endif

                                <div wire:loading wire:target="form.photo"
                                    class="absolute inset-0 bg-black/40 backdrop-blur-[2px] flex items-center justify-center">
                                    <span class="loading loading-spinner loading-md text-white"></span>
                                </div>
                            </div>

                            <label
                                class="absolute -bottom-3 -right-3 p-3 rounded-2xl bg-white dark:bg-zinc-800 shadow-lg border border-zinc-100 dark:border-zinc-700 cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-all active:scale-95 group-hover:shadow-blue-500/20">
                                <x-heroicon-o-camera class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                <input type="file" wire:model="form.photo" class="hidden" accept="image/*" />
                            </label>
                        </div>

                        <div class="flex-1 text-center md:text-left">
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Foto Profil Awak Bus</h3>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 max-w-sm">Perbarui foto formal kru.
                                Maksimum 2MB (JPG, PNG).</p>
                            @error('form.photo')
                                <span class="text-xs text-red-500 mt-2 block font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="p-8 grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-8">
                    {{-- Left Column: Personal Info --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 mb-2">
                            <div
                                class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
                                <x-heroicon-o-identification class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <h4 class="text-sm font-bold uppercase tracking-wider text-zinc-900 dark:text-white">
                                Identitas & Personal</h4>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="NIK (Nomor Induk Kependudukan)*" wire:model="form.nik"
                                placeholder="Masukkan 16 digit NIK" />
                            <x-form-input label="Nama Lengkap (Sesuai KTP)*" wire:model="form.name"
                                placeholder="Nama Lengkap" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Nomor SIM*" wire:model="form.license_number" placeholder="Nomor SIM" />
                            <x-form-input label="Tanggal Lahir*" wire:model="form.birth_date" type="date" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-select label="Jenis Kelamin*" wire:model="form.gender">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </x-form-select>
                            <x-form-input label="Kota Domisili*" wire:model="form.domicile_city"
                                placeholder="Semarang" />
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <x-form-textarea label="Alamat Asal (Sesuai KTP)*" wire:model="form.original_address"
                                placeholder="Alamat lengkap sesuai KTP" />
                            <x-form-textarea label="Alamat Sekarang*" wire:model="form.current_address"
                                placeholder="Alamat domisili saat ini" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Nomor Kontak 1 (HP Utama)*" wire:model="form.phone_number"
                                placeholder="08xxxx" />
                            <x-form-input label="Nomor Kontak 2 (Kontak Darurat)" wire:model="form.contact_phone_1"
                                placeholder="08xxxx" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <x-form-select label="Golongan*" wire:model="form.rank">
                                <option value="">Pilih Golongan</option>
                                <option value="Golongan I">Golongan I</option>
                                <option value="Golongan II">Golongan II</option>
                                <option value="Golongan III">Golongan III</option>
                                <option value="Golongan IV">Golongan IV</option>
                            </x-form-select>
                            <div class="col-span-2">
                                <x-form-select label="Posisi Awak*" wire:model="form.crew_position_id">
                                    <option value="">Pilih Posisi</option>
                                    @foreach ($this->crewPositions as $cp)
                                        <option value="{{ $cp->id }}">{{ $cp->name }}</option>
                                    @endforeach
                                </x-form-select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Tanggal Masuk*" wire:model="form.join_date" type="date" />
                            <x-form-select label="Pendidikan Terakhir" wire:model="form.education">
                                <option value="">Pilih Pendidikan</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA/SMK">SMA/SMK</option>
                                <option value="D3">D3</option>
                                <option value="S1">S1</option>
                            </x-form-select>
                        </div>
                    </div>

                    {{-- Right Column: Admin & Ops Info --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 mb-2">
                            <div
                                class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
                                <x-heroicon-o-briefcase class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                            </div>
                            <h4 class="text-sm font-bold uppercase tracking-wider text-zinc-900 dark:text-white">
                                Administrasi & Penugasan</h4>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-select label="Agama*" wire:model="form.religion">
                                <option value="">Pilih Agama</option>
                                <option value="Islam">Islam</option>
                                <option value="Kristen Protestan">Kristen Protestan</option>
                                <option value="Kristen Katolik">Kristen Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Khonghucu">Khonghucu</option>
                            </x-form-select>
                            <x-form-select label="Status Pernikahan*" wire:model="form.marital_status">
                                <option value="">Pilih Status</option>
                                <option value="Lajang">Lajang</option>
                                <option value="Menikah">Menikah</option>
                                <option value="Cerai">Cerai</option>
                            </x-form-select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-select label="Golongan Darah*" wire:model="form.blood_type">
                                <option value="">Pilih</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                            </x-form-select>
                            <x-form-input label="Jumlah Anak*" wire:model="form.children_count" type="number"
                                placeholder="0" />
                        </div>

                        <x-form-input label="Nama Istri/Suami" wire:model="form.spouse_name"
                            placeholder="Nama Pasangan" />

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-select label="Wilayah Penugasan" wire:model.live="form.region">
                                <option value="">Pilih Wilayah</option>
                                @foreach ($this->provinces as $prov)
                                    <option value="{{ $prov }}">{{ $prov }}</option>
                                @endforeach
                            </x-form-select>
                            <x-form-select label="Kota Penugasan*" wire:model.live="form.pool_id">
                                <option value="">Pilih Kota Penugasan</option>
                                @foreach ($this->locations as $loc)
                                    <option value="{{ $loc->id }}">
                                        {{ $loc->city ? $loc->city . ' - ' : '' }}{{ $loc->name }}</option>
                                @endforeach
                            </x-form-select>
                        </div>

                        <x-form-select label="Agen Pool" wire:model.live="form.agent_id">
                            <option value="">Pilih Agen</option>
                            @foreach ($this->agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                            @endforeach
                        </x-form-select>

                        <x-form-select label="Armada Default" wire:model="form.bus_id">
                            <option value="">Pilih Armada</option>
                            @foreach ($this->buses as $bus)
                                <option value="{{ $bus->id }}">{{ $bus->fleet_code }} - {{ $bus->name }}
                                </option>
                            @endforeach
                        </x-form-select>

                        <x-form-select label="Rute Default" wire:model="form.route_id">
                            <option value="">Pilih Rute</option>
                            @foreach ($this->routes as $route)
                                <option value="{{ $route->id }}">{{ $route->route_code }} - {{ $route->name }}
                                </option>
                            @endforeach
                        </x-form-select>

                        <x-form-select label="Status Operasional*" wire:model="form.status" class="font-bold">
                            <option value="active">Active (Tersedia)</option>
                            <option value="on_leave">On Leave (Cuti)</option>
                            <option value="suspended">Suspended</option>
                            <option value="inactive">Inactive</option>
                        </x-form-select>
                    </div>
                </div>

                {{-- Footer: Actions --}}
                <div
                    class="p-8 bg-zinc-50/50 dark:bg-zinc-800/20 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-400 italic text-[11px]">
                        <x-heroicon-o-information-circle class="w-4 h-4" />
                        Pastikan semua data bertanda bintang (*) telah diisi dengan benar.
                    </div>
                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <button type="submit" wire:loading.attr="disabled"
                            class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-0 px-8 rounded-2xl shadow-lg shadow-indigo-600/30 font-bold tracking-wide flex items-center justify-center gap-2 group transition-all">
                            <x-heroicon-o-check-circle wire:loading.remove wire:target="saveCrew"
                                class="w-5 h-5 group-hover:scale-110 transition-transform" />
                            <span wire:loading wire:target="saveCrew"
                                class="loading loading-spinner loading-sm"></span>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
