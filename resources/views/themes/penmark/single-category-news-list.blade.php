<div class="grid items-center grid-cols-4 gap-4 bg-neutral-100 dark:bg-neutral-800">
  <a href="{{ __url($news->encode_title) }}"
    class="block w-full h-20 xl:h-28 2xl:h-36 group overflow-hidden">
    <img class="w-full h-full object-cover group-hover:scale-105 transition_5"
      src="{{ isset($news->photoLibrary->image_base_url) ? $news->photoLibrary->image_base_url : asset('/assets/news-card-view.png') }}"
      alt="{{ $news->image_alt }}" />
  </a>
  <div class="col-span-3 space-y-2 pr-2">
    <a href="{{ __url($news->encode_title) }}" class="text-xl font-bold line-clamp-2 dark:text-neutral-50 text-neutral-800 hover:text-sky-600 dark:hover:text-sky-600 transition_3">
      {{ $news->title }}
    </a>
    <div class="flex gap-1.5 items-center">
      <figure class="w-8 h-8 rounded-full overflow-hidden">
        <img class="w-full h-full object-cover" 
        src="{{ isset($news->postByUser->profile_image) ? asset('storage/' . $news->postByUser->profile_image) : asset('/assets/profile.png') }}"
        alt="{{ $news->postByUser->full_name ?? localize('unknown') }}">
      </figure>
      <p class="text-sm display-inline-block items-center text-neutral-800 dark:text-neutral-50">
        <span>{{ localize('by') }} - {{ $news->postByUser->full_name ?? localize('unknown') }}</span>,
        <span>{{ $news->reader_hit ?? 0 }} {{ localize('read') }}</span>,
        <span>{{ news_publish_date_format($news->publish_date) }}</span>
      </p>
    </div>
    <p class="text-sm line-clamp-1 xl:line-clamp-2 text-neutral-600 dark:text-neutral-50">
      {{ clean_news_content($news->news) }}
    </p>
  </div>
</div>