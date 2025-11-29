<div id="{{ $secThreeSubData['subcategory']->slug }}" class="grid md:grid-cols-2 gap-5 blog-content hidden">
  <!-- Card Section -->
  <div class="">
    @if(isset($secThreeSubData['news'][0]))
        <a href="{{ __url($secThreeSubData['news'][0]->news->encode_title) }}">
            <figure class="w-full h-[320px] 2xl:h-[460px]">
                <img class="w-full h-full object-cover" src="{{ isset($secThreeSubData['news'][0]->news->photoLibrary->large_image) ? asset('storage/' . $secThreeSubData['news'][0]->news->photoLibrary->large_image) : asset('/assets/news-details-view.png') }}"
                    alt="{{ $secThreeSubData['news'][0]->news->image_alt }}">
            </figure>
        </a>
        <div class="p-3 2xl:px-6 2xl:py-8 bg-neutral-100 dark:bg-neutral-900 space-y-3 2xl:space-y-8">
            <a href="{{ __url($secThreeSubData['news'][0]->news->encode_title) }}" class="text-xl xl:text-2xl font-bold line-clamp-2 text-neutral-800 dark:text-neutral-50 hover:text-sky-600 dark:hover:text-sky-600 h-18">
                {{ $secThreeSubData['news'][0]->news->title }}
            </a>
            <p class="text-neutral-600 line-clamp-3 dark:text-neutral-50">
                {{ clean_news_content($secThreeSubData['news'][0]->news->news) }}
            </p>
            <div class="flex gap-1.5 items-center h-12">
                <figure class="w-8 h-8 rounded-full overflow-hidden">
                    <img class="w-full h-full object-cover"
                        src="{{ isset($secThreeSubData['news'][0]->news->postByUser->profile_image) ? asset('storage/' . $secThreeSubData['news'][0]->news->postByUser->profile_image) : asset('/assets/profile.png') }}"
                        alt="{{ $secThreeSubData['news'][0]->news->postByUser->full_name ?? localize('unknown') }}">
                </figure>
                <p class="space-x-2 text-sm dark:text-white">
                    <span>{{ localize('by') }} - {{ $secThreeSubData['news'][0]->news->postByUser->full_name ?? localize('unknown') }}</span>,
                    <span>{{ $secThreeSubData['news'][0]->news->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                    <span>{{ news_publish_date_format($secThreeSubData['news'][0]->news->publish_date) }}</span>
                </p>
            </div>
        </div>
    @endif
  </div>

    <section class="flex gap-5 overflow-auto pb-2 2xl:pb-0 2xl:grid 2xl:grid-cols-2 2xl:gap-4">
        @if ($secThreeSubData['news']->slice(1)->isNotEmpty())
            @foreach ($secThreeSubData['news']->slice(1) as $secThreeSubAllData)
                <div>
                    <div class="w-[340px] lg:w-[380px] 2xl:w-full">
                        <a href="{{ __url($secThreeSubAllData->news->encode_title) }}">
                            <figure class="w-full h-[320px] 2xl:h-[180px] 4xl:h-[185px]">
                                <img class="w-full h-full object-cover"
                                    src="{{ isset($secThreeSubAllData->news->photoLibrary->image_base_url) ? $secThreeSubAllData->news->photoLibrary->image_base_url : asset('/assets/news-card-view.png') }}" 
                                    alt="{{ $secThreeSubAllData->news->image_alt }}">
                            </figure>
                        </a>
                        <div class="p-3 bg-neutral-100 dark:bg-neutral-900 space-y-3">
                            <a href="{{ __url($secThreeSubAllData->news->encode_title) }}" class="text-xl font-bold line-clamp-2 text-neutral-800 dark:text-neutral-50 hover:text-sky-600 dark:hover:text-sky-600 h-15">
                                {{ $secThreeSubAllData->news->title }}
                            </a>
                            <p class="text-neutral-600 line-clamp-2 dark:text-neutral-50 h-12">
                                {{ clean_news_content($secThreeSubAllData->news->news) }}
                            </p>
                            <div class="flex gap-1.5 items-center h-10">
                                <figure class="w-8 h-8 rounded-full overflow-hidden">
                                    <img class="w-full h-full object-cover"
                                        src="{{ isset($secThreeSubAllData->news->postByUser->profile_image) ? asset('storage/' . $secThreeSubAllData->news->postByUser->profile_image) : asset('/assets/profile.png') }}"
                                        alt="{{ $secThreeSubAllData->news->postByUser->full_name ?? localize('unknown') }}">
                                </figure>
                                <p class="text-sm dark:text-neutral-50">
                                    <span>{{ localize('by') }} - {{ $secThreeSubAllData->news->postByUser->full_name ?? localize('unknown') }}</span>,
                                    <span>{{ $secThreeSubAllData->news->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                                    <span>{{ news_publish_date_format($secThreeSubAllData->news->publish_date) }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </section>

</div>