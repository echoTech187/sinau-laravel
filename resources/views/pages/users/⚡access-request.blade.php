<?php
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AccessRequest;
use App\Models\Roles;
use App\Models\Permissions;
use App\Helpers\AuditLogger;

new class extends Component {
    use WithPagination;

    public string $type = 'role_grant';
    public ?int $selectedRoleId = null;
    public string $selectedPermissions = '';
    public string $reason = '';

    public function mount()
    {
        // Default values
    }

    public function submitRequest()
    {
        $this->validate([
            'type' => 'required|in:role_grant,permission_grant',
            'reason' => 'required|min:10',
            'selectedRoleId' => 'required_if:type,role_grant',
            'selectedPermissions' => 'required_if:type,permission_grant',
        ]);

        $requestedChanges = [];
        if ($this->type === 'role_grant') {
            $requestedChanges = ['role_id' => $this->selectedRoleId];
        } else {
            $requestedChanges = ['permissions' => array_filter(explode(',', $this->selectedPermissions))];
        }

        AccessRequest::create([
            'requester_id' => auth()->id(),
            'target_user_id' => auth()->id(), // For now, users request for themselves
            'type' => $this->type,
            'requested_changes' => $requestedChanges,
            'reason' => $this->reason,
            'status' => 'pending',
        ]);

        AuditLogger::record(AuditLogger::ACCESS_REQUEST_CREATED, auth()->id(), [
            'type' => $this->type,
            'changes' => $requestedChanges,
        ]);

        $this->reset(['selectedRoleId', 'selectedPermissions', 'reason']);
        $this->dispatch('notify', type: 'success', title: 'Berhasil', message: 'Permintaan akses Anda telah diajukan and menunggu review.');
    }

    public function getMyRequests()
    {
        return AccessRequest::with(['reviewer'])
            ->where('requester_id', auth()->id())
            ->latest()
            ->paginate(5);
    }
};
?>

<div class="container space-y-6">
    @include('partials.heading', [
        'title' => 'Permintaan Akses Saya',
        'description' => 'Ajukan permintaan penambahan hak akses atau lihat status pengajuan sebelumnya.',
    ])

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Form Col --}}
        <div class="lg:col-span-1 space-y-6">
            <div
                class="bg-white/60 dark:bg-zinc-800/60 backdrop-blur-xl border border-white/30 dark:border-white/10 rounded-2xl p-6 shadow-sm animate-fade-in-up">
                <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-200 mb-4 flex items-center gap-2">
                    <x-heroicon-o-plus-circle class="size-4 text-blue-500" />
                    Ajukan Permintaan Baru
                </h3>

                <form wire:submit="submitRequest" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Tipe
                            Permintaan</label>
                        <select wire:model.live="type"
                            class="select select-bordered w-full bg-white dark:bg-zinc-900 text-sm rounded-xl border-zinc-200 dark:border-zinc-700">
                            <option value="role_grant">Tambah Role Baru</option>
                            <option value="permission_grant">Tambah Izin Khusus</option>
                        </select>
                    </div>

                    @if ($type === 'role_grant')
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Pilih
                                Role</label>
                            <select wire:model="selectedRoleId"
                                class="select select-bordered w-full bg-white dark:bg-zinc-900 text-sm rounded-xl border-zinc-200 dark:border-zinc-700">
                                <option value="">— Pilih Role —</option>
                                @foreach (App\Models\Roles::where('slug', '!=', 'super-admin')->get() as $role)
                                    <option value="{{ $role->id }}">{{ $role->role }}</option>
                                @endforeach
                            </select>
                            @error('selectedRoleId')
                                <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @else
                        <div>
                            <label
                                class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Permission
                                (Slug, Pisah Koma)</label>
                            <input type="text" wire:model="selectedPermissions"
                                placeholder="e.g. user.create, user.delete"
                                class="input input-bordered w-full bg-white dark:bg-zinc-900 text-sm rounded-xl border-zinc-200 dark:border-zinc-700" />
                            <p class="text-[10px] text-zinc-400 mt-1">Masukkan list slug permission dipisahkan tanda
                                koma.</p>
                            @error('selectedPermissions')
                                <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Alasan
                            Pengajuan</label>
                        <textarea wire:model="reason" placeholder="Jelaskan mengapa Anda memerlukan akses ini..."
                            class="textarea textarea-bordered w-full h-24 bg-white dark:bg-zinc-900 text-sm rounded-xl border-zinc-200 dark:border-zinc-700 resize-none"></textarea>
                        @error('reason')
                            <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit"
                        class="btn btn-primary w-full shadow-lg shadow-blue-500/20 rounded-xl gap-2 h-10 min-h-0 bg-blue-600 border-none text-white">
                        <x-heroicon-o-paper-airplane class="size-4" />
                        Kirim Permintaan
                    </button>
                </form>
            </div>
        </div>

        {{-- History Col --}}
        <div class="lg:col-span-2">
            <div class="bg-white/60 dark:bg-zinc-800/60 backdrop-blur-xl border border-white/30 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden animate-fade-in-up"
                style="animation-delay: 100ms">
                <div class="p-4 border-b border-zinc-100 dark:border-zinc-700/50 bg-zinc-50/50 dark:bg-zinc-900/50">
                    <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-200 flex items-center gap-2">
                        <x-heroicon-o-clock class="size-4 text-zinc-400" />
                        Riwayat Pengajuan
                    </h3>
                </div>

                @php $myRequests = $this->getMyRequests(); @endphp

                @if ($myRequests->isEmpty())
                    <div class="py-20 text-center space-y-3">
                        <div
                            class="size-12 rounded-2xl bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center mx-auto">
                            <x-heroicon-o-document-magnifying-glass class="size-6 text-zinc-300" />
                        </div>
                        <p class="text-xs text-zinc-400 font-medium tracking-tight">Belum ada riwayat pengajuan akses.
                        </p>
                    </div>
                @else
                    <div class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                        @foreach ($myRequests as $req)
                            <div class="p-4 hover:bg-zinc-50/50 dark:hover:bg-zinc-700/20 transition-all">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 space-y-2">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $req->typeColor() }}">
                                                {{ $req->typeLabel() }}
                                            </span>
                                            <span class="text-[10px] text-zinc-400 font-mono italic">
                                                {{ is_array($req->requested_changes) ? json_encode($req->requested_changes) : $req->requested_changes }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-zinc-600 dark:text-zinc-400 italic">"{{ $req->reason }}"
                                        </p>
                                        <div class="text-[10px] text-zinc-400 flex items-center gap-1">
                                            <x-heroicon-o-calendar class="size-3" />
                                            Diajukan {{ $req->created_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    <div class="text-right space-y-1.5 flex flex-col items-end">
                                        <div
                                            class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $req->statusColor() }}">
                                            {{ ucfirst($req->status) }}
                                        </div>
                                        @if ($req->status !== 'pending' && $req->review_note)
                                            <div
                                                class="text-[10px] text-zinc-500 bg-zinc-100 dark:bg-zinc-900 rounded p-1.5 max-w-37.5 relative">
                                                <div
                                                    class="absolute -top-1 right-2 size-2 bg-zinc-100 dark:bg-zinc-900 rotate-45">
                                                </div>
                                                <span class="font-bold">Note:</span> {{ $req->review_note }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-4 border-t border-zinc-100 dark:border-zinc-700/50 text-xs">
                        {{ $myRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
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

<x-slot:title>Permintaan Akses Saya</x-slot:title>
