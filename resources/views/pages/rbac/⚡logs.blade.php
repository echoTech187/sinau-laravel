<?php
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RbacLog;
use App\Models\User;
use App\Helpers\AuditLogger;
use Livewire\Attributes\Title;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $filterEvent = '';
    public string $filterDate = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedFilterEvent(): void
    {
        $this->resetPage();
    }
    public function updatedFilterDate(): void
    {
        $this->resetPage();
    }

    public function getLogs()
    {
        return RbacLog::with(['actor', 'targetUser'])
            ->when($this->filterEvent, fn($q) => $q->where('event', $this->filterEvent))
            ->when($this->filterDate, fn($q) => $q->whereDate('created_at', $this->filterDate))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('event', 'like', "%{$this->search}%")
                        ->orWhereHas('actor', fn($a) => $a->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('targetUser', fn($t) => $t->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->latest()
            ->paginate(20);
    }

    public function clearAllLogs(): void
    {
        abort_unless(auth()->user()?->role?->slug === 'super-admin', 403);
        RbacLog::truncate();
        $this->resetPage();
    }

    public function eventOptions(): array
    {
        return [AuditLogger::ROLE_ASSIGNED, AuditLogger::ROLE_REVOKED, AuditLogger::PERMISSION_TOGGLED, AuditLogger::MODULE_PERMISSIONS_SET, AuditLogger::FIELD_SECURITY_ADDED, AuditLogger::FIELD_SECURITY_UPDATED, AuditLogger::FIELD_SECURITY_REMOVED, AuditLogger::DIRECT_PERMISSION_GRANT, AuditLogger::DIRECT_PERMISSION_DENY, AuditLogger::DIRECT_PERMISSION_RESET, AuditLogger::USER_ROLE_ASSIGNED, AuditLogger::USER_ROLE_REVOKED];
    }
};
?>

<div class="container relative min-h-screen pb-10">
    <x-slot:title>Log Aktivitas RBAC</x-slot:title>

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
                            <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-white" />
                        </div>
                        Log Aktivitas RBAC
                    </h1>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                        Jejak audit setiap perubahan hak akses dalam sistem.
                    </p>
                </div>
                @if (auth()->user()?->role?->slug === 'super-admin')
                    <button wire:click="clearAllLogs"
                        wire:confirm="Hapus SEMUA log aktivitas? Tindakan ini tidak dapat dibatalkan!"
                        class="btn btn-sm bg-red-600 hover:bg-red-700 text-white border-0 shadow-lg shadow-red-600/20 rounded-xl font-bold gap-2 shrink-0">
                        <x-heroicon-o-trash class="w-4 h-4" />
                        Hapus Semua Log
                    </button>
                @endif
            </header>
        </div>

        @php $logs = $this->getLogs(); @endphp

        <!-- Table Card (buses-standard: p-6, filter+search in mb-6 row) -->
        <div class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm overflow-hidden animate-fade-in-up"
            style="animation-delay: 0.1s">

            <!-- Filter + Search Row -->
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between xl:justify-end mb-6">
                <!-- Filter Pill -->
                <div
                    class="flex gap-2 p-1 bg-zinc-100/80 dark:bg-zinc-800/80 backdrop-blur rounded-xl border border-zinc-200/50 dark:border-zinc-700/50 w-full md:w-auto overflow-x-auto">
                    <select wire:model.live="filterEvent"
                        class="select select-sm border-0 bg-transparent focus:outline-none focus:ring-0 text-zinc-600 dark:text-zinc-300 font-medium">
                        <option value="">Semua Event</option>
                        @foreach ($this->eventOptions() as $evt)
                            <option value="{{ $evt }}">{{ AuditLogger::label($evt) }}</option>
                        @endforeach
                    </select>
                    <div class="w-px h-6 bg-zinc-300 dark:bg-zinc-700 my-auto"></div>
                    <input type="date" wire:model.live="filterDate"
                        class="input input-sm border-0 bg-transparent focus:outline-none focus:ring-0 text-zinc-600 dark:text-zinc-300 font-medium" />
                    @if ($this->search || $this->filterEvent || $this->filterDate)
                        <div class="w-px h-6 bg-zinc-300 dark:bg-zinc-700 my-auto"></div>
                        <button wire:click="$set('search', ''); $set('filterEvent', ''); $set('filterDate', '')"
                            class="btn btn-ghost btn-sm border-0 bg-transparent text-zinc-400 hover:text-zinc-700 rounded-xl">
                            <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                            Reset
                        </button>
                    @endif
                </div>

                <!-- Search -->
                <div class="relative w-full md:w-80 group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <x-heroicon-o-magnifying-glass
                            class="w-4 h-4 text-zinc-400 group-focus-within:text-indigo-500 transition-colors" />
                    </div>
                    <input type="text" placeholder="Cari aktor, target, event..."
                        wire:model.live.debounce.300ms="search"
                        class="input input-sm w-full pl-11 bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20" />
                </div>
            </div>

            @if ($logs->isEmpty())
                <div class="flex flex-col items-center justify-center py-24 gap-5 text-zinc-400">
                    <div
                        class="w-20 h-20 rounded-full bg-zinc-50 dark:bg-zinc-800/50 flex items-center justify-center border border-zinc-100 dark:border-zinc-800">
                        <x-heroicon-o-clipboard-document-list class="w-10 h-10 text-zinc-300 dark:text-zinc-600" />
                    </div>
                    <div class="text-center">
                        <p class="text-base font-bold text-zinc-500 dark:text-zinc-400">Belum ada log aktivitas</p>
                        <p class="text-sm text-zinc-400 dark:text-zinc-500 mt-1">Log akan muncul saat ada perubahan hak
                            akses.</p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table table-sm w-full">
                        <thead>
                            <tr
                                class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider text-[10px]">
                                <th class="py-4 pl-4">Waktu</th>
                                <th class="py-4">Aktor</th>
                                <th class="py-4">Event</th>
                                <th class="py-4">Target</th>
                                <th class="py-4 pr-4">Detail</th>
                            </tr>
                        </thead>
                        @foreach ($logs as $log)
                            <tbody x-data="{ open: false }"
                                class="border-t-0 divide-y divide-zinc-100 dark:divide-zinc-800/50">
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                    <!-- Waktu -->
                                    <td class="py-3 pl-4 whitespace-nowrap">
                                        <div class="font-bold text-xs text-zinc-700 dark:text-zinc-300">
                                            {{ $log->created_at->format('d M Y') }}
                                        </div>
                                        <div class="text-[10px] text-zinc-400 font-mono">
                                            {{ $log->created_at->format('H:i:s') }}</div>
                                    </td>

                                    <!-- Aktor -->
                                    <td class="py-3">
                                        @if ($log->actor)
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-7 h-7 rounded-full bg-linear-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white text-[10px] font-black shrink-0">
                                                    {{ substr($log->actor->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200">
                                                        {{ $log->actor->name }}</p>
                                                    <p class="text-[10px] text-zinc-400">{{ $log->actor->email }}</p>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-zinc-400 text-xs">—</span>
                                        @endif
                                    </td>

                                    <!-- Event badge -->
                                    <td class="py-3">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider border {{ AuditLogger::badgeColor($log->event) }}">
                                            {{ AuditLogger::label($log->event) }}
                                        </span>
                                    </td>

                                    <!-- Target -->
                                    <td class="py-3">
                                        @if ($log->targetUser)
                                            <p class="text-xs font-bold text-zinc-700 dark:text-zinc-300">
                                                {{ $log->targetUser->name }}</p>
                                            <p class="text-[10px] text-zinc-400">{{ $log->targetUser->email }}</p>
                                        @elseif(!empty($log->properties['role']))
                                            <span class="text-xs text-zinc-500">Role:
                                                <strong>{{ $log->properties['role'] }}</strong></span>
                                        @else
                                            <span class="text-zinc-400 text-xs">—</span>
                                        @endif
                                    </td>

                                    <!-- Detail toggle -->
                                    <td class="py-3 pr-4">
                                        <button @click="open = !open"
                                            class="btn btn-ghost btn-xs rounded-lg text-zinc-500 hover:text-indigo-600 gap-1">
                                            <x-heroicon-o-chevron-down class="w-3.5 h-3.5 transition-transform"
                                                x-bind:class="open ? 'rotate-180' : ''" />
                                            Detail
                                        </button>
                                    </td>
                                </tr>

                                <!-- Expanded detail row -->
                                <tr x-show="open" style="display:none" class="bg-zinc-50/50 dark:bg-zinc-800/20">
                                    <td colspan="5" class="px-4 pb-3 pt-1">
                                        <pre
                                            class="text-xs text-zinc-600 dark:text-zinc-300 bg-white dark:bg-zinc-900 rounded-xl p-3 overflow-x-auto border border-zinc-200 dark:border-zinc-700 font-mono leading-relaxed">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </td>
                                </tr>
                            </tbody>
                        @endforeach
                    </table>
                </div>

                @if ($logs->hasPages())
                    <div class="px-5 py-4 border-t border-zinc-100 dark:border-zinc-800">
                        {{ $logs->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
