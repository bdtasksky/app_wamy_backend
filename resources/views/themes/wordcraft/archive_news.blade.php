<x-web-layout :brdcArchiveInfo="$archiveNews">
    <!-- Pagination -->
    <section class="container mt-2">
        <div class="bg-white dark:text-white dark:bg-neutral-800 flex items-center p-2 gap-3">
            <ul class="flex gap-1 items-center">
                <li>
                    <a class="text-neutral-600 dark:text-white transition_3 whitespace-nowrap"
                        href="{{ __url('/') }}">{{ localize('home') }}</a>
                </li>
                <svg width="12" height="14" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 1L1 15" stroke="oklch(70.8% 0 0)" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>

                <li class="text-brand-primary line-clamp-1">{{ localize('archive') }}</li>
            </ul>
        </div>

        <form action="{{ __url('archive') }}" method="GET">
            <section class="grid items-center grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3 my-4">
                <!-- Date Range -->
                <div>
                    <input id="myDateRangeInput" name="daterange"
                        value="{{ request('daterange') }}"
                        class="text-sm w-full px-2 py-2 border rounded-md dark:bg-neutral-800 dark:border-neutral-800 dark:text-neutral-50"
                        placeholder="Select Date" type="text">
                </div>

                <!-- Category -->
                <div>
                    <select name="category" id="category"
                        class="text-sm w-full px-2 py-2 border rounded-md dark:bg-neutral-800 dark:border-neutral-800 dark:text-neutral-50">
                        <option value="">{{ localize('select_category') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Content Search -->
                <div>
                    <input type="text" name="content" id="content"
                        value="{{ request('content') }}"
                        class="text-sm w-full px-2 py-2 border rounded-md dark:bg-neutral-800 dark:border-neutral-800 dark:text-neutral-50"
                        placeholder="{{ localize('search_here') }}">
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <div class="w-1/2">
                        <button type="submit"
                            class="w-full text-sm bg-neutral-600 px-7 py-2 rounded-md text-white">
                            {{ localize('search') }}
                        </button>
                    </div>
                    <div class="w-1/2">
                        <a href="{{ __url('archive') }}"
                        class="w-full text-sm bg-sky-600 px-7 py-2 rounded-md text-white block text-center">
                            {{ localize('search_all') }}
                        </a>
                    </div>
                </div>
            </section>
        </form>

    </section>

    <!-- Archive News Grid -->
    <section class="container mt-2 pb-8 gap-4">
        <div class="block">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @if ($archiveNews->isNotEmpty())
                    @foreach ($archiveNews as $news)
                        <!-- Single News Card -->
                        <div class="border dark:border-neutral-700 hover:border-sky-600 transition_3 hover:-translate-y-2 group">
                            <a href="{{ __url($news->encode_title) }}">
                                <figure class="w-full h-[180px] overflow-hidden">
                                    <img class="w-full h-full object-cover"
                                        src="{{ isset($news->photoLibrary->image_base_url) ? $news->photoLibrary->image_base_url : asset('/assets/news-card-view.png') }}" 
                                        alt="{{ $news->image_alt }}">
                                </figure>
                            </a>
                            <div class="p-3 bg-white dark:bg-neutral-800 space-y-3">
                                <a href="{{ __url($news->encode_title) }}" class="text-xl font-bold line-clamp-2 h-14 dark:text-neutral-50 text-neutral-800 group-hover:text-sky-600 transition_3">
                                    {{ $news->title }}
                                </a>
                                <p class="text-neutral-600 line-clamp-2 h-12 dark:text-neutral-50">
                                    {{ clean_news_content($news->news) }}
                                </p>
                                <div class="flex gap-1.5 items-center h-10">
                                    <div>
                                        <figure class="w-8 h-8 rounded-full overflow-hidden">
                                            <img class="w-full h-full object-cover"
                                                src="{{ isset($news->postByUser->profile_image) ? asset('storage/' . $news->postByUser->profile_image) : asset('/assets/profile.png') }}"
                                                alt="{{ $news->postByUser->full_name ?? localize('unknown') }}">
                                        </figure>
                                    </div>
                                    <p class="space-x-2 text-sm flex items-center text-neutral-800 dark:text-neutral-50">
                                        <span class="inline-block">{{ $news->postByUser->full_name ?? localize('unknown') }}</span>,
                                        <span class="flex items-center gap-1"><svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="currentColor" d="M320 216C368.6 216 408 176.6 408 128C408 79.4 368.6 40 320 40C271.4 40 232 79.4 232 128C232 176.6 271.4 216 320 216zM320 514.7L320 365.4C336.3 358.6 352.9 351.7 369.7 344.7C408.7 328.5 450.5 320.1 492.8 320.1L512 320.1L512 480.1L492.8 480.1C433.7 480.1 375.1 491.8 320.5 514.6L320 514.8zM320 296L294.9 285.5C248.1 266 197.9 256 147.2 256L112 256C85.5 256 64 277.5 64 304L64 496C64 522.5 85.5 544 112 544L147.2 544C197.9 544 248.1 554 294.9 573.5L307.7 578.8C315.6 582.1 324.4 582.1 332.3 578.8L345.1 573.5C391.9 554 442.1 544 492.8 544L528 544C554.5 544 576 522.5 576 496L576 304C576 277.5 554.5 256 528 256L492.8 256C442.1 256 391.9 266 345.1 285.5L320 296z"/></svg> {{ $news->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                                        <span class="inline-block">{{ news_publish_date_format($news->publish_date) }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full">
                        <div class="bg-red-100 text-red-700 px-4 py-3 rounded text-center w-full text-xl" role="alert">
                            {{ localize('no_information_found') }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Pagination Links -->
            <div class="mt-6 flex justify-center">
                {{ $archiveNews->links('vendor.pagination.archive') }}
            </div>
        </div>
    </section>

    @push('plugins-css')
        <!-- daterangepicker CSS  -->
        <link rel="stylesheet" type="text/css" href="{{ asset('website/plugins/daterangepicker/daterangepicker.css') }}" />
    @endpush

    <!-- Details Page News (right side news sticky) End -->
    @push('plugins-js')
        <script src="{{ asset('website/plugins/daterangepicker/moment.min.js') }}"></script>
        <script src="{{ asset('website/plugins/daterangepicker/daterangepicker.min.js') }}"></script>
    @endpush
    @push('custom-js')
        <script src="{{ asset('website/js/date-range.js') }}"></script>
    @endpush
</x-web-layout>
