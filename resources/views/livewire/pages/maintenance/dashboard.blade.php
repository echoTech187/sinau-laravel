@php
    /** @var \App\Livewire\Pages\Maintenance\Dashboard $this */
@endphp

<div class="relative min-h-full">
    <!-- Decorative Background Blob -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-150 h-150 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[100px]">
        </div>
        <div
            class="absolute bottom-0 left-1/4 w-125 h-125 bg-purple-500/10 dark:bg-purple-500/5 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 space-y-8">
        <!-- Header -->
        <div class="animate-fade-in-up">
            <header
                class="flex flex-wrap items-center justify-between gap-6 border-b border-zinc-200 dark:border-zinc-700/50 pb-6">
                <div class="flex-1 min-w-[280px]">
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <div
                            class="p-2.5 rounded-2xl bg-linear-to-br from-indigo-500 to-blue-600 shadow-lg shadow-indigo-500/30">
                            <x-heroicon-o-wrench class="w-6 h-6 text-white" />
                        </div>
                        Maintenance Dashboard
                    </h1>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                        Pantau kesehatan armada dan peringatan jatuh tempo dokumen resmi secara real-time.
                    </p>
                </div>
                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center justify-end gap-3 w-full sm:w-auto">
                    <a wire:navigate href="{{ route('maintenance.logs') }}"
                        class="btn btn-sm sm:btn-md bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-2xl shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-all font-bold">
                        <x-heroicon-o-clipboard-document-check class="w-5 h-5 text-indigo-500" />
                        Riwayat Perawatan
                    </a>
                </div>
            </header>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 animate-fade-in-up" style="animation-delay: 0.1s">
            <!-- Left Side: Maintenance Alerts -->
            <div
                class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm overflow-hidden flex flex-col">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 rounded-xl bg-amber-500/10 text-amber-500">
                        <x-heroicon-o-bell-alert class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-zinc-900 dark:text-white">Peringatan Perawatan</h2>
                        <p class="text-xs text-zinc-500">Bus yang membutuhkan perhatian mekanik</p>
                    </div>
                </div>

                @if (empty($busHealth))
                    <div class="flex-1 flex flex-col items-center justify-center py-12 text-center">
                        <div class="size-20 bg-green-500/10 rounded-full flex items-center justify-center mb-4">
                            <x-heroicon-o-check-circle class="w-10 h-10 text-green-500" />
                        </div>
                        <p class="font-bold text-zinc-900 dark:text-white">Armada Sehat</p>
                        <p class="text-xs text-zinc-500 mt-1">Semua armada dalam kondisi optimal.</p>
                    </div>
                @else
                    <div class="space-y-4 overflow-y-auto max-h-[500px] pr-2 custom-scrollbar">
                        @foreach ($busHealth as $item)
                            <div
                                class="bg-zinc-50/50 dark:bg-zinc-800/20 border border-zinc-200/50 dark:border-zinc-700/50 rounded-2xl overflow-hidden transition-all hover:border-indigo-500/30">
                                <div class="p-4 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="size-10 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center font-bold text-xs">
                                            {{ $item['bus']->fleet_code }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-zinc-900 dark:text-white text-sm">
                                                {{ $item['bus']->plate_number }}</p>
                                            <p class="text-[10px] text-zinc-500 uppercase font-bold tracking-widest">
                                                {{ $item['bus']->chassis_brand }}</p>
                                        </div>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase tracking-wider 
                                        {{ $item['worst_status'] == 'overdue'
                                            ? 'bg-red-50 text-red-600 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20'
                                            : 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20' }}">
                                        {{ $item['worst_status'] }}
                                    </span>
                                </div>
                                <div class="px-4 pb-4">
                                    <div class="border-t border-zinc-100 dark:border-zinc-800 pt-3 space-y-3">
                                        @foreach ($item['rules'] as $rule)
                                            @if ($rule['status'] !== 'healthy')
                                                <div
                                                    class="flex flex-col gap-1.5 p-3 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700/50 shadow-sm transition-all hover:bg-zinc-50 dark:hover:bg-zinc-700/30">
                                                    <div class="flex items-center justify-between">
                                                        <span
                                                            class="text-sm font-bold text-zinc-900 dark:text-white">{{ $rule['task_name'] }}</span>
                                                        <x-badge :value="ucfirst($rule['status'])" :color="$rule['status'] == 'overdue' ? 'error' : 'warning'" size="xs" />
                                                    </div>

                                                    <div
                                                        class="flex items-center justify-between text-[10px] text-zinc-500 font-medium">
                                                        <div class="flex items-center gap-1">
                                                            <x-heroicon-o-clock class="w-3 h-3" />
                                                            Estimasi: {{ $rule['estimated_hours'] }} Jam
                                                        </div>
                                                        <div class="flex items-center gap-1 font-mono">
                                                            Sisa: {{ number_format($rule['remaining_km']) }} KM
                                                        </div>
                                                    </div>

                                                    @if ($rule['preferred_agent'])
                                                        <div
                                                            class="flex items-center gap-1 text-[10px] text-indigo-500 font-bold border-t border-zinc-50 dark:border-zinc-700/50 pt-2 mt-1">
                                                            <x-heroicon-o-map-pin class="w-3 h-3" />
                                                            Rekomendasi: {{ $rule['preferred_agent'] }}
                                                        </div>
                                                    @endif

                                                    <div
                                                        class="w-full h-1.5 bg-zinc-100 dark:bg-zinc-900 rounded-full overflow-hidden mt-1">
                                                        @php
                                                            $percent = max(
                                                                0,
                                                                min(
                                                                    100,
                                                                    ($rule['remaining_km'] / $rule['interval']) * 100,
                                                                ),
                                                            );
                                                            $barColor =
                                                                $rule['status'] == 'overdue'
                                                                    ? 'bg-red-500'
                                                                    : 'bg-amber-500';
                                                        @endphp
                                                        <div class="{{ $barColor }} h-full transition-all duration-500"
                                                            style="width: {{ 100 - $percent }}%"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="mt-4 flex justify-end">
                                        <a href="{{ route('maintenance.logs', ['bus_id' => $item['bus']->id]) }}"
                                            class="btn btn-xs bg-indigo-50 dark:bg-indigo-500/10 border-0 text-indigo-600 dark:text-indigo-400 rounded-lg font-bold hover:bg-indigo-100 dark:hover:bg-indigo-500/20">
                                            <x-heroicon-o-pencil-square class="w-3.5 h-3.5" />
                                            Catat Perawatan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right Side: Document Expiry Alerts -->
            <div
                class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm overflow-hidden flex flex-col">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 rounded-xl bg-indigo-500/10 text-indigo-500">
                        <x-heroicon-o-document-duplicate class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-zinc-900 dark:text-white">Jatuh Tempo Dokumen</h2>
                        <p class="text-xs text-zinc-500">STNK, KIR, dan KPS yang akan segera habis</p>
                    </div>
                </div>

                @if (empty($expiringDocs))
                    <div class="flex-1 flex flex-col items-center justify-center py-12 text-center">
                        <div class="size-20 bg-blue-500/10 rounded-full flex items-center justify-center mb-4">
                            <x-heroicon-o-check-badge class="w-10 h-10 text-blue-500" />
                        </div>
                        <p class="font-bold text-zinc-900 dark:text-white">Dokumen Aman</p>
                        <p class="text-xs text-zinc-500 mt-1">Belum ada dokumen yang mendekati jatuh tempo.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="table table-sm w-full">
                            <thead>
                                <tr
                                    class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider text-[10px]">
                                    <th class="py-4">Fleet</th>
                                    <th class="py-4">Dokumen</th>
                                    <th class="py-4 text-right">Jatuh Tempo</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @foreach ($expiringDocs as $doc)
                                    <tr class="border-b border-zinc-100 dark:border-zinc-800/50">
                                        <td class="py-3 font-bold text-zinc-900 dark:text-white">
                                            {{ $doc['fleet_code'] }}</td>
                                        <td class="py-3">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach (explode(', ', $doc['documents']) as $d)
                                                    <span
                                                        class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-[9px] font-bold text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                                        {{ $d }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="py-3 text-right">
                                            <div class="flex flex-col items-end">
                                                <span
                                                    class="font-bold text-red-500 text-xs">{{ \Carbon\Carbon::parse($doc['min_expiry'])->format('d M Y') }}</span>
                                                <span
                                                    class="text-[10px] text-zinc-500">{{ \Carbon\Carbon::parse($doc['min_expiry'])->diffForHumans() }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
