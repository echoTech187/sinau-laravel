<x-layouts::app>
    <div class="p-6 max-w-7xl mx-auto animate-fade-in-up">

        <header class="mb-6 flex items-center gap-3">
            <div class="p-2.5 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/30">
                <flux:icon.banknotes class="w-6 h-6 text-white" />
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Daftar Transaksi</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Simulasi Row-Level Security: Data hanya akan tampil sesuai izin Region Anda.
                </p>
            </div>
        </header>

        <div
            class="bg-white/60 dark:bg-zinc-800/60 backdrop-blur-xl rounded-2xl shadow-sm border border-zinc-200/50 dark:border-zinc-700/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead
                        class="bg-zinc-50/50 dark:bg-zinc-800/50 border-b border-zinc-200/50 dark:border-zinc-700/50 text-zinc-600 dark:text-zinc-300">
                        <tr>
                            <th class="px-6 py-4 font-medium">No. Invoice</th>
                            <th class="px-6 py-4 font-medium">Cabang (Branch)</th>
                            <th class="px-6 py-4 font-medium">Nominal</th>
                            <th class="px-6 py-4 font-medium">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/50 dark:divide-zinc-700/50">
                        @if ($transactions && count($transactions) > 0)
                            @foreach ($transactions as $t)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/30 transition-colors">
                                    <td class="px-6 py-4 font-mono text-zinc-900 dark:text-white">
                                        {{ $t->invoice_number }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 font-medium text-xs">
                                            {{ $t->branch->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-zinc-900 dark:text-zinc-100">
                                        Rp {{ number_format($t->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">
                                        {{ $t->description }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-zinc-500">
                                    <flux:icon.shield-exclamation class="w-10 h-10 mx-auto mb-3 text-zinc-400" />
                                    <p class="text-base text-zinc-800 dark:text-zinc-200 font-medium">Tidak Ada Data</p>
                                    <p class="mt-1">Tidak ada transaksi yang bisa ditampilkan. Anda mungkin tidak
                                        memiliki izin (Data Scope) untuk cabang mana pun.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div
                class="bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200/50 dark:border-zinc-700/50 px-6 py-3 text-right text-xs text-zinc-500">
                Menampilkan total {{ $transactions->count() }} transaksi
            </div>
        </div>

    </div>
</x-layouts::app>
