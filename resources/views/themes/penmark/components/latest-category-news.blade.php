<!-- Top Category News section -->
<section class="container mt-4  mb-8 lg:mb-12">
    <div class="lg:flex justify-between gap-4 mb-4">
        <h1
            class="text-xl font-bold mb-4 bg-sky-600 text-white px-3 py-1.5 lg:bg-transparent lg:px-0 lg:py-0 lg:text-sky-600">
            {{ $sectionThreeAllNews[0]->category->category_name }}
        </h1>
        {{-- Header Tabs --}}
        <div class="flex gap-2 overflow-auto pb-3">
            <button type="button" data-tab="all"
                class="blog-category-tab capitalize text-nowrap active text-base p-2 text-neutral-600 font-medium border-b-4 border-transparent transition-all">
                {{ localize('all_post') }}
            </button>
            @if ($sectionThreeAllSubNews->isNotEmpty())
                @foreach ($sectionThreeAllSubNews as $data)
                    <button type="button" data-tab="{{ $data['subcategory']->slug }}"
                        class="blog-category-tab capitalize text-nowrap text-base p-2 text-neutral-600 font-medium border-b-4 border-transparent transition-all">
                        {{ strtoupper($data['subcategory']->category_name) }}
                    </button>
                @endforeach
            @endif
        </div>
    </div>

    {{-- catagory tab 1 --}}
    <div id="all" class="grid md:grid-cols-2 gap-5 blog-content">
        <!-- Card Section -->
        <div class="">
            @if(isset($sectionThreeAllNews[0]))
                <a href="{{ __url($sectionThreeAllNews[0]->news->encode_title) }}">
                    <figure class="w-full h-[320px] 2xl:h-[460px]">
                        <img class="w-full h-full object-cover" src="{{ isset($sectionThreeAllNews[0]->news->photoLibrary->large_image) ? asset('storage/' . $sectionThreeAllNews[0]->news->photoLibrary->large_image) : asset('/assets/news-details-view.png') }}"
                            alt="{{ $sectionThreeAllNews[0]->news->image_alt }}">
                    </figure>
                </a>
                <div class="p-3 2xl:px-6 2xl:py-8 bg-neutral-100 dark:bg-neutral-900 space-y-3 2xl:space-y-8">
                    <a href="{{ __url($sectionThreeAllNews[0]->news->encode_title) }}" class="text-xl xl:text-2xl font-bold line-clamp-2 text-neutral-800 dark:text-neutral-50 hover:text-sky-600 dark:hover:text-sky-600 h-18">
                        {{ $sectionThreeAllNews[0]->news->title }}
                    </a>
                    <p class="text-neutral-600 line-clamp-3 dark:text-neutral-50">
                        {{ clean_news_content($sectionThreeAllNews[0]->news->news) }}
                    </p>
                    <div class="flex gap-1.5 items-center h-12">
                        <figure class="w-8 h-8 rounded-full overflow-hidden">
                            <img class="w-full h-full object-cover"
                                src="{{ isset($sectionThreeAllNews[0]->news->postByUser->profile_image) ? asset('storage/' . $sectionThreeAllNews[0]->news->postByUser->profile_image) : asset('/assets/profile.png') }}"
                                alt="{{ $sectionThreeAllNews[0]->news->postByUser->full_name ?? localize('unknown') }}">
                        </figure>
                        <p class="space-x-2 text-sm dark:text-white">
                            <span>{{ localize('by') }} - {{ $sectionThreeAllNews[0]->news->postByUser->full_name ?? localize('unknown') }}</span>,
                            <span>{{ $sectionThreeAllNews[0]->news->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                            <span>{{ news_publish_date_format($sectionThreeAllNews[0]->news->publish_date) }}</span>
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <section class="flex gap-5 overflow-auto pb-2 2xl:pb-0 2xl:grid 2xl:grid-cols-2 2xl:gap-4">
            <!-- Card Section -->
            @if ($sectionThreeAllNews->slice(1)->isNotEmpty())
                @foreach ($sectionThreeAllNews->slice(1) as $sectionThreeAllNewsItem)
                    <div>
                        <div class="w-[340px] lg:w-[380px] 2xl:w-full">
                            <a href="{{ __url($sectionThreeAllNewsItem->news->encode_title) }}">
                                <figure class="w-full h-[320px] 2xl:h-[180px] 4xl:h-[185px]">
                                    <img class="w-full h-full object-cover"
                                        src="{{ isset($sectionThreeAllNewsItem->news->photoLibrary->image_base_url) ? $sectionThreeAllNewsItem->news->photoLibrary->image_base_url : asset('/assets/news-card-view.png') }}" 
                                        alt="{{ $sectionThreeAllNewsItem->news->image_alt }}">
                                </figure>
                            </a>
                            <div class="p-3 bg-neutral-100 dark:bg-neutral-900 space-y-3">
                                <a href="{{ __url($sectionThreeAllNewsItem->news->encode_title) }}" class="text-xl font-bold line-clamp-2 text-neutral-800 dark:text-neutral-50 hover:text-sky-600 dark:hover:text-sky-600 h-15">
                                    {{ $sectionThreeAllNewsItem->news->title }}
                                </a>
                                <p class="text-neutral-600 line-clamp-2 dark:text-neutral-50 h-12">
                                    {{ clean_news_content($sectionThreeAllNewsItem->news->news) }}
                                </p>
                                <div class="flex gap-1.5 items-center h-10">
                                    <figure class="w-8 h-8 rounded-full overflow-hidden">
                                        <img class="w-full h-full object-cover"
                                            src="{{ isset($sectionThreeAllNewsItem->news->postByUser->profile_image) ? asset('storage/' . $sectionThreeAllNewsItem->news->postByUser->profile_image) : asset('/assets/profile.png') }}"
                                            alt="{{ $sectionThreeAllNewsItem->news->postByUser->full_name ?? localize('unknown') }}">
                                    </figure>
                                    <p class="text-sm dark:text-neutral-50">
                                        <span>{{ localize('by') }} - {{ $sectionThreeAllNewsItem->news->postByUser->full_name ?? localize('unknown') }}</span>,
                                        <span>{{ $sectionThreeAllNewsItem->news->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                                        <span>{{ news_publish_date_format($sectionThreeAllNewsItem->news->publish_date) }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </section>
    </div>

    <!-- Sub Category  -->
    @if ($sectionThreeAllSubNews->isNotEmpty())
        @foreach ($sectionThreeAllSubNews as $secThreeSubData)
            @include('themes.penmark.components.sub-tab-content', [
                'secThreeSubData' => $secThreeSubData,
            ])
        @endforeach
    @endif
</section>

