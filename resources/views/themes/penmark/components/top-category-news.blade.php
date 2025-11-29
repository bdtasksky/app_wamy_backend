<!-- Top Category News section -->
<section class="container lg:flex lg:gap-12 mt-4 space-y-8 lg:space-y-0 mb-8 lg:mb-12">

    <!-- Left Section -->
    <section class="w-full lg:w-[60%]">
        <a href="{{ __url($homePageTopNews[0]->news->encode_title) }}">
            <figure class="w-full h-[360px] lg:h-[460px]">
                <img class="w-full h-full object-cover" src="{{ isset($homePageTopNews[0]->news->photoLibrary->large_image) ? asset('storage/'.$homePageTopNews[0]->news->photoLibrary->large_image) : asset('/assets/news-details-view.png') }}"
                    alt="{{ $homePageTopNews[0]->news->image_alt }}">
            </figure>
        </a>
        <div class="p-8 lg:py-12 bg-neutral-100 dark:bg-neutral-900">
            <a href="{{ __url($homePageTopNews[0]->category->slug) }}" class="text-pink-600 font-semibold">
                {{ $homePageTopNews[0]->category->category_name }}
            </a>
            <a href="{{ __url($homePageTopNews[0]->news->encode_title) }}" class="text-xl xl:text-2xl font-bold my-3 line-clamp-3 text-neutral-800 dark:text-neutral-50 hover:text-sky-600 dark:hover:text-sky-600">
                {{ $homePageTopNews[0]->news->title }}
            </a>
            <div class="flex gap-1.5 items-center mt-12">
                <figure class="w-8 h-8 rounded-full overflow-hidden">
                    <img class="w-full h-full object-cover"
                        src="{{ isset($homePageTopNews[0]->news->postByUser->profile_image) ? asset('storage/' . $homePageTopNews[0]->news->postByUser->profile_image) : asset('/assets/profile.png') }}" alt="{{ $homePageTopNews[0]->news->postByUser->full_name ?? localize('unknown') }}">
                </figure>
                <p class="space-x-3 dark:text-white">
                    <span>{{ localize('by') }} - {{ $homePageTopNews[0]->news->postByUser->full_name ?? localize('unknown') }}</span>,
                    <span>{{ $homePageTopNews[0]->news->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                    <span>{{ news_publish_date_format($homePageTopNews[0]->news->publish_date) }}</span>
                </p>
            </div>
        </div>
    </section>

    <!-- Right Section -->
    <section class="w-full lg:w-[40%] border-x dark:border-neutral-800 px-4">
            {{-- Header Tabs --}}
            {{-- <div class="bg-neutral-50 dark:bg-neutral-900 flex gap-6 mb-4 px-6 pt-4 justify-between ">
                <button data-tab="latest" class="news-tab-btn active text-lg pb-4 px-4 text-neutral-600 dark:text-white font-medium border-b-4 border-transparent transition-all capitalize">
                    {{ localize('latest_posts') }}
                </button>
                <button data-tab="popular" class="news-tab-btn text-lg pb-4 px-4 text-neutral-600 dark:text-white font-medium border-b-4 border-transparent transition-all capitalize">
                    {{ localize('popular_posts') }}
                </button>
            </div> --}}

            <div class="bg-neutral-50 dark:bg-neutral-900 flex gap-6 mb-4 px-6 pt-2 justify-between">
                <button 
                    data-tab="latest" 
                    class="news-tab-btn text-lg pb-4 px-4 font-medium border-b-4 border-sky-600 transition-all capitalize 
                        text-neutral-600 dark:text-white">
                    {{ localize('latest_posts') }}
                </button>
                <button 
                    data-tab="popular" 
                    class="news-tab-btn text-lg pb-4 px-4 font-medium border-b-4 border-transparent transition-all capitalize 
                        text-neutral-600 dark:text-white">
                    {{ localize('popular_posts') }}
                </button>
            </div>



            <div>
                {{-- Latest Post Section --}}
                <div id="latest" class="space-y-4 tab-content">
                    @foreach ($latestNews->take(4) as $latestNewsItem)
                        <div class="relative">
                            <p class="px-2 py-1 font-semibold text-white" {!! bgColorStyle($latestNewsItem->category->color_code) !!}>
                                {{ $latestNewsItem->category->category_name }}
                            </p>
                            <a href="{{ __url($latestNewsItem->encode_title) }}" class="text-lg hover:text-sky-600 dark:hover:text-sky-600 font-semibold line-clamp-2 my-3 transition_3 dark:text-neutral-50">
                                {{ $latestNewsItem->title }}
                            </a>
                            <div class="flex gap-1.5 items-center">
                                <figure class="w-8 h-8 rounded-full overflow-hidden">
                                    <img class="w-full h-full object-cover"
                                        src="{{ isset($latestNewsItem->postByUser->profile_image) ? asset('storage/' . $latestNewsItem->postByUser->profile_image) : asset('/assets/profile.png') }}"
                                        alt="{{ $latestNewsItem->postByUser->full_name ?? localize('unknown') }}">
                                </figure>
                                <p class="space-x-3 dark:text-neutral-50">
                                    <span>{{ localize('by') }} - {{ $latestNewsItem->postByUser->full_name ?? localize('unknown') }}</span>,
                                    <span>{{ $latestNewsItem->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                                    <span>{{ news_publish_date_format($latestNewsItem->publish_date) }}</span>
                                </p>
                            </div>
                        </div>
                        <hr class="h-px last:hidden bg-neutral-200 border-0 dark:bg-neutral-700" />
                    @endforeach
                </div>

                {{-- Popular Post Section (hidden by default) --}}
                <div id="popular" class="space-y-4 tab-content hidden">
                    @foreach ($popularNews as $popularNewsItem)
                        <div class="relative">
                            <p class="px-2 py-1 font-semibold text-white" {!! bgColorStyle($popularNewsItem->category->color_code) !!}>
                                {{ $popularNewsItem->category->category_name }}
                            </p>
                            <a href="" class="text-lg hover:text-sky-600 dark:hover:text-sky-600 font-semibold line-clamp-2 my-3 transition_3 dark:text-neutral-50">
                                {{ $popularNewsItem->title }}
                            </a>
                            <div class="flex gap-1.5 items-center">
                                <figure class="w-8 h-8 rounded-full overflow-hidden">
                                    <img class="w-full h-full object-cover"
                                        src="{{ isset($popularNewsItem->postByUser->profile_image) ? asset('storage/' . $popularNewsItem->postByUser->profile_image) : asset('/assets/profile.png') }}"
                                        alt="{{ $popularNewsItem->postByUser->full_name ?? localize('unknown') }}">
                                </figure>
                                <p class="space-x-3 dark:text-neutral-50">
                                    <span>{{ localize('by') }} - {{ $popularNewsItem->postByUser->full_name ?? localize('unknown') }}</span>,
                                    <span>{{ $popularNewsItem->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                                    <span>{{ news_publish_date_format($popularNewsItem->publish_date) }}</span>
                                </p>
                            </div>
                        </div>
                        <hr class="h-px last:hidden bg-neutral-200 border-0 dark:bg-neutral-700" />
                    @endforeach
                </div>
            </div>

    </section>
</section>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tabs = document.querySelectorAll(".news-tab-btn");
        const contents = document.querySelectorAll(".tab-content");

        tabs.forEach(tab => {
            tab.addEventListener("click", function() {
                // Reset all tabs
                tabs.forEach(t => {
                    t.classList.remove("text-sky-600", "border-sky-600");
                    t.classList.add("text-neutral-600", "dark:text-white", "border-transparent");
                });

                // Hide all content
                contents.forEach(c => c.classList.add("hidden"));

                // Activate clicked tab
                this.classList.add("text-sky-600", "border-sky-600");
                this.classList.remove("text-neutral-600", "dark:text-white", "border-transparent");

                // Show related content
                const target = this.getAttribute("data-tab");
                document.getElementById(target).classList.remove("hidden");
            });
        });
    });
</script>
