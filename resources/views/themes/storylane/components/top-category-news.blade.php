<!-- Top Category News section -->
<section class="container my-4 pt-4 grid grid-cols-1 lg:grid-cols-6 xl:grid-cols-4 gap-4">

    <!-- Left Section -->
    <section class="lg:col-span-4 xl:col-span-3">
        @if ($homePageTopNews->isNotEmpty())
            @include('themes.storylane.components.slider.home-slider')
        @endif
    </section>

    <!-- Right Section -->
    <section class="lg:col-span-2 xl:col-span-1  grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-1">
        @if ($mostNewsCategory->isNotEmpty())
            <div class="space-y-4 md:space-y-6 lg:space-y-2 xl:space-y-5 py-2 xl:py-4 border dark:border-neutral-800 rounded-sm px-4">
                @foreach ($mostNewsCategory as $mostNewsCategoryItem)
                    <a href="{{ __url($mostNewsCategoryItem->slug) }}" class="flex items-center gap-2 group">
                        <div>
                            <figure class="w-20 h-[70px] rounded-sm overflow-hidden">
                                <img class="w-full h-full object-cover rounded-md"
                                    src="{{ (isset($mostNewsCategoryItem->category_imgae) && $mostNewsCategoryItem->category_imgae != '') ? asset('storage/' . $mostNewsCategoryItem->category_imgae) : asset('/assets/category-grid-view.png') }}"
                                    alt="{{ $mostNewsCategoryItem->category_name }}">
                            </figure>
                        </div>
                        <div class="w-full">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-lg group-hover:text-sky-600 dark:group-hover:text-sky-600 font-semibold line-clamp-1 transition_3 dark:text-neutral-50">
                                    {{ $mostNewsCategoryItem->category_name }}
                                </p>
                                <span class="text-lg font-semibold dark:text-neutral-50">
                                    {{ $mostNewsCategoryItem->news_count }}
                                </span>
                            </div>
                            <p class="text-sm line-clamp-2 group-hover:text-sky-600 dark:group-hover:text-sky-600 dark:text-neutral-50 transition_3">
                                {{ $mostNewsCategoryItem->description }}
                            </p>
                        </div>
                    </a>
                    <hr class="h-px last:hidden bg-neutral-200 border-0 dark:bg-neutral-700" />
                @endforeach
            </div>
            
            {{-- Add section --}}
            <div class="mt-4 md:mt-0 lg:hidden">
                <figure class="">
                    @php $ad = get_advertisements(1, 4); @endphp

                    @if ($ad && $ad->status == 1)
                        {!! $ad->embed_code !!}
                    @elseif(!$ad)
                        <img class="w-full h-full object-cover" src="{{ asset('assets/ads-electronic-medium.png') }}" alt="" />
                    @endif
                </figure>
            </div>
        @endif
    </section>
</section>
