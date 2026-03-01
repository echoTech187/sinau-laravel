<?php
use App\Helpers\AuditLogger;
use App\Models\AccessRequest;
use App\Models\Permissions;
use App\Models\Roles;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $statusFilter = 'pending';

    public function mount()
    {
        abort_unless(auth()->user()->can('rbac.approvals.index'), 403);
    }

    public string $typeFilter = '';

    public string $search = '';

    public ?string $selectedRequestId = null;

    public string $reviewNote = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setStatus(string $status): void
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function getRequests()
    {
        return AccessRequest::with(['requester', 'targetUser', 'reviewer'])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->whereHas('requester', fn($r) => $r->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('targetUser', fn($t) => $t->where('name', 'like', "%{$this->search}%"))
                        ->orWhere('reason', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(10);
    }

    public function approve(string $id): void
    {
        $request = AccessRequest::findOrFail($id);

        if ($request->status !== 'pending') {
            $this->dispatch('notify', type: 'error', title: 'Gagal', description: 'Permintaan ini sudah diproses.');

            return;
        }

        DB::beginTransaction();
        try {
            $targetUser = $request->targetUser;
            $changes = $request->requested_changes;

            if ($request->type === 'role_grant') {
                $roleId = $changes['role_id'];
                $dataScope = $changes['data_scope'] ?? null;

                // Cek if already has this secondary role
                $exists = DB::table('role_user')->where('user_id', $targetUser->id)->where('role_id', $roleId)->exists();

                if (!$exists) {
                    $targetUser->additionalRoles()->attach($roleId, ['data_scope' => $dataScope]);
                }

                AuditLogger::record(AuditLogger::USER_ROLE_ASSIGNED, $targetUser->id, [
                    'role_id' => $roleId,
                    'data_scope' => $dataScope,
                    'via_approval' => $id,
                ]);
            } elseif ($request->type === 'role_revoke') {
                $roleId = $changes['role_id'];
                $targetUser->additionalRoles()->detach($roleId);

                AuditLogger::record(AuditLogger::USER_ROLE_REVOKED, $targetUser->id, [
                    'role_id' => $roleId,
                    'via_approval' => $id,
                ]);
            } elseif ($request->type === 'permission_grant') {
                $permissions = $changes['permissions'] ?? [];
                foreach ($permissions as $slug) {
                    $p = Permissions::where('slug', $slug)->first();
                    if ($p) {
                        $targetUser->permissions()->syncWithoutDetaching([$p->id => ['forbidden' => false]]);
                    }
                }

                AuditLogger::record(AuditLogger::DIRECT_PERMISSION_GRANT, $targetUser->id, [
                    'permissions' => $permissions,
                    'via_approval' => $id,
                ]);
            } elseif ($request->type === 'permission_revoke') {
                $permissions = $changes['permissions'] ?? [];
                foreach ($permissions as $slug) {
                    $p = Permissions::where('slug', $slug)->first();
                    if ($p) {
                        $targetUser->permissions()->syncWithoutDetaching([$p->id => ['forbidden' => true]]);
                    }
                }

                AuditLogger::record(AuditLogger::DIRECT_PERMISSION_DENY, $targetUser->id, [
                    'permissions' => $permissions,
                    'via_approval' => $id,
                ]);
            }

            $request->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_note' => $this->reviewNote,
            ]);

            AuditLogger::record(AuditLogger::ACCESS_REQUEST_APPROVED, $targetUser->id, [
                'request_id' => $id,
                'type' => $request->type,
            ]);

            DB::commit();
            $this->reviewNote = '';
            $this->dispatch('notify', type: 'success', title: 'Berhasil', message: 'Permintaan disetujui and perubahan telah diterapkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', type: 'error', title: 'Gagal', message: 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(string $id): void
    {
        $request = AccessRequest::findOrFail($id);

        if ($request->status !== 'pending') {
            $this->dispatch('notify', type: 'error', title: 'Gagal', description: 'Permintaan ini sudah diproses.');

            return;
        }

        $request->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $this->reviewNote,
        ]);

        AuditLogger::record(AuditLogger::ACCESS_REQUEST_REJECTED, $request->target_user_id, [
            'request_id' => $id,
            'reason' => $this->reviewNote,
        ]);

        $this->reviewNote = '';
        $this->dispatch('notify', type: 'info', title: 'Ditolak', message: 'Permintaan telah ditolak.');
    }
};
?>

<div class="container relative min-h-screen pb-10">
    <x-slot:title>Inbox Approval</x-slot:title>

    <!-- Decorative Background Blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-150 h-150 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-1/4 w-125 h-125 bg-violet-500/10 dark:bg-violet-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-6">
        <!-- Header -->
        <div class="animate-fade-in-up">
            <header
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700/50 pb-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-linear-to-br from-indigo-500 to-violet-600 shadow-lg shadow-indigo-500/30">
                            <x-heroicon-o-inbox class="w-6 h-6 text-white" />
                        </div>
                        Inbox Approval
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Kelola permintaan perubahan hak akses dari pengguna.
                    </p>
                </div>
            </header>
        </div>

        @php $requests = $this->getRequests(); @endphp

        <!-- Table Card (buses-standard: p-6, filter+search mb-6) -->
        <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm overflow-hidden animate-fade-in-up"
            style="animation-delay: 0.1s">

            <!-- Filter + Search Row -->
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between xl:justify-end mb-6">
                <!-- Status tabs pill + Type filter pill -->
                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Status Tab Pill -->
                    <div
                        class="flex gap-1 p-1 bg-zinc-100/80 dark:bg-zinc-800/80 backdrop-blur rounded-xl border border-zinc-200/50 dark:border-zinc-700/50">
                        <button wire:click="setStatus('pending')"
                            class="btn btn-sm border-0 rounded-lg font-bold text-[10px] uppercase tracking-widest transition-all {{ $statusFilter === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-transparent text-zinc-500 hover:text-zinc-800' }}">
                            <span
                                class="w-1.5 h-1.5 rounded-full {{ $statusFilter === 'pending' ? 'bg-white animate-pulse' : 'bg-zinc-400' }}"></span>
                            Pending
                        </button>
                        <button wire:click="setStatus('approved')"
                            class="btn btn-sm border-0 rounded-lg font-bold text-[10px] uppercase tracking-widest transition-all {{ $statusFilter === 'approved' ? 'bg-emerald-500 text-white shadow-sm' : 'bg-transparent text-zinc-500 hover:text-zinc-800' }}">
                            <x-heroicon-o-check-circle class="w-3.5 h-3.5" />
                            Disetujui
                        </button>
                        <button wire:click="setStatus('rejected')"
                            class="btn btn-sm border-0 rounded-lg font-bold text-[10px] uppercase tracking-widest transition-all {{ $statusFilter === 'rejected' ? 'bg-red-500 text-white shadow-sm' : 'bg-transparent text-zinc-500 hover:text-zinc-800' }}">
                            <x-heroicon-o-x-circle class="w-3.5 h-3.5" />
                            Ditolak
                        </button>
                    </div>

                    <!-- Type Filter Pill -->
                    <div
                        class="flex gap-2 p-1 bg-zinc-100/80 dark:bg-zinc-800/80 backdrop-blur rounded-xl border border-zinc-200/50 dark:border-zinc-700/50">
                        <select wire:model.live="typeFilter"
                            class="select select-sm border-0 bg-transparent focus:outline-none focus:ring-0 text-zinc-600 dark:text-zinc-300 font-medium">
                            <option value="">Semua Tipe</option>
                            <option value="role_grant">Tambah Role</option>
                            <option value="role_revoke">Cabut Role</option>
                            <option value="permission_grant">Tambah Izin</option>
                            <option value="permission_revoke">Cabut Izin</option>
                        </select>
                    </div>
                </div>

                <!-- Search -->
                <div class="relative w-full md:w-80 group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <x-heroicon-o-magnifying-glass
                            class="w-4 h-4 text-zinc-400 group-focus-within:text-indigo-500 transition-colors" />
                    </div>
                    <input type="text" placeholder="Cari nama atau alasan..." wire:model.live.debounce.300ms="search"
                        class="input input-sm w-full pl-11 bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20" />
                </div>
            </div>

            <!-- Table -->
            @if ($requests->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 gap-5 text-zinc-400">
                    <div
                        class="w-20 h-20 rounded-full bg-zinc-50 dark:bg-zinc-800/50 flex items-center justify-center border border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-inbox class="w-10 h-10 text-zinc-300 dark:text-zinc-600" />
                    </div>
                    <div class="text-center">
                        <p class="text-base font-bold text-zinc-500 dark:text-zinc-400">Tidak ada permintaan</p>
                        <p class="text-sm text-zinc-400 dark:text-zinc-500 mt-1">Antrean approval sedang kosong saat
                            ini.</p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table table-sm w-full whitespace-nowrap">
                        <thead>
                            <tr
                                class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider text-[10px]">
                                <th class="py-4 pl-4">Pengaju / Target</th>
                                <th class="py-4">Jenis Perubahan</th>
                                <th class="py-4">Alasan</th>
                                <th class="py-4">Waktu</th>
                                <th class="py-4 pr-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                            @foreach ($requests as $req)
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-all group">
                                    <!-- Pengaju / Target -->
                                    <td class="py-4 pl-4">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-1.5">
                                                <span
                                                    class="text-[9px] uppercase font-black text-zinc-400 tracking-widest">Dari:</span>
                                                <span
                                                    class="text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ $req->requester->name }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <span
                                                    class="text-[9px] uppercase font-black text-zinc-400 tracking-widest">Untuk:</span>
                                                <span
                                                    class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ $req->targetUser->name }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Jenis -->
                                    <td class="py-4">
                                        <div class="space-y-1">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-black uppercase border {{ $req->typeColor() }}">
                                                {{ $req->typeLabel() }}
                                            </span>
                                            <div
                                                class="text-[10px] text-zinc-400 max-w-45 wrap-break-word font-mono italic">
                                                {{ is_array($req->requested_changes) ? json_encode($req->requested_changes) : $req->requested_changes }}
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Alasan -->
                                    <td class="py-4">
                                        <p
                                            class="text-xs text-zinc-500 dark:text-zinc-400 max-w-50 leading-relaxed italic">
                                            "{{ $req->reason ?: 'Tidak ada alasan' }}"
                                        </p>
                                    </td>

                                    <!-- Waktu -->
                                    <td class="py-4 whitespace-nowrap">
                                        <div class="text-xs font-bold text-zinc-700 dark:text-zinc-300">
                                            {{ $req->created_at->diffForHumans() }}</div>
                                        <div class="text-[10px] text-zinc-400 font-mono">
                                            {{ $req->created_at->format('d M Y, H:i') }}</div>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-4 pr-4 text-right">
                                        @if ($req->status === 'pending')
                                            <div class="flex items-center justify-end gap-2" x-data="{ confirming: false }">
                                                <div x-show="!confirming" class="flex gap-1">
                                                    <button @click="confirming = 'approve'"
                                                        class="btn btn-ghost btn-xs btn-square rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600"
                                                        title="Setujui">
                                                        <x-heroicon-o-check class="w-4 h-4" />
                                                    </button>
                                                    <button @click="confirming = 'reject'"
                                                        class="btn btn-ghost btn-xs btn-square rounded-lg bg-red-50 hover:bg-red-100 text-red-600"
                                                        title="Tolak">
                                                        <x-heroicon-o-x-mark class="w-4 h-4" />
                                                    </button>
                                                </div>
                                                <div x-show="confirming" class="flex flex-col gap-2 items-end"
                                                    @click.outside="confirming = false">
                                                    <textarea wire:model="reviewNote" placeholder="Catatan (opsional)..."
                                                        class="w-48 h-16 text-[10px] p-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 resize-none outline-none focus:ring-2 focus:ring-indigo-500/20"></textarea>
                                                    <div class="flex gap-2">
                                                        <button @click="confirming = false"
                                                            class="text-[10px] font-bold text-zinc-400 hover:text-zinc-600">Batal</button>
                                                        <button x-show="confirming === 'approve'"
                                                            wire:click="approve('{{ $req->id }}')"
                                                            class="btn btn-xs bg-emerald-500 hover:bg-emerald-600 text-white border-0 rounded-lg font-bold shadow-sm">
                                                            Setujui
                                                        </button>
                                                        <button x-show="confirming === 'reject'"
                                                            wire:click="reject('{{ $req->id }}')"
                                                            class="btn btn-xs bg-red-500 hover:bg-red-600 text-white border-0 rounded-lg font-bold shadow-sm">
                                                            Tolak
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex flex-col items-end gap-1">
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase border {{ $req->statusColor() }}">
                                                    @if ($req->status === 'approved')
                                                        <x-heroicon-s-check-circle class="w-3 h-3" />
                                                    @else
                                                        <x-heroicon-s-x-circle class="w-3 h-3" />
                                                    @endif
                                                    {{ ucfirst($req->status) }}
                                                </span>
                                                @if ($req->reviewer)
                                                    <div class="text-[10px] text-zinc-400">Oleh:
                                                        {{ $req->reviewer->name }}</div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($requests->hasPages())
                    <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        {{ $requests->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
