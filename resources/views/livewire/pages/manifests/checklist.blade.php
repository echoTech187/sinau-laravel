<div class="container relative min-h-screen pb-10">
    <!-- Decorative Background Blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div
            class="absolute top-0 right-0 w-[600px] h-[600px] bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-1/4 w-[500px] h-[500px] bg-purple-500/10 dark:bg-purple-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto space-y-5">
        <!-- Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-700/50 pb-4 animate-fade-in">
            <div class="flex items-center gap-3">
                <a href="{{ route('manifests.index') }}"
                    class="w-9 h-9 flex items-center justify-center bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm hover:border-indigo-500 hover:text-indigo-600 transition-all group shrink-0">
                    <x-heroicon-o-arrow-left class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" />
                </a>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-2">
                        <div
                            class="p-2 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/30">
                            <x-heroicon-s-clipboard-document-check class="w-5 h-5 text-white" />
                        </div>
                        Inspeksi P2H: <span class="text-indigo-600">{{ $manifest->manifest_number }}</span>
                    </h1>
                    <div class="flex items-center gap-2 mt-1 ml-9">
                        <span
                            class="text-[10px] font-black bg-indigo-600 text-white px-2 py-0.5 rounded-md uppercase tracking-[0.15em]">{{ $manifest->schedule->bus->fleet_code }}</span>
                        <span
                            class="text-xs text-zinc-400 font-bold uppercase">{{ $manifest->schedule->route->name }}</span>
                    </div>
                </div>
            </div>

            <div class="shrink-0">
                @php
                    $statusConfig = match ($manifest->status->value) {
                        'draft' => [
                            'bg' => 'bg-amber-100/80',
                            'text' => 'text-amber-700',
                            'border' => 'border-amber-200/50',
                            'dot' => 'bg-amber-500',
                        ],
                        'approved' => [
                            'bg' => 'bg-emerald-100/80',
                            'text' => 'text-emerald-700',
                            'border' => 'border-emerald-200/50',
                            'dot' => 'bg-emerald-500',
                        ],
                        'rejected' => [
                            'bg' => 'bg-rose-100/80',
                            'text' => 'text-rose-700',
                            'border' => 'border-rose-200/50',
                            'dot' => 'bg-rose-500',
                        ],
                        default => [
                            'bg' => 'bg-zinc-100/80 dark:bg-zinc-800/50',
                            'text' => 'text-zinc-700 dark:text-zinc-300',
                            'border' => 'border-zinc-200/50 dark:border-zinc-700/50',
                            'dot' => 'bg-zinc-500',
                        ],
                    };
                @endphp
                <div
                    class="px-4 py-2 rounded-xl text-[10px] font-black border flex items-center gap-2 {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}">
                    <span class="w-2 h-2 rounded-full {{ $statusConfig['dot'] }} animate-pulse"></span>
                    STATUS: {{ strtoupper($manifest->status->value) }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            <!-- Sidebar: Categories Navigation -->
            <div class="lg:col-span-3 space-y-4">
                <div
                    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm overflow-hidden">
                    <div
                        class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                        <span class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">Divisi
                            Checklist</span>
                    </div>
                    <nav class="space-y-1 p-2">
                        @foreach ($this->categories as $category)
                            @php
                                $approval = $manifest->approvals->where('category_id', $category->id)->first();
                                $isApproved = $approval && $approval->status->value === 'approved';
                                $isRejected = $approval && $approval->status->value === 'rejected';
                                $isActive = $selectedCategoryId == $category->id;
                            @endphp
                            <button wire:click="selectCategory({{ $category->id }})"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl border transition-all duration-300 relative overflow-hidden group/item {{ $isActive ? 'bg-zinc-900 dark:bg-indigo-600 border-zinc-900 dark:border-indigo-600 text-white shadow-md' : 'bg-transparent border-transparent text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800 hover:border-zinc-100 dark:hover:border-zinc-700' }}">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ $isActive ? 'bg-white/10' : 'bg-zinc-100 dark:bg-zinc-700' }}">
                                        @if ($isApproved)
                                            <x-heroicon-s-check-badge class="w-4 h-4 text-emerald-500" />
                                        @elseif($isRejected)
                                            <x-heroicon-s-x-circle class="w-4 h-4 text-rose-500" />
                                        @else
                                            <x-heroicon-o-queue-list
                                                class="w-4 h-4 {{ $isActive ? 'text-white' : 'text-zinc-400 dark:text-zinc-500' }}" />
                                        @endif
                                    </div>
                                    <div class="text-left">
                                        <p class="font-bold text-xs leading-none mb-0.5">{{ $category->name }}</p>
                                        <p
                                            class="text-[9px] font-black uppercase tracking-widest {{ $isActive ? 'text-white/50' : 'text-zinc-400' }}">
                                            {{ $isApproved ? 'TERVERIFIKASI' : ($isRejected ? 'DITOLAK' : 'MENUNGGU') }}
                                        </p>
                                    </div>
                                </div>
                                <x-heroicon-o-chevron-right
                                    class="w-3.5 h-3.5 shrink-0 {{ $isActive ? 'text-white' : 'text-zinc-300 dark:text-zinc-600' }}" />
                            </button>
                        @endforeach
                    </nav>
                </div>

                <!-- Info Help Card -->
                <div class="bg-indigo-600 border border-indigo-500 rounded-2xl p-5 text-white relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="relative z-10 space-y-2">
                        <div class="w-8 h-8 bg-white/10 rounded-xl flex items-center justify-center">
                            <x-heroicon-o-information-circle class="w-5 h-5" />
                        </div>
                        <p class="text-xs font-bold leading-relaxed opacity-80">
                            Item <span class="text-rose-300 font-black">🚨 KRITIS</span> adalah syarat mutlak.
                            Jika gagal, armada <span class="text-rose-300 font-black">WAJIB GROUNDED</span>.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Content: Inspection Items List -->
            <div
                class="lg:col-span-9 flex flex-col bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm overflow-hidden">
                <!-- Content Header -->
                <div
                    class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-black text-zinc-900 dark:text-white tracking-tight">
                            {{ $this->categories->find($selectedCategoryId)?->name }}
                        </h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-0.5">Detailed Safety
                            Protocol</p>
                    </div>
                    <div
                        class="flex items-center gap-3 bg-white dark:bg-zinc-950 px-3 py-2 rounded-xl border border-zinc-100 dark:border-zinc-800 shrink-0">
                        <div class="text-right">
                            <p
                                class="text-[9px] text-zinc-400 font-black uppercase tracking-widest leading-none mb-0.5">
                                Pass Score</p>
                            <p class="text-lg font-black text-indigo-600 leading-none tabular-nums">
                                {{ $this->categories->find($selectedCategoryId)?->min_passing_percentage }}%
                            </p>
                        </div>
                        <x-heroicon-o-shield-check class="w-5 h-5 text-indigo-500" />
                    </div>
                </div>

                <!-- Items List -->
                <div class="p-4 flex-1 space-y-3 overflow-y-auto">
                    @if (count($this->currentItems) > 0)
                        @foreach ($this->currentItems as $index => $item)
                            @php $resultVal = $responses[$item->id]['result'] ?? ''; @endphp
                            <div
                                class="p-4 bg-white/80 dark:bg-zinc-900/30 rounded-xl border transition-all duration-300 relative group/card {{ $resultVal === 'fail' ? 'border-rose-200 dark:border-rose-500/20 bg-rose-50/10' : ($resultVal === 'pass' ? 'border-emerald-200 dark:border-emerald-500/20' : 'border-zinc-100 dark:border-zinc-800') }}">

                                @if ($item->is_critical)
                                    <div
                                        class="absolute top-0 right-6 -translate-y-1/2 px-3 py-0.5 bg-rose-600 text-white text-[9px] font-black uppercase tracking-widest rounded-full z-20">
                                        🚨 KRITIS
                                    </div>
                                @endif

                                <div class="flex flex-col gap-3">
                                    <!-- Item Header -->
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="w-6 h-6 rounded-md bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 flex items-center justify-center text-[10px] font-black shrink-0">
                                            {{ $index + 1 }}
                                        </span>
                                        <h3
                                            class="text-sm font-black text-zinc-900 dark:text-white tracking-tight leading-tight flex-1 group-hover/card:text-indigo-600 transition-colors">
                                            {{ $item->item_name }}
                                        </h3>
                                        <span
                                            class="text-[9px] font-black text-zinc-400 uppercase tracking-widest shrink-0">{{ $item->max_score }}
                                            pts</span>
                                    </div>

                                    <!-- Radio Buttons -->
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach (\App\Enums\ManifestResult::cases() as $res)
                                            @php
                                                $isSelected = $resultVal === $res->value;
                                                $colorStyles = match ($res->value) {
                                                    'pass' => $isSelected
                                                        ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/20 ring-2 ring-emerald-100 dark:ring-emerald-500/10'
                                                        : 'bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-400 hover:border-emerald-400 hover:text-emerald-600',
                                                    'pass_with_note' => $isSelected
                                                        ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/20 ring-2 ring-amber-100 dark:ring-amber-500/10'
                                                        : 'bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-400 hover:border-amber-400 hover:text-amber-600',
                                                    'fail' => $isSelected
                                                        ? 'bg-rose-600 text-white shadow-sm shadow-rose-500/20 ring-2 ring-rose-100 dark:ring-rose-500/10'
                                                        : 'bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-400 hover:border-rose-400 hover:text-rose-600',
                                                    'n_a' => $isSelected
                                                        ? 'bg-zinc-600 text-white shadow-sm ring-2 ring-zinc-100 dark:ring-zinc-500/10'
                                                        : 'bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-400 hover:border-zinc-400 hover:text-zinc-600',
                                                };
                                            @endphp
                                            <label class="cursor-pointer">
                                                <input type="radio"
                                                    wire:model.live="responses.{{ $item->id }}.result"
                                                    value="{{ $res->value }}" class="sr-only">
                                                <div
                                                    class="h-9 flex items-center justify-center border-2 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all duration-300 {{ $colorStyles }}">
                                                    {{ $res->value === 'pass_with_note' ? 'NOTA' : ($res->value === 'n_a' ? 'N/A' : strtoupper($res->name)) }}
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>

                                    @if ($resultVal === 'pass_with_note' || $resultVal === 'fail')
                                        <div class="pt-3 border-t border-dashed border-zinc-100 dark:border-zinc-800">
                                            <textarea wire:model.blur="responses.{{ $item->id }}.notes" placeholder="Jelaskan kondisi detail..."
                                                class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-3 py-2 text-xs font-bold text-zinc-700 dark:text-zinc-300 placeholder-zinc-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none h-20 resize-none transition-all"></textarea>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="flex flex-col items-center justify-center py-20 opacity-30 gap-4">
                            <x-heroicon-o-document-magnifying-glass class="w-14 h-14" />
                            <p class="text-sm font-black uppercase tracking-widest">Checklist Kosong</p>
                        </div>
                    @endif
                </div>

                <!-- Footer Action -->
                <div
                    class="px-5 py-4 bg-zinc-900 dark:bg-indigo-950 border-t border-zinc-800 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 bg-white/5 rounded-xl flex items-center justify-center border border-white/10">
                            <x-heroicon-o-finger-print class="text-indigo-400 w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Konfirmasi
                                Digital</p>
                            <p class="text-[10px] text-zinc-500">ID Staff & Timestamp dicatat otomatis.</p>
                        </div>
                    </div>
                    <button wire:click="saveChecklist" wire:loading.attr="disabled"
                        class="btn btn-sm bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/30 rounded-xl font-bold px-6 transition-all disabled:opacity-50">
                        <span wire:loading.remove>
                            <x-heroicon-o-check-circle class="w-4 h-4 inline -mt-0.5" />
                            Submit Inspeksi
                        </span>
                        <span wire:loading class="loading loading-spinner loading-xs"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
