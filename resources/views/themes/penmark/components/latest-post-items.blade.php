@foreach ($news as $latestPostItem)
    <div class="border dark:border-neutral-700 hover:border-sky-600 transition_3 hover:-translate-y-2 group">
        <a href="{{ __url($latestPostItem->encode_title) }}">
            <figure class="w-full h-[180px] overflow-hidden">
                <img class="w-full h-full object-cover"
                    src="{{ $latestPostItem->photoLibrary->image_base_url ?? asset('/assets/news-card-view.png') }}"
                    alt="{{ $latestPostItem->image_alt }}">
            </figure>
        </a>
        <div class="p-3 bg-neutral-100 dark:bg-neutral-800 space-y-3">
            <a href="{{ __url($latestPostItem->encode_title) }}" class="text-xl font-bold line-clamp-2 h-14 dark:text-neutral-50 text-neutral-800 group-hover:text-sky-600 transition_3">
                {{ $latestPostItem->title }}
            </a>
            <p class="text-neutral-600 line-clamp-2 h-12 dark:text-neutral-50">
                {{ clean_news_content($latestPostItem->news) }}
            </p>
            <div class="flex gap-1.5 items-center h-10">
                <figure class="w-8 h-8 rounded-full overflow-hidden">
                    <img class="w-full h-full object-cover"
                        src="{{ isset($latestPostItem->postByUser->profile_image) ? asset('storage/' . $latestPostItem->postByUser->profile_image) : asset('/assets/profile.png') }}"
                        alt="{{ $latestPostItem->postByUser->full_name ?? localize('unknown') }}">
                </figure>
                <p class="text-sm display-inline-block gap-0.5 items-center text-neutral-800 dark:text-neutral-50">
                    <span>{{ localize('by') }} - {{ $latestPostItem->postByUser->full_name ?? localize('unknown') }}</span>,
                    <span>{{ $latestPostItem->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                    <span>{{ news_publish_date_format($latestPostItem->publish_date) }}</span>
                </p>
            </div>
        </div>
    </div>
@endforeach
