<section
    class="border p-2 2xl:p-3 rounded-md order-3 bg-white dark:bg-neutral-900 dark:divide-neutral-700 dark:border-neutral-800 shadow-sm">
    <div class="@if (!$themeSettings->background_color || mode()) bg-neutral-700 @endif @if (mode()) dark:bg-neutral-800 @endif @if (!$themeSettings->text_color || mode()) text-white @endif p-2 uppercase font-medium"
        style="@if (!mode() && $themeSettings->background_color) background-color: {{ $themeSettings->background_color }}; @endif @if (!mode() && $themeSettings->text_color) color: {{ $themeSettings->text_color }}; @endif">
        {{ localize('popular_post') }}
    </div>
    <div class="space-y-4  pt-4">
        @foreach ($popularNews as $popularNewsItem)
            <a href="{{ __url($popularNewsItem->encode_title) }}" class="flex items-center gap-2 group">
                <div>
                    <figure class="w-20 h-[70px] overflow-hidden">
                        <img class="w-full h-full object-cover"
                            src="{{ isset($popularNewsItem->photoLibrary->image_base_url) ? $popularNewsItem->photoLibrary->image_base_url : asset('/assets/news-card-view.png') }}"
                            alt="{{ $popularNewsItem->image_alt }}">
                    </figure>
                </div>
                <p class="text-sm line-clamp-3 group-hover:text-sky-600 dark:group-hover:text-sky-600 dark:text-neutral-50 transition_3">
                    {{ clean_news_content($popularNewsItem->news) }}
                </p>
            </a>
            <hr class="h-px last:hidden bg-neutral-200 border-0 dark:bg-neutral-700" />
        @endforeach
    </div>
</section>
