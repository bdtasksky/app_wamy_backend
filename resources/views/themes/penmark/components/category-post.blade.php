<div class="flex justify-between gap-4 mb-4">
    <h1
        class="text-xl font-bold mb-4 bg-sky-600 text-white px-3 py-1.5 lg:bg-transparent lg:px-0 lg:py-0 lg:text-sky-600">
        {{ $categoryNews[0]->category->category_name }}
    </h1>

    <a href="{{ __url($categoryNews[0]->category->slug) }}"
        class="capitalize text-nowrap text-base p-2 text-neutral-600 dark:text-white hover:underline hover:text-sky-600 font-medium transition-all duration-300 ease-in">
        {{ localize('view_more') }}
    </a>
</div>

<section class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    <!-- Card Section -->

    @foreach ($categoryNews as $categoryItem)
        <div class="border dark:border-neutral-700 hover:border-sky-600 transition_3 hover:-translate-y-2 group">
            <a href="{{ __url($categoryItem->news->encode_title) }}">
                <figure class="w-full h-[180px] overflow-hidden">
                    <img class="w-full h-full object-cover" 
                    src="{{ isset($categoryItem->news->photoLibrary->image_base_url) ? $categoryItem->news->photoLibrary->image_base_url : asset('/assets/news-card-view.png') }}" 
                    alt="{{ $categoryItem->news->image_alt }}">
                </figure>
            </a>
            <div class="p-3 bg-neutral-100 dark:bg-neutral-800 space-y-3">
                <a href="{{ __url($categoryItem->news->encode_title) }}" class="text-xl font-bold line-clamp-2 h-14 dark:text-neutral-50 text-neutral-800 group-hover:text-sky-600 transition_3">
                    {{ $categoryItem->news->title }}
                </a>
                <p class="text-neutral-600 line-clamp-2 h-12 dark:text-neutral-50">
                    {{ clean_news_content($categoryItem->news->news) }}
                </p>
                <div class="flex gap-1.5 items-center h-10">
                    <figure class="w-8 h-8 rounded-full overflow-hidden">
                        <img class="w-full h-full object-cover" 
                        src="{{ isset($categoryItem->news->postByUser->profile_image) ? asset('storage/' . $categoryItem->news->postByUser->profile_image) : asset('/assets/profile.png') }}"
                        alt="{{ $categoryItem->news->postByUser->full_name ?? localize('unknown') }}">
                    </figure>
                    <p class="text-sm display-inline-block items-center text-neutral-800 dark:text-neutral-50">
                        <span>{{ localize('by') }} - {{ $categoryItem->news->postByUser->full_name ?? localize('unknown') }}</span>,
                        <span>{{ $categoryItem->news->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                        <span>{{ news_publish_date_format($categoryItem->news->publish_date) }}</span>
                    </p>
                </div>
            </div>
        </div>
    @endforeach

</section>
