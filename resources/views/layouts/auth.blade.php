<x-layouts::auth.custom :title="$title ?? null" :description="$description ?? null">
    {{ $slot }}
    @if ($chart ?? false)
        <script src="{{ $chart->cdn() }}"></script>
        {{ $chart->script() }}
    @endif
</x-layouts::auth.custom>
