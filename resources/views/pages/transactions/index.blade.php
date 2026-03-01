<?php

use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Facades\Gate;

new class extends Component {
    public function with(): array
    {
        return [
            // Kueri berjalan normal tanpa ada 'where(branch_id, ...)'
            'transactions' => Transaction::with('branch')->latest()->get(),
        ];
    }
};
?>
<div>
    <div class="p-6 max-w-7xl mx-auto animate-fade-in-up">

        <header class="mb-8 flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="p-3 rounded-2xl bg-linear-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/30">
                    <x-heroicon-o-banknotes class="w-7 h-7 text-white" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white leading-tight">Daftar
                        Transaksi</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Row-Level Security Active: Data difilter sesuai regional Anda.
                    </p>
                </div>
            </div>
            <div class="sm:ml-auto">
                <a href="{{ route('dashboard') }}" wire:navigate
                    class="btn btn-sm btn-ghost gap-2 rounded-xl text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all hover:-translate-x-1">
                    <x-heroicon-o-arrow-left class="size-4" />
                    Kembali
                </a>
            </div>
        </header>

        <div
            class="bg-white/60 dark:bg-zinc-900/60 backdrop-blur-xl rounded-3xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">No.
                                Invoice</th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">Cabang
                                (Branch)</th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                                Nominal</th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                                Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @if ($transactions && count($transactions) > 0)
                            @foreach ($transactions as $t)
                                <tr
                                    class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors border-b border-zinc-100 dark:border-zinc-800/50 last:border-0 group">
                                    <td class="px-6 py-4 font-bold font-mono text-zinc-900 dark:text-white">
                                        {{ $t->invoice_number }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 font-bold text-[10px] uppercase tracking-tighter border border-indigo-100 dark:border-indigo-500/20">
                                            {{ $t->branch->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if (auth()->user()->isFieldHidden('App\Models\Transaction', 'amount'))
                                            <span
                                                class="inline-flex items-center gap-2 text-xs font-bold font-mono text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded-md"
                                                title="Data Disensor">
                                                <x-heroicon-o-eye-slash class="w-3.5 h-3.5" />
                                                Rp ••••••••
                                            </span>
                                        @else
                                            <span class="text-sm font-bold text-zinc-900 dark:text-zinc-100">
                                                Rp {{ number_format($t->amount, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400 italic">
                                        {{ $t->description }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-6 py-20 text-center">
                                    <div class="mb-4 flex justify-center">
                                        <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-2xl">
                                            <x-heroicon-o-shield-exclamation
                                                class="w-12 h-12 text-zinc-300 dark:text-zinc-600" />
                                        </div>
                                    </div>
                                    <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Tidak Ada Data</h3>
                                    <p
                                        class="mt-2 text-sm text-zinc-500 dark:text-zinc-500 max-w-sm mx-auto leading-relaxed">
                                        Tidak ada transaksi yang bisa ditampilkan. Anda mungkin tidak memiliki izin
                                        jangkauan data untuk cabang mana pun.
                                    </p>
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
</div>

<x-slot:title>Keamanan Baris (Data Scoping)</x-slot:title>
