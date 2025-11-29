<section class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach ($sectionTwoNews['leftNews'] as $sectionTwoNewsLeftItem)
        <a href="{{ __url($sectionTwoNewsLeftItem->news->encode_title) }}" class="flex items-center gap-3 group bg-white p-1">
            <div>
                <figure class="w-44 h-[110px] xl:h-[140px] overflow-hidden">
                    <img class="w-full h-full object-cover"
                        src="{{ isset($sectionTwoNewsLeftItem->news->photoLibrary->image_base_url) ? $sectionTwoNewsLeftItem->news->photoLibrary->image_base_url : asset('/assets/news-card-view.png') }}"
                        alt="{{ $sectionTwoNewsLeftItem->news->image_alt }}">
                </figure>
            </div>
            <div class="w-full">
                <p class="text-lg leading-6 group-hover:text-sky-600 dark:group-hover:text-sky-600 font-semibold line-clamp-2 transition_3 dark:text-neutral-50">
                    {{ $sectionTwoNewsLeftItem->news->title }}
                </p>
                <div class="capitalize flex items-center gap-2 text-xs mt-3 mb-1 dark:text-neutral-50">
                    <span>{{ $sectionTwoNewsLeftItem->news->postByUser->full_name ?? localize('unknown') }}</span>
                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="currentColor" d="M528 320C528 434.9 434.9 528 320 528C205.1 528 112 434.9 112 320C112 205.1 205.1 112 320 112C434.9 112 528 205.1 528 320zM64 320C64 461.4 178.6 576 320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320zM296 184L296 320C296 328 300 335.5 306.7 340L402.7 404C413.7 411.4 428.6 408.4 436 397.3C443.4 386.2 440.4 371.4 429.3 364L344 307.2L344 184C344 170.7 333.3 160 320 160C306.7 160 296 170.7 296 184z"/></svg>
                    <span>{{ news_publish_date_format($sectionTwoNewsLeftItem->news->publish_date) }}</span>
                    <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path fill="currentColor"
                            d="M168.2 384.9c-15-5.4-31.7-3.1-44.6 6.4c-8.2 6-22.3 14.8-39.4 22.7c5.6-14.7 9.9-31.3 11.3-49.4c1-12.9-3.3-25.7-11.8-35.5C60.4 302.8 48 272 48 240c0-79.5 83.3-160 208-160s208 80.5 208 160s-83.3 160-208 160c-31.6 0-61.3-5.5-87.8-15.1zM26.3 423.8c-1.6 2.7-3.3 5.4-5.1 8.1l-.3 .5c-1.6 2.3-3.2 4.6-4.8 6.9c-3.5 4.7-7.3 9.3-11.3 13.5c-4.6 4.6-5.9 11.4-3.4 17.4c2.5 6 8.3 9.9 14.8 9.9c5.1 0 10.2-.3 15.3-.8l.7-.1c4.4-.5 8.8-1.1 13.2-1.9c.8-.1 1.6-.3 2.4-.5c17.8-3.5 34.9-9.5 50.1-16.1c22.9-10 42.4-21.9 54.3-30.6c31.8 11.5 67 17.9 104.1 17.9c141.4 0 256-93.1 256-208S397.4 32 256 32S0 125.1 0 240c0 45.1 17.7 86.8 47.7 120.9c-1.9 24.5-11.4 46.3-21.4 62.9zM144 272a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm144-32a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm80 32a32 32 0 1 0 0-64 32 32 0 1 0 0 64z" />
                    </svg>
                    <span>{{ $sectionTwoNewsLeftItem->news->comments_count }}</span>
                </div>
                <p class="text-neutral-600 dark:text-neutral-50 text-s line-clamp-2">
                    {{ clean_news_content($sectionTwoNewsLeftItem->news->news) }}
                </p>
            </div>
        </a>
    @endforeach
</section>
