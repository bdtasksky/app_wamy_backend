<div class="border dark:border-neutral-700 hover:border-sky-600 transition_3 hover:-translate-y-2 group">
  <a href="{{ __url($news->encode_title) }}">
    <figure class="w-full h-[180px] overflow-hidden">
      <img class="w-full h-full object-cover" 
      src="{{ isset($news->photoLibrary->image_base_url) ? $news->photoLibrary->image_base_url : asset('/assets/news-card-view.png') }}" 
      alt="{{ $news->image_alt }}">
    </figure>
  </a>
  <div class="p-3 bg-neutral-100 dark:bg-neutral-800 space-y-3">
    <a href="{{ __url($news->encode_title) }}" class="text-xl font-bold line-clamp-2 h-14 dark:text-neutral-50 text-neutral-800 group-hover:text-sky-600 dark:hover:text-sky-600 transition_3">
      {{ $news->title }}
    </a>
    <p class="text-neutral-600 line-clamp-2 h-12 dark:text-neutral-50">
      {{ clean_news_content($news->news) }}
    </p>
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
  </div>
</div>