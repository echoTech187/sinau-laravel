@props(['label', 'placeholder' => '', 'rows' => 4, 'name' => null])

@php
    $errorKey =
        $name ??
        ($attributes->get('wire:model') ??
            ($attributes->get('wire:model.live') ?? ($attributes->get('wire:model.defer') ?? '')));
@endphp

<div class="form-control w-full">
    <label class="label pt-0 pb-1.5 flex items-center justify-between">
        <span class="label-text text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">
            {{ $label }}
        </span>
        @if (isset($action))
            <span class="ml-auto">{{ $action }}</span>
        @endif
    </label>
    <textarea placeholder="{{ $placeholder }}" rows="{{ $rows }}"
        {{ $attributes->merge(['class' => 'textarea textarea-bordered bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all leading-relaxed text-sm']) }}></textarea>
    @if ($errorKey)
        @error($errorKey)
            <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
        @enderror
    @endif
</div>
