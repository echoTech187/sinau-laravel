@php
    $user = Auth::user();
    $chart = new App\Charts\MonthlyUsersChart(new ArielMejiaDev\LarapexCharts\BarChart());
    $chart = $chart->build();
@endphp

<x-layouts::app :title="__('Dashboard')">
    <div class="flex items-stretch justify-start gap-8 mb-8 flex-wrap">
        {{-- Profile --}}
        <div
            class="w-full flex flex-col justify-between gap-6 mb-4  rounded-2xl items-start border-gray-100 dark:bg-neutral-primary-soft dark:border-neutral-700/70 max-w-full rounded-base">
            <div class="relative flex flex-col items-start justify-center w-full p-8">
                <img class="absolute top-0 left-0 z-1 bottom-0 rounded-2xl right-0 w-full h-full object-cover object-top text-gray-300 dark:text-gray-700"
                    src="/images/background/1.jpg" alt="">
                <div class="z-3 flex items-center gap-4">
                    <img class="object-cover rounded-full h-24 w-24 mb-4 border-4 border-white dark:border-neutral-700"
                        src="/images/avatar/avatar-1.jpg" alt="">
                    <div>
                        <h5 class="text-2xl font-bold tracking-tight text-heading leading-8">{{ $user['name'] }}
                        </h5>
                        <p class="text-body mb-4 ">{{ $user['email'] }}</p>
                        <div>
                            <button type="button"
                                class="inline-flex items-center w-fit gap-2 cursor-pointer bg-white text-black/70 rounded-lg  border ring-0 border-gray-200/70 dark:bg-neutral-primary-soft dark:border-neutral-700/70 hover:bg-white hover:text-black/70 hover:backdrop-blur-3xl hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-xs px-3 py-1.5 focus:outline-none">
                                <x-heroicon-s-pencil-square class="w-5 h-5 text-black/60" />
                                Ubah Profil

                            </button>
                        </div>
                    </div>


                </div>
            </div>
            <div class="relative flex flex-col xl:flex-row items-start gap-6 w-full rounded-2xl z-2">

                <div
                    class="relative z-3 flex flex-col items-start justify-center w-full px-6 py-3 bg-gray-50 border border-gray-100 text-[#0094d4]  rounded-xl">
                    <div class="flex items-stretch justify-between gap-4 w-full">
                        <div>
                            <h1 class="font-semibold tracking-tight text-heading leading-8">Projek Aktif</h1>
                            <div
                                class="text-2xl font-normal tracking-tight text-heading leading-8 flex items-center gap-2 w-full text-black/70 dark:text-white/70">
                                18
                                <span class="text-sm flex items-center text-[#16ce41]">
                                    <x-heroicon-c-chevron-up class="w-5 h-5 " /> +1
                                </span>
                            </div>
                            <span class="font-bold tracking-tight text-heading leading-8">Februari 2025</span>
                        </div>
                        <div flex class="flex items-center justify-end gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-12 lg:size-18 opacity-40">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                        </div>
                    </div>

                </div>

                <div
                    class="relative z-3 flex items-start justify-center w-full px-6 py-3 text-[#f59d39] bg-gray-50 border border-gray-100 rounded-xl">
                    <div class="flex items-stretch justify-between gap-4 w-full">
                        <div>
                            <h1 class="font-semibold tracking-tight text-heading leading-8">Total Permintaan APIs</h1>
                            <div
                                class="text-2xl font-normal tracking-tight text-heading leading-8 flex items-center gap-2 w-full text-black/70 dark:text-white/70">
                                125K
                                <span class="text-sm flex items-center text-[#f20101]">
                                    <x-heroicon-c-chevron-down class="w-5 h-5" /> -1%
                                </span>
                            </div>
                            <span class="font-bold tracking-tight text-heading leading-8">Februari 2025</span>
                        </div>
                        <div flex class="flex items-center justify-end gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-12 lg:size-18 opacity-40">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                            </svg>
                        </div>
                    </div>

                </div>

                <div
                    class="relative z-3 flex flex-col items-start justify-center w-full px-6 py-3 text-[#e82b71] bg-gray-50 border border-gray-100 rounded-xl">
                    <div class="flex items-stretch justify-between gap-4 w-full">
                        <div>
                            <h1 class="font-semibold tracking-tight text-heading leading-8">Jumlah Tim Aktif</h1>
                            <p
                                class="text-2xl font-normal tracking-tight text-heading leading-8 flex items-center gap-2 w-full text-black/70 dark:text-white/70">
                                8 Tim</p>
                            <span class="font-bold tracking-tight text-heading leading-8">Semua Projek</span>
                        </div>
                        <div flex class="flex items-center justify-end gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-12 lg:size-18 opacity-40">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                        </div>
                    </div>

                </div>
            </div>

        </div>


        <div class="w-full bg-white shadow-2xl shadow-gray-100 rounded-2xl">
            <div class="w-full block max-w-full px-6 pt-6">
                <h5 class="mb-3 text-2xl font-semibold tracking-tight text-heading leading-8">Grafik Aktifitas
                    Pengembangan
                    Februari 2025</h5>
                <p class="text-body">Grafik Performa Aplikasi Februari 2025</p>
            </div>
            <div class="w-full px-6 pb-6 overflow-hidden relative">
                {!! $chart->container() !!}
            </div>
        </div>
        <a href="#"
            class="flex flex-col items-center flex-2 bg-neutral-primary-soft p-6 border border-default rounded-base shadow-xs">
            <img class="object-cover w-full rounded-base h-64 md:h-auto md:w-48 mb-4 md:mb-0"
                src="/docs/images/blog/image-4.jpg" alt="">
            <div class="flex flex-col justify-between md:p-4 leading-normal">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-heading">Streamlining your design process today.
                </h5>
                <p class="mb-6 text-body">In today’s fast-paced digital landscape, fostering seamless collaboration
                    among
                    Developers and IT Operations.</p>
                <div>
                    <button type="button"
                        class="inline-flex items-center w-auto text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                        Read more
                        <svg class="w-4 h-4 ms-1.5 rtl:rotate-180 -me-0.5" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 12H5m14 0-4 4m4-4-4-4" />
                        </svg>
                    </button>
                </div>
            </div>
        </a>
        <a href="#"
            class="flex-2 max-w-full bg-neutral-primary-soft block  p-6 border border-default rounded-base shadow-xs hover:bg-neutral-secondary-medium">
            <h5 class="mb-3 text-2xl font-semibold tracking-tight text-heading leading-8">Noteworthy technology
                acquisitions 2021</h5>
            <p class="text-body">Here are the biggest technology acquisitions of 2025 so far, in reverse chronological
                order.</p>
        </a>


    </div>
    @if ($chart ?? false)
        <script src="{{ $chart->cdn() }}"></script>
        {{ $chart->script() }}
    @endif
</x-layouts::app>
