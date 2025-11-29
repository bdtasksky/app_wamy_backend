<x-web-layout>
    <!-- Pagination -->
    <section class="container mt-2">
        <div class="bg-neutral-100 dark:text-white dark:bg-neutral-800 flex items-center p-2 gap-3">
            <ul class="flex gap-1 items-center">
                <li>
                    <a class="text-neutral-600 dark:text-white transition_3 whitespace-nowrap"
                        href="{{ __url('/') }}">{{ localize('home') }}</a>
                </li>
                <svg width="12" height="14" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 1L1 15" stroke="oklch(70.8% 0 0)" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>

                <li class="text-brand-primary line-clamp-1">{{ localize('Search') }}</li>
            </ul>
        </div>
    </section>
    <section class="container mt-2 pb-8 grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-4">
        <section class="md:col-span-2 xl:col-span-3 gap-6">
            <h1 class="font-semibold mt-2 mb-5 text-2xl dark:text-white text-neutral-800">
                {{ localize('Search') }} : {{ $searchTerm }}
            </h1>
            @if ($newsResults->isEmpty())
                <h1 class="text-neutral-600">{{ localize('No results found for') }} : {{ $searchTerm }}</h1>
            @else
                @php
                    $firstSixNews = $newsResults->slice(0, 6);
                    $secondSixNews = $newsResults->slice(6, 6);
                    $remainingNews = $newsResults->slice(12);
                @endphp
                <div class="block">
                    <div data-content="card_view" class="grid md:grid-cols-2 xl:grid-cols-3 gap-8">
                        <!-- cart -->
                        @if ($firstSixNews->isNotEmpty())
                            @foreach ($firstSixNews as $news)
                                <div>
                                    <div class="w-[340px] lg:w-[380px] 2xl:w-full">
                                        <a href="{{ __url($news->encode_title) }}">
                                            <figure class="w-full h-[320px] 2xl:h-[180px] 4xl:h-[185px]">
                                                <img class="w-full h-full object-cover"
                                                    src="{{ isset($news->photoLibrary->image_base_url) ? $news->photoLibrary->image_base_url : asset('/assets/category-grid-view.png') }}" alt="{{ $news->image_alt }}">
                                            </figure>
                                        </a>
                                        <div class="p-3 bg-neutral-100 dark:bg-neutral-900 space-y-3">
                                            <a href="{{ __url($news->encode_title) }}"
                                                class="text-xl font-bold line-clamp-2 text-neutral-800 dark:text-neutral-50 hover:text-sky-600 dark:hover:text-sky-600 h-15">
                                                {{ $news->title }}
                                            </a>
                                            <p class="text-neutral-600 line-clamp-2 dark:text-neutral-50 h-12">
                                                {{ clean_news_content($news->news) }}
                                            </p>
                                            <div class="flex gap-1.5 items-center h-10">
                                                <figure class="w-8 h-8 rounded-full overflow-hidden">
                                                    <img class="w-full h-full object-cover"
                                                        src="{{ isset($news->postByUser->profile_image) ? asset('storage/' . $news->postByUser->profile_image) : asset('/assets/profile.png') }}"
                                                        alt="{{ $news->postByUser->full_name ?? localize('unknown') }}">
                                                </figure>
                                                <p class="space-x-2 text-sm dark:text-neutral-50">
                                                    <span>{{ localize('by') }} - {{ $news->postByUser->full_name ?? localize('unknown') }}</span>,
                                                    <span>{{ $news->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                                                    <span>{{ news_publish_date_format($news->publish_date) }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <!-- add section -->
                <figure class="mt-8">
                    @php $ad = get_advertisements(2, 2); @endphp

                    @if ($ad && $ad->status == 1)
                        {!! $ad->embed_code !!}
                    @elseif(!$ad)
                        <img class="w-full h-full object-cover" src="{{ asset('assets/banner-large.png') }}" alt="" />
                    @endif
                </figure>

                <!-- secondSixNews Card View -->
                @if ($secondSixNews->isNotEmpty())
                    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-8 mt-8">
                        <!-- cart -->
                        @foreach ($secondSixNews as $news)
                            <div>
                                    <div class="w-[340px] lg:w-[380px] 2xl:w-full">
                                        <a href="{{ __url($news->encode_title) }}">
                                            <figure class="w-full h-[320px] 2xl:h-[180px] 4xl:h-[185px]">
                                                <img class="w-full h-full object-cover"
                                                    src="{{ isset($news->photoLibrary->image_base_url) ? $news->photoLibrary->image_base_url : asset('/assets/category-grid-view.png') }}" alt="{{ $news->image_alt }}">
                                            </figure>
                                        </a>
                                        <div class="p-3 bg-neutral-100 dark:bg-neutral-900 space-y-3">
                                            <a href="{{ __url($news->encode_title) }}"
                                                class="text-xl font-bold line-clamp-2 text-neutral-800 dark:text-neutral-50 hover:text-sky-600 dark:hover:text-sky-600 h-15">
                                                {{ $news->title }}
                                            </a>
                                            <p class="text-neutral-600 line-clamp-2 dark:text-neutral-50 h-12">
                                                {{ clean_news_content($news->news) }}
                                            </p>
                                            <div class="flex gap-1.5 items-center h-10">
                                                <figure class="w-8 h-8 rounded-full overflow-hidden">
                                                    <img class="w-full h-full object-cover"
                                                        src="{{ isset($news->postByUser->profile_image) ? asset('storage/' . $news->postByUser->profile_image) : asset('/assets/profile.png') }}"
                                                        alt="{{ $news->postByUser->full_name ?? localize('unknown') }}"
                                                        alt="{{ $news->postByUser->full_name ?? localize('unknown') }}">
                                                </figure>
                                                <p class="space-x-2 text-sm dark:text-neutral-50">
                                                    <span>{{ localize('by') }} - {{ $news->postByUser->full_name ?? localize('unknown') }}</span>,
                                                    <span>{{ $news->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                                                    <span>{{ news_publish_date_format($news->publish_date) }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        @endforeach
                    </div>

                    <!-- add section -->
                    <figure class="mt-8">
                        @php $ad = get_advertisements(2, 3); @endphp

                        @if ($ad && $ad->status == 1)
                            {!! $ad->embed_code !!}
                        @elseif(!$ad)
                            <img class="w-full h-full object-cover" src="{{ asset('assets/banner-large.png') }}" alt="" />
                        @endif
                    </figure>

                @endif

                @if ($remainingNews->isNotEmpty())
                    <section class="my-8">
                        <!-- Card section -->
                        <div id="news-grid"
                            class="relative grid md:grid-cols-2 xl:grid-cols-3 gap-8">
                            <!-- cart -->
                            @foreach ($remainingNews as $news)
                                <div>
                                    <div class="w-[340px] lg:w-[380px] 2xl:w-full">
                                        <a href="{{ __url($news->encode_title) }}">
                                            <figure class="w-full h-[320px] 2xl:h-[180px] 4xl:h-[185px]">
                                                <img class="w-full h-full object-cover"
                                                    src="{{ isset($news->photoLibrary->image_base_url) ? $news->photoLibrary->image_base_url : asset('/assets/category-grid-view.png') }}" alt="{{ $news->image_alt }}">
                                            </figure>
                                        </a>
                                        <div class="p-3 bg-neutral-100 dark:bg-neutral-900 space-y-3">
                                            <a href="{{ __url($news->encode_title) }}"
                                                class="text-xl font-bold line-clamp-2 text-neutral-800 dark:text-neutral-50 hover:text-sky-600 dark:hover:text-sky-600 h-15">
                                                {{ $news->title }}
                                            </a>
                                            <p class="text-neutral-600 line-clamp-2 dark:text-neutral-50 h-12">
                                                {{ clean_news_content($news->news) }}
                                            </p>
                                            <div class="flex gap-1.5 items-center h-10">
                                                <figure class="w-8 h-8 rounded-full overflow-hidden">
                                                    <img class="w-full h-full object-cover"
                                                        src="{{ isset($news->postByUser->profile_image) ? asset('storage/' . $news->postByUser->profile_image) : asset('/assets/profile.png') }}"
                                                        alt="{{ $news->postByUser->full_name ?? localize('unknown') }}"
                                                        alt="{{ $news->postByUser->full_name ?? localize('unknown') }}">
                                                </figure>
                                                <p class="space-x-2 text-sm dark:text-neutral-50">
                                                    <span>{{ localize('by') }} - {{ $news->postByUser->full_name ?? localize('unknown') }}</span>,
                                                    <span>{{ $news->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                                                    <span>{{ news_publish_date_format($news->publish_date) }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endif
        </section>
        <!-- Right section news -->
        <section>
            <div class="space-y-6 sticky top-16">
                <!-- Popular post -->
                @include('themes.penmark.components.common.popular-post')

                <!-- Ads section -->
                <figure class="">
                    @php $ad = get_advertisements(2, 1); @endphp

                    @if ($ad && $ad->status == 1)
                        {!! $ad->embed_code !!}
                    @elseif(!$ad)
                        <img class="w-full h-full object-cover" src="{{ asset('assets/ads-electronic.png') }}" alt="" />
                    @endif
                </figure>

                <!-- Top Week -->
                @include('themes.penmark.components.common.recommended-top-week-post')

                <!-- Voting poll -->
                @if ($votingPoll)
                    @include('themes.penmark.components.common.voting-poll')
                @endif

            </div>
        </section>

    </section>
</x-web-layout>
