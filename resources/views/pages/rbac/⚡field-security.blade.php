<?php
use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Roles;
use App\Models\FieldPermission;
use Illuminate\Support\Facades\Cache;
use App\Helpers\AuditLogger;

new class extends Component {
    public Roles $roles;

    // Form state
    public string $model = '';
    public string $field = '';
    public bool $is_hidden = true;
    public ?int $editingId = null;

    // Deletion confirmation
    public ?int $confirmingId = null;
    public ?string $confirmingField = null;

    // Available models & their securable fields
    public array $availableModels = [
        'App\Models\Transaction' => [
            'label' => 'Transaksi (Transaction)',
            'fields' => [
                'amount' => 'Nominal (amount)',
                'invoice_number' => 'No. Invoice (invoice_number)',
                'description' => 'Keterangan (description)',
            ],
        ],
        'App\Models\User' => [
            'label' => 'Pengguna (User)',
            'fields' => [
                'email' => 'Email',
                'password' => 'Password',
                'avatar' => 'Avatar / Foto',
            ],
        ],
        'App\Models\Employee' => [
            'label' => 'Karyawan (Employee)',
            'fields' => [
                'salary' => 'Gaji (salary)',
                'phone_number' => 'No. Telepon',
                'address' => 'Alamat',
            ],
        ],
    ];

    public function mount(Roles $roles): void
    {
        $this->roles = $roles;
    }

    #[Computed]
    public function fieldPermissions()
    {
        return FieldPermission::where('role_id', $this->roles->id)->get();
    }

    public function modelLabel(string $modelClass): string
    {
        return $this->availableModels[$modelClass]['label'] ?? class_basename($modelClass);
    }

    public function fieldLabel(string $modelClass, string $field): string
    {
        return $this->availableModels[$modelClass]['fields'][$field] ?? $field;
    }

    public function availableFields(): array
    {
        if (!$this->model || !isset($this->availableModels[$this->model])) {
            return [];
        }
        $usedFields = FieldPermission::where('role_id', $this->roles->id)->where('model', $this->model)->when($this->editingId, fn($q) => $q->where('id', '!=', $this->editingId))->pluck('field')->toArray();

        return array_filter($this->availableModels[$this->model]['fields'], fn($label, $key) => !in_array($key, $usedFields), ARRAY_FILTER_USE_BOTH);
    }

    public function updatedModel(): void
    {
        $this->field = '';
    }

    public function save(): void
    {
        $this->validate(
            [
                'model' => 'required',
                'field' => 'required',
            ],
            [
                'model.required' => 'Pilih model terlebih dahulu.',
                'field.required' => 'Pilih field yang akan disensor.',
            ],
        );

        if ($this->editingId) {
            FieldPermission::where('id', $this->editingId)->update([
                'model' => $this->model,
                'field' => $this->field,
                'is_hidden' => $this->is_hidden,
            ]);
        } else {
            FieldPermission::create([
                'role_id' => $this->roles->id,
                'model' => $this->model,
                'field' => $this->field,
                'is_hidden' => $this->is_hidden,
            ]);
        }

        $this->clearRoleCache();
        $this->resetForm();
        unset($this->fieldPermissions);
        $this->dispatch('saved');

        // Audit trail
        $event = $this->editingId ? AuditLogger::FIELD_SECURITY_UPDATED : AuditLogger::FIELD_SECURITY_ADDED;
        AuditLogger::record($event, null, [
            'role' => $this->roles->role,
            'role_id' => $this->roles->id,
            'model' => $this->model,
            'field' => $this->field,
            'is_hidden' => $this->is_hidden,
        ]);
    }

    public function edit(int $id): void
    {
        $fp = FieldPermission::findOrFail($id);
        $this->editingId = $fp->id;
        $this->model = $fp->model;
        $this->field = $fp->field;
        $this->is_hidden = $fp->is_hidden;
    }

    public function promptDelete(int $id, string $field): void
    {
        $this->confirmingId = $id;
        $this->confirmingField = $field;
    }

    public function confirmDelete(): void
    {
        if (!$this->confirmingId) {
            return;
        }
        $this->delete($this->confirmingId);
        $this->confirmingId = null;
        $this->confirmingField = null;
    }

    public function delete(int $id): void
    {
        $fp = FieldPermission::find($id);
        FieldPermission::destroy($id);
        $this->clearRoleCache();
        unset($this->fieldPermissions);
        if ($this->editingId === $id) {
            $this->resetForm();
        }

        // Audit trail
        AuditLogger::record(AuditLogger::FIELD_SECURITY_REMOVED, null, [
            'role' => $this->roles->role,
            'role_id' => $this->roles->id,
            'model' => $fp?->model,
            'field' => $fp?->field,
        ]);
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->model = '';
        $this->field = '';
        $this->is_hidden = true;
        $this->editingId = null;
        $this->resetErrorBag();
    }

    private function clearRoleCache(): void
    {
        // Clear per-user caches that depend on field permissions for this role
        $userIds = $this->roles->users()->pluck('id');
        foreach ($userIds as $uid) {
            Cache::forget("user_field_permissions_{$uid}");
            Cache::forget("user_permissions_{$uid}");
        }
    }
};
?>

<div class="container space-y-5 h-full">
    <x-slot:title>Keamanan Kolom (Field Security)</x-slot:title>
    @include('partials.heading', [
        'title' => 'Field Security: ' . $this->roles->role,
        'description' => 'Atur kolom data sensitif yang disembunyikan dari peran ini.',
    ])

    {{-- Breadcrumb --}}
    <nav class="flex items-center flex-wrap gap-2 text-sm text-zinc-500 dark:text-zinc-400 -mt-4">
        <a href="{{ route('rbac.show') }}" class="hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-1">
            <x-heroicon-o-shield-check class="size-4" />
            RBAC Manager
        </a>
        <x-heroicon-o-chevron-right class="size-4 text-zinc-300 dark:text-zinc-600" />
        <a href="{{ route('rbac.permission.edit', $this->roles) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
            {{ $this->roles->role }}
        </a>
        <x-heroicon-o-chevron-right class="size-4 text-zinc-300 dark:text-zinc-600" />
        <span class="text-zinc-800 dark:text-zinc-200 font-medium">Field Security</span>
    </nav>

    {{-- Success flash --}}
    <div x-data="{ show: false }" x-on:saved.window="show = true; setTimeout(() => show = false, 3000)" x-show="show"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display:none;"
        class="flex items-center gap-2 text-sm text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40 rounded-xl px-4 py-2.5">
        <x-heroicon-o-check-circle class="size-4 shrink-0" />
        Aturan sensor berhasil disimpan.
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        {{-- ============================================================ --}}
        {{-- LEFT: Form Tambah / Edit                                      --}}
        {{-- ============================================================ --}}
        <div class="lg:col-span-2 space-y-4">
            <div
                class="bg-white/60 dark:bg-zinc-800/60 backdrop-blur-xl border border-white/30 dark:border-white/10
                        rounded-2xl shadow-sm p-5 space-y-4 animate-fade-in-up">

                <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                    @if ($this->editingId)
                        <x-heroicon-o-pencil-square class="size-5 text-amber-500" />
                        Edit Aturan Sensor
                    @else
                        <x-heroicon-o-plus-circle class="size-5 text-blue-500" />
                        Tambah Aturan Sensor
                    @endif
                </h3>

                {{-- Model --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                        Model / Entitas
                    </label>
                    <select wire:model.live="model"
                        class="select select-bordered select-sm w-full bg-white dark:bg-zinc-900 dark:border-zinc-600 dark:text-zinc-100">
                        <option value="">— Pilih Model —</option>
                        @foreach ($this->availableModels as $class => $meta)
                            <option value="{{ $class }}">{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                    @error('model')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Field --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                        Kolom / Field
                    </label>
                    <select wire:model="field"
                        class="select select-bordered select-sm w-full bg-white dark:bg-zinc-900 dark:border-zinc-600 dark:text-zinc-100"
                        {{ !$this->model ? 'disabled' : '' }}>
                        <option value="">— Pilih Field —</option>
                        @foreach ($this->availableFields() as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('field')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                    @if ($this->model && empty($this->availableFields()))
                        <p class="text-xs text-amber-500 flex items-center gap-1">
                            <x-heroicon-o-exclamation-triangle class="size-3.5" />
                            Semua field pada entitas ini sudah dikonfigurasi.
                        </p>
                    @endif
                </div>

                {{-- is_hidden toggle --}}
                <div
                    class="flex items-center justify-between py-2 px-3 rounded-xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
                    <div>
                        <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Sembunyikan field?</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Aktifkan untuk menyensor data pada peran ini
                        </p>
                    </div>
                    <input type="checkbox" class="toggle toggle-primary toggle-sm" wire:model="is_hidden" />
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-2 pt-1">
                    <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary btn-sm flex-1 gap-1">
                        <x-heroicon-o-check class="size-4" />
                        {{ $this->editingId ? 'Simpan Perubahan' : 'Tambah Aturan' }}
                    </button>
                    @if ($this->editingId)
                        <button wire:click="cancelEdit" class="btn btn-ghost btn-sm text-zinc-600 dark:text-zinc-400">
                            Batal
                        </button>
                    @endif
                </div>
            </div>

            {{-- Info Card --}}
            <div
                class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/40 rounded-2xl p-4 space-y-2">
                <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-300 flex items-center gap-1.5">
                    <x-heroicon-o-information-circle class="size-4" />
                    Cara kerja Field Security
                </h4>
                <ul class="text-xs text-blue-700 dark:text-blue-400 space-y-1 list-disc list-inside">
                    <li>Aturan berlaku untuk semua pengguna dengan peran <strong>{{ $this->roles->role }}</strong>.
                    </li>
                    <li>Kolom yang disensor akan ditampilkan sebagai <code>Rp ••••••••</code> atau <code>***</code>.
                    </li>
                    <li>Peran <em>Super Admin</em> selalu dapat melihat semua data.</li>
                    <li>Perubahan langsung efektif (cache otomatis di-reset).</li>
                </ul>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- RIGHT: Daftar Aturan yang Aktif                              --}}
        {{-- ============================================================ --}}
        <div class="lg:col-span-3 space-y-3">

            <div class="flex items-center justify-between gap-4">
                <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                    <x-heroicon-o-eye-slash class="size-5 text-rose-500" />
                    Aturan Sensor Aktif
                    <span
                        class="badge badge-sm bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 border-0">
                        {{ $this->fieldPermissions->count() }}
                    </span>
                </h3>
            </div>

            @if ($this->fieldPermissions->isEmpty())
                <div
                    class="flex flex-col items-center justify-center py-16 gap-3
                            bg-white/60 dark:bg-zinc-800/60 backdrop-blur-xl
                            border border-white/30 dark:border-white/10 rounded-2xl shadow-sm">
                    <div class="p-4 rounded-2xl bg-zinc-100 dark:bg-zinc-700">
                        <x-heroicon-o-eye class="size-10 text-zinc-400 dark:text-zinc-500" />
                    </div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Belum ada aturan sensor</p>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 text-center max-w-xs">
                        Semua field data terlihat untuk peran ini. Tambahkan aturan di panel kiri untuk menyembunyikan
                        data sensitif.
                    </p>
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($this->fieldPermissions->groupBy('model') as $modelClass => $rules)
                        <div
                            class="bg-white/60 dark:bg-zinc-800/60 backdrop-blur-xl
                                    border border-white/30 dark:border-white/10
                                    rounded-2xl shadow-sm overflow-hidden animate-fade-in-up">

                            {{-- Model Header --}}
                            <div
                                class="flex items-center gap-3 px-4 py-3
                                        bg-zinc-50/70 dark:bg-zinc-800/70
                                        border-b border-zinc-100 dark:border-zinc-700">
                                <div class="p-1.5 rounded-lg bg-rose-100 dark:bg-rose-900/30 shrink-0">
                                    <x-heroicon-o-table-cells class="size-4 text-rose-500 dark:text-rose-400" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                                        {{ $this->modelLabel($modelClass) }}
                                    </p>
                                    <p class="text-xs font-mono text-zinc-400 dark:text-zinc-500 truncate">
                                        {{ $modelClass }}</p>
                                </div>
                                <span
                                    class="badge badge-sm bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 border-0">
                                    {{ $rules->count() }} field
                                </span>
                            </div>

                            {{-- Field Rules --}}
                            <div class="divide-y divide-zinc-100 dark:divide-zinc-700/60">
                                @foreach ($rules as $fp)
                                    <div
                                        class="flex items-center gap-3 px-4 py-3 group/row
                                                hover:bg-zinc-50 dark:hover:bg-zinc-700/30 transition-colors
                                                {{ $this->editingId === $fp->id ? 'bg-amber-50/60 dark:bg-amber-900/10 border-l-2 border-amber-400' : '' }}">

                                        {{-- Status badge --}}
                                        @if ($fp->is_hidden)
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                         bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400
                                                         text-xs font-medium shrink-0">
                                                <x-heroicon-o-eye-slash class="size-3" />
                                                Tersensor
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                         bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400
                                                         text-xs font-medium shrink-0">
                                                <x-heroicon-o-eye class="size-3" />
                                                Terlihat
                                            </span>
                                        @endif

                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100">
                                                {{ $this->fieldLabel($modelClass, $fp->field) }}
                                            </p>
                                            <p class="text-xs font-mono text-zinc-400 dark:text-zinc-500">
                                                {{ $fp->field }}</p>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button wire:click="edit({{ $fp->id }})"
                                                class="btn btn-ghost btn-xs text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20"
                                                title="Edit">
                                                <x-heroicon-o-pencil class="size-3.5" />
                                            </button>
                                            <button
                                                wire:click="promptDelete({{ $fp->id }}, '{{ $fp->field }}')"
                                                class="btn btn-ghost btn-xs text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"
                                                title="Hapus">
                                                <x-heroicon-o-trash class="size-3.5" />
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Footer Actions --}}
    <div
        class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
        <a href="{{ route('rbac.permission.edit', $this->roles) }}"
            class="btn btn-ghost text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800">
            <x-heroicon-o-arrow-left class="size-4" />
            Kembali ke Izin Akses
        </a>
        <a href="{{ route('rbac.add.teams', $this->roles) }}"
            class="btn btn-outline dark:border-zinc-600 dark:text-zinc-300 dark:hover:bg-zinc-800 btn-sm">
            <x-heroicon-o-user-plus class="size-4" />
            Kelola Anggota
        </a>
    </div>

    @if ($confirmingId)
        <x-rbac.confirm-modal title="Hapus Aturan Sensor"
            message="Yakin ingin menghapus aturan sensor field '{{ $confirmingField }}'?"
            confirmAction="confirmDelete" cancelAction="$set('confirmingId', null)" />
    @endif
</div>

<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.4s ease-out both;
    }
</style>
