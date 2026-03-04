@props(['label', 'type' => 'text', 'placeholder' => '', 'name' => null])

@php
    $errorKey =
        $name ??
        ($attributes->get('wire:model') ??
            ($attributes->get('wire:model.live') ?? ($attributes->get('wire:model.defer') ?? '')));
@endphp

<div class="form-control w-full">
    <label class="label pt-0 pb-1.5">
        <span class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">
            {{ $label }}
        </span>
        @if (isset($action))
            {{ $action }}
        @endif
    </label>
    @if (isset($prefix) || isset($suffix))
        <div class="relative">
            @if (isset($prefix))
                <span
                    class="absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none z-10 text-sm font-medium">{{ $prefix }}</span>
            @endif
            <input type="{{ $type }}" placeholder="{{ $placeholder }}"
                {{ $attributes->merge(['class' => 'input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium placeholder:font-normal w-full' . (isset($prefix) ? ' pl-9' : '') . (isset($suffix) ? ' pr-12' : '')]) }} />
            @if (isset($suffix))
                <span
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-zinc-400 tracking-widest pointer-events-none">{{ $suffix }}</span>
            @endif
        </div>
    @else
        <input type="{{ $type }}" placeholder="{{ $placeholder }}"
            {{ $attributes->merge(['class' => 'input input-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium placeholder:font-normal']) }} />
    @endif
    @if ($errorKey)
        @error($errorKey)
            <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
        @enderror
    @endif
</div>
