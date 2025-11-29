<section class="lg:col-span-3 mt-6">
    <div
        class="flex justify-between gap-2 items-center mb-4 pb-1 border-b-2 border-neutral-300 dark:border-neutral-700 relative before:absolute before:left-0 rtl:before:left-auto rtl:before:right-0 before:h-1 before:bg-brand-primary before:z-10 before:-bottom-[3px] before:w-20">
        <h1 class="text-sky-600 text-lg font-semibold uppercase">
            {{ $sectionSixNews[0]->category->category_name }}
        </h1>
        <a href="{{ __url($sectionSixNews[0]->category->slug) }}"
            class="capitalize text-neutral-400 hover:text-sky-600 hover:underline transition_3">
            {{ localize('view_more') }}
        </a>
    </div>

    <!-- Card section -->
    <div
        class="grid md:grid-cols-2 xl:grid-cols-3 gap-4 xl:gap-0 xl:divide-x xl:rtl:divide-x-reverse divide-neutral-200 dark:divide-neutral-700">

        @php
            $secSixchunkedNews = $sectionSixNews->chunk(4);
        @endphp

        @foreach ($secSixchunkedNews as $index => $secSixNewsChunk)
            @php
                $paddingClass = match ($index) {
                    0 => 'xl:px-3 xl:pl-0',
                    1 => 'xl:px-3',
                    2 => 'xl:px-3 xl:pr-0',
                };
            @endphp

            @if ($secSixNewsChunk->isNotEmpty())
                <div class="{{ $paddingClass }}">
                    <a href="{{ __url($secSixNewsChunk->first()->news->encode_title) }}"
                        class="block w-full h-64 group overflow-hidden">
                        <img class="w-full h-full object-cover group-hover:scale-105 transition_5"
                            src="{{ isset($secSixNewsChunk->first()->news->photoLibrary->image_base_url) ? $secSixNewsChunk->first()->news->photoLibrary->image_base_url : asset('/assets/news-card-view.png') }}"
                            alt="{{ $secSixNewsChunk->first()->news->image_alt }}" />
                    </a>
                    <div class="space-y-2 divide-y divide-neutral-200 dark:divide-neutral-700">
                        <div class="py-2 space-y-2">
                            <a href="{{ __url($secSixNewsChunk->first()->news->encode_title) }}"
                                class="line-clamp-2 text-neutral-800 dark:text-white hover:text-sky-600 dark:hover:text-sky-600 font-bold text-lg leading-6 transition_3">
                                {{ $secSixNewsChunk->first()->news->title }}
                            </a>
                            <p class="text-sm line-clamp-3 dark:text-neutral-50">
                                {{ clean_news_content($secSixNewsChunk->first()->news->news) }}
                            </p>
                            <div class="flex gap-1.5 items-center">
                                <figure class="w-8 h-8 rounded-full overflow-hidden">
                                    <img class="w-full h-full object-cover" 
                                    src="{{ isset($secSixNewsChunk->first()->news->postByUser->profile_image) ? asset('storage/' . $secSixNewsChunk->first()->news->postByUser->profile_image) : asset('/assets/profile.png') }}"
                                    alt="{{ $secSixNewsChunk->first()->news->postByUser->full_name ?? localize('unknown') }}">
                                </figure>
                                <p class="text-sm display-inline-block items-center text-neutral-800 dark:text-neutral-50">
                                    <span>{{ localize('by') }} - {{ $secSixNewsChunk->first()->news->postByUser->full_name ?? localize('unknown') }}</span>,
                                    <span>{{ $secSixNewsChunk->first()->news->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                                    <span>{{ news_publish_date_format($secSixNewsChunk->first()->news->publish_date) }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- bottom news list -->
                        @if ($secSixNewsChunk->slice(1)->isNotEmpty())
                            @foreach ($secSixNewsChunk->slice(1) as $secSixNewsChunkItem)
                                <a class="block pt-2" href="{{ __url($secSixNewsChunkItem->news->encode_title) }}">
                                    <div class="grid grid-cols-3 gap-2 items-center">
                                        <figure class="w-full h-20">
                                            <img class="w-full h-full object-cover"
                                                src="{{ isset($secSixNewsChunkItem->news->photoLibrary->image_base_url) ? $secSixNewsChunkItem->news->photoLibrary->image_base_url : asset('/assets/opinion-avatar.png') }}"
                                                alt="{{ $secSixNewsChunkItem->news->image_alt }}" />
                                        </figure>
                                        <h2
                                            class="col-span-2 line-clamp-3 text-neutral-600 hover:text-sky-600 transition_3 dark:text-neutral-50 dark:hover:text-sky-600">
                                            {{ $secSixNewsChunkItem->news->title }}
                                        </h2>
                                    </div>
                                </a>
                            @endforeach
                        @endif

                    </div>
                </div>
            @endif
        @endforeach
    </div>
</section>
