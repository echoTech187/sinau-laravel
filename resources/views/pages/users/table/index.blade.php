@props([
    'data' => [],
    'columns' => [],
    'paginate' => true,
    'sortColumn' => '',
    'sortDirection' => '',
    'perPage' => 10,
    'search' => '',
    'filterable' => [],
    'filter' => [],
    'filters' => [],
    'actions' => [],
    'actionable' => false,
])

<div
    class="bg-white/60 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-sm overflow-hidden animate-fade-in-up">

    <!-- Filter & Search Row -->
    <div
        class="flex flex-col md:flex-row gap-3 items-center justify-between p-4 border-b border-zinc-100 dark:border-zinc-800">
        <!-- Left: filters + perPage -->
        <div class="flex items-center gap-2 flex-wrap w-full md:w-auto">
            @if ($filterable)
                @foreach ($filters as $filter)
                    <div
                        class="flex gap-1 p-1 bg-zinc-100/80 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/50 dark:border-zinc-700/50">
                        <select wire:model.live.debounce.300ms="filter.{{ $filter['name'] }}"
                            class="select select-sm border-0 bg-transparent focus:outline-none focus:ring-0 text-zinc-600 dark:text-zinc-300 font-medium">
                            <option value="">{{ $filter['label'] }}</option>
                            @foreach ($filter['options'] as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            @endif

            <!-- PerPage -->
            <div
                class="flex gap-1 p-1 bg-zinc-100/80 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/50 dark:border-zinc-700/50">
                <select wire:model.live.debounce.300ms="perPage"
                    class="select select-sm border-0 bg-transparent focus:outline-none focus:ring-0 text-zinc-600 dark:text-zinc-300 font-medium">
                    <option value="Semua">Semua</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="100">100</option>
                    <option value="250">250</option>
                </select>
            </div>
        </div>

        <!-- Right: Search + Action Buttons -->
        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Search -->
            <div class="relative flex-1 md:w-72 group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                    <x-heroicon-o-magnifying-glass
                        class="w-4 h-4 text-zinc-400 group-focus-within:text-indigo-500 transition-colors" />
                </div>
                <input type="text" placeholder="Cari Nama atau Email..." wire:model.live.debounce.300ms="search"
                    class="input input-sm w-full pl-11 bg-white/50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20" />
            </div>

            <!-- Import Button -->
            <button wire:click="import" title="Import Pengguna"
                class="btn btn-sm btn-ghost gap-2 rounded-xl text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 shrink-0">
                <x-heroicon-o-arrow-path class="w-4 h-4" />
                <span class="hidden sm:inline">Import</span>
            </button>

            <!-- Add User Button -->
            @can('user.manage')
                <button wire:click="create" wire:navigate title="Tambah Pengguna"
                    class="btn btn-sm bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-lg shadow-indigo-600/30 rounded-xl transition-all hover:-translate-y-0.5 font-bold shrink-0">
                    <x-heroicon-o-user-plus class="w-4 h-4" />
                    <span class="hidden sm:inline">Tambah</span>
                </button>
            @endcan
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="table table-sm w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr
                    class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider text-[10px]">
                    <th class="py-4 pl-4 w-10 text-center">
                        <x-heroicon-o-hashtag class="w-3.5 h-3.5 text-zinc-400 mx-auto" />
                    </th>

                    @foreach ($columns as $column)
                        <th class="py-4 px-4">
                            @if (isset($column['sortable']) && $column['sortable'])
                                <button wire:click="sort('{{ $column['name'] }}')"
                                    class="flex items-center gap-1.5 hover:text-indigo-600 transition-colors group">
                                    {{ $column['label'] }}
                                    @if ($sortColumn === $column['name'])
                                        <x-heroicon-s-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}
                                            class="w-3 h-3 text-indigo-500" />
                                    @else
                                        <x-heroicon-o-chevron-up-down
                                            class="w-3 h-3 text-zinc-300 opacity-0 group-hover:opacity-100 transition-opacity" />
                                    @endif
                                </button>
                            @else
                                {{ $column['label'] }}
                            @endif
                        </th>
                    @endforeach

                    <th class="py-4 px-4 text-right pr-4">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 text-sm">
                @foreach ($data as $index => $user)
                    <tr
                        class="group hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors border-b border-zinc-100 dark:border-zinc-800/50 last:border-0">
                        <!-- Row Number -->
                        <td class="py-3 pl-4 text-center text-[10px] font-bold text-zinc-400">
                            {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                        </td>

                        @foreach ($columns as $column)
                            <td class="py-3 px-4">
                                @if ($column['name'] === 'role.role')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 text-[9px] font-black uppercase tracking-widest">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ data_get($user, $column['name']) ?? 'No Role' }}
                                    </span>
                                @elseif($column['name'] === 'name')
                                    <div class="font-bold text-zinc-800 dark:text-zinc-200 text-sm tracking-tight">
                                        {{ data_get($user, $column['name']) }}
                                    </div>
                                @elseif($column['name'] === 'email')
                                    <div class="text-zinc-500 dark:text-zinc-400 text-xs font-medium">
                                        {{ data_get($user, $column['name']) }}
                                    </div>
                                @else
                                    <div class="text-zinc-500 dark:text-zinc-400 text-xs font-medium">
                                        {{ data_get($user, $column['name']) }}
                                    </div>
                                @endif
                            </td>
                        @endforeach

                        <td class="py-3 pr-4">
                            <div
                                class="flex items-center justify-end gap-1 translate-x-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300">
                                @can('user.manage')
                                    <a href="{{ route('user.permission', $user->id) }}" wire:navigate
                                        class="btn btn-ghost btn-xs btn-square hover:bg-indigo-50 dark:hover:bg-indigo-900/30 text-indigo-500 rounded-lg"
                                        title="Hak Akses">
                                        <x-heroicon-o-shield-check class="w-4 h-4" />
                                    </a>

                                    <button wire:click="edit({{ $user->id }})"
                                        class="btn btn-ghost btn-xs btn-square hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-500 rounded-lg"
                                        title="Edit User">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </button>

                                    <button wire:click="delete({{ $user->id }})"
                                        wire:confirm="Yakin ingin menghapus user ini?"
                                        class="btn btn-ghost btn-xs btn-square hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-red-500 rounded-lg"
                                        title="Hapus User">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($paginate && $data->hasPages())
        <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800">
            {{ $data->links() }}
        </div>
    @endif
</div>
