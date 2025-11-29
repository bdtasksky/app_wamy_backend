@foreach ($news as $latestPostItem)
    <div class="border dark:border-neutral-700 hover:border-sky-600 transition_3 hover:-translate-y-2 group relative">
        <a href="{{ __url($latestPostItem->encode_title) }}">
            <figure class="w-full h-[180px] overflow-hidden">
                <img class="w-full h-full object-cover"
                    src="{{ isset($latestPostItem->photoLibrary->image_base_url) ? $latestPostItem->photoLibrary->image_base_url : asset('/assets/news-card-view.png') }}" 
                    alt="{{ $latestPostItem->image_alt }}">
            </figure>
            <p class="text-xs text-white px-5 py-2 inline-block absolute top-4 right-4 rounded-full z-10" {!! bgColorStyle($latestPostItem->category->color_code) !!}>
                {{ $latestPostItem->category->category_name }}
            </p>
        </a>
        <div class="p-3 bg-white dark:bg-neutral-800 space-y-3">
            <a href="{{ __url($latestPostItem->encode_title) }}" class="text-xl font-bold line-clamp-2 h-14 dark:text-neutral-50 text-neutral-800 group-hover:text-sky-600 transition_3">
                {{ $latestPostItem->title }}
            </a>
            <p class="text-neutral-600 line-clamp-2 h-12 dark:text-neutral-50">
                {{ clean_news_content($latestPostItem->news) }}
            </p>
            <div class="flex gap-1.5 items-center h-10">
                <div>
                    <figure class="w-8 h-8 rounded-full overflow-hidden">
                        <img class="w-full h-full object-cover"
                            src="{{ isset($latestPostItem->postByUser->profile_image) ? asset('storage/' . $latestPostItem->postByUser->profile_image) : asset('/assets/profile.png') }}"
                            alt="{{ $latestPostItem->postByUser->full_name ?? localize('unknown') }}">
                    </figure>
                </div>
                <p class="space-x-2 text-sm flex items-center text-neutral-800 dark:text-neutral-50">
                    <span class="inline-block">{{ $latestPostItem->postByUser->full_name ?? localize('unknown') }}</span>,
                    <span class="flex items-center gap-1"><svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="currentColor" d="M320 216C368.6 216 408 176.6 408 128C408 79.4 368.6 40 320 40C271.4 40 232 79.4 232 128C232 176.6 271.4 216 320 216zM320 514.7L320 365.4C336.3 358.6 352.9 351.7 369.7 344.7C408.7 328.5 450.5 320.1 492.8 320.1L512 320.1L512 480.1L492.8 480.1C433.7 480.1 375.1 491.8 320.5 514.6L320 514.8zM320 296L294.9 285.5C248.1 266 197.9 256 147.2 256L112 256C85.5 256 64 277.5 64 304L64 496C64 522.5 85.5 544 112 544L147.2 544C197.9 544 248.1 554 294.9 573.5L307.7 578.8C315.6 582.1 324.4 582.1 332.3 578.8L345.1 573.5C391.9 554 442.1 544 492.8 544L528 544C554.5 544 576 522.5 576 496L576 304C576 277.5 554.5 256 528 256L492.8 256C442.1 256 391.9 266 345.1 285.5L320 296z"/></svg> {{ $latestPostItem->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                    <span class="inline-block">{{ news_publish_date_format($latestPostItem->publish_date) }}</span>
                </p>
            </div>
        </div>
    </div>
@endforeach