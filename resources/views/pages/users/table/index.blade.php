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

<flux:heading class="sr-only">{{ __('Kelola Pengguna & Izin') }}</flux:heading>
<div class="flex items-center justify-between my-4 gap-4">
    <div class="flex items-center gap-2">

        @if ($filterable)
            <div class="flex items-center gap-2">
                @foreach ($filters as $filter)
                    <flux:select placeholder="{{ $filter['label'] }}"
                        wire:model.live.debounce.300ms="filter.{{ $filter['name'] }}">
                        <flux:select.option value="">{{ __('Semua') }}</flux:select.option>
                        @foreach ($filter['options'] as $option)
                            <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                        @endforeach
                    </flux:select>
                @endforeach
            </div>
        @endif
    </div>
    <div class="flex items-center gap-2">
        <flux:button class="max-md:gap-0!" variant="outline" icon="arrow-path" wire:click="import"
            title="Import Pengguna" data-test="import-user-button">

            <span class="hidden sm:inline">{{ __('Import Pengguna') }}</span>
        </flux:button>
        <flux:button class="max-md:gap-0!" variant="primary" icon="user-plus" wire:click="create"
            title="Tambah Pengguna" data-test="create-user-button" wire:navigate>
            <span class="hidden sm:inline">{{ __('Tambah Pengguna') }}</span>
        </flux:button>
    </div>

</div>
<div class="flex items-center justify-between my-4 gap-4">
    <div class="flex items-center gap-2">
        <flux:select placeholder="Show" wire:model.live.debounce.300ms="perPage">
            <flux:select.option>Semua</flux:select.option>
            <flux:select.option>10</flux:select.option>
            <flux:select.option>25</flux:select.option>
            <flux:select.option>100</flux:select.option>
            <flux:select.option>250</flux:select.option>
        </flux:select>
    </div>
    <div class="flex items-center gap-2">
        <flux:input icon="magnifying-glass" placeholder="Cari Nama atau Email" @style(['placeholder:text-zinc-400 placeholder:text-xs'])
            wire:model.live.debounce.300ms="search" />


    </div>
</div>
<flux:table :paginate="$paginate ? $data : null">
    <flux:table.columns>
        @foreach ($columns as $column)
            @if (isset($column['sortable']) && $column['sortable'])
                <flux:table.column sortable :sorted="$sortColumn === $column['name']" :direction="$sortDirection"
                    wire:click="sort('{{ $column['name'] }}')" key="{{ $column['label'] }}">
                    {{ $column['label'] }}
                </flux:table.column>
            @else
                <flux:table.column key="{{ $column['name'] }}">
                    {{ $column['label'] }}
                </flux:table.column>
            @endif
        @endforeach
        <flux:table.column class="text-right w-20">Aksi</flux:table.column>
    </flux:table.columns>
    <flux:table.rows>
        @foreach ($data as $user)
            <flux:table.row key="{{ $user->id }}">
                @foreach ($columns as $column)
                    <flux:table.cell>
                        <flux:text>{{ data_get($user, $column['name']) }}</flux:text>
                    </flux:table.cell>
                @endforeach

                <flux:table.cell>
                    <flux:dropdown offset="-15" gap="2">
                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom">
                        </flux:button>
                        <flux:menu>
                            <flux:menu.item :href="route('user.permission')" icon="key" wire:navigate>
                                {{ __('Permissions') }}
                            </flux:menu.item>
                            <flux:menu.item wire:click="edit({{ $user->id }})" icon="pencil" wire:navigate>
                                {{ __('Edit') }}
                            </flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                </flux:table.cell>
            </flux:table.row>
        @endforeach
    </flux:table.rows>
</flux:table>
