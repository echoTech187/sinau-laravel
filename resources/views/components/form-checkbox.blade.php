@props(['label', 'description' => null, 'toggle' => false, 'name' => null])

@php
    $errorKey =
        $name ??
        ($attributes->get('wire:model') ??
            ($attributes->get('wire:model.live') ?? ($attributes->get('wire:model.defer') ?? '')));
    $inputClass = $toggle
        ? 'toggle toggle-primary toggle-sm'
        : 'checkbox checkbox-sm checkbox-primary rounded-lg mt-0.5';
@endphp

<label
    class="flex items-start gap-4 p-4 rounded-2xl bg-white/50 dark:bg-zinc-950/50 hover:bg-zinc-50 dark:hover:bg-zinc-800/80 cursor-pointer transition-all border border-zinc-200 dark:border-zinc-800">
    <input type="checkbox" {{ $attributes->merge(['class' => $inputClass]) }} />
    <div class="flex flex-col">
        <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ $label }}</span>
        @if ($description)
            <span class="text-[10px] text-zinc-400 leading-tight mt-1">{{ $description }}</span>
        @endif
    </div>
    @if ($errorKey)
        @error($errorKey)
            <span class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</span>
        @enderror
    @endif
</label>
