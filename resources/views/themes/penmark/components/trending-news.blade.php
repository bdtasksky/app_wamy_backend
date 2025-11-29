<!-- Top Category News section -->
<section class="container mt-4 mb-8 lg:mb-12">
    <div class="lg:flex justify-between gap-4 mb-4">
        <h1
            class="text-xl font-bold mb-4 bg-sky-600 text-white px-3 py-1.5 lg:bg-transparent lg:px-0 lg:py-0 lg:text-sky-600 capitalize">
            {{ localize('trending_post') }}
        </h1>
    </div>
    <div class="lg:flex lg:gap-6 space-y-8 lg:space-y-0">

        <section class=" lg:w-[60%]">
            <a href="{{ __url($trendingNews[0]->encode_title) }}"
                class="bg_gradient_top flex-1  block w-full h-[360px] lg:h-[720px]">
                <figure class="">
                    <img class="w-full h-[360px] lg:h-[720px] object-cover"
                        src="{{ isset($trendingNews[0]->photoLibrary->large_image) ? asset('storage/'.$trendingNews[0]->photoLibrary->large_image) : asset('/assets/news-details-view.png') }}"
                        alt="{{ $trendingNews[0]->image_alt }}" />
                </figure>
                <div class="p-2 md:p-5 absolute z-10 bottom-6 left-0">
                    <p class="bg-cyan-100 text-cyan-800 px-3 py-1 inline-block">
                        {{ $trendingNews[0]->category->category_name }}
                    </p>
                    <h1 href="{{ __url($trendingNews[0]->encode_title) }}" class="text-white hover:text-sky-600 dark:hover:text-sky-600 text-2xl lg:text-3.5xl my-2 line-clamp-2">
                        {{ $trendingNews[0]->title }}
                    </h1>

                    <div class="flex gap-1.5 items-center mt-6">
                        <figure class="w-8 h-8 rounded-full overflow-hidden">
                            <img class="w-full h-full object-cover"
                                src="{{ isset($trendingNews[0]->postByUser->profile_image) ? asset('storage/' . $trendingNews[0]->postByUser->profile_image) : asset('/assets/profile.png') }}"
                                alt="{{ $trendingNews[0]->postByUser->full_name ?? localize('unknown') }}">
                        </figure>
                        <p class="space-x-3 text-white">
                            <span>{{ localize('by') }} - {{ $trendingNews[0]->postByUser->full_name ?? localize('unknown') }}</span>,
                            <span>{{ $trendingNews[0]->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                            <span>{{ news_publish_date_format($trendingNews[0]->publish_date) }}</span>
                        </p>
                    </div>
                </div>
            </a>
        </section>

        <!-- Right Section -->
        <section class="w-full lg:w-[40%] border dark:border-neutral-700 p-4">
            {{-- Latest Post Section --}}
            <div class="space-y-4">
                @if ($trendingNews->slice(1)->isNotEmpty())
                    @foreach ($trendingNews->slice(1) as $trendingAllData)
                
                        <div class="relative">
                            <p class="px-2 py-1 font-semibold text-white" {!! bgColorStyle($trendingAllData->category->color_code) !!}>
                                {{ $trendingAllData->category->category_name }}
                            </p>
                            <a href="{{ __url($trendingAllData->encode_title) }}"
                                class="text-lg hover:text-sky-600 dark:hover:text-sky-600 dark:text-neutral-50 font-semibold line-clamp-2 my-3 transition_3">
                                {{ $trendingAllData->title }}
                            </a>
                            <div class="flex gap-1.5 items-center dark:text-neutral-50">
                                <figure class="w-8 h-8 rounded-full overflow-hidden">
                                    <img class="w-full h-full object-cover"
                                        src="{{ isset($trendingAllData->postByUser->profile_image) ? asset('storage/' . $trendingAllData->postByUser->profile_image) : asset('/assets/profile.png') }}"
                                        alt="{{ $trendingAllData->postByUser->full_name ?? localize('unknown') }}">
                                </figure>
                                <p class="space-x-3">
                                    <span>{{ localize('by') }} - {{ $trendingAllData->postByUser->full_name ?? localize('unknown') }}</span>,
                                    <span>{{ $trendingAllData->reader_hit ?? 0 }} {{ localize('read') }}</span>,
                                    <span>{{ news_publish_date_format($trendingAllData->publish_date) }}</span>
                                </p>
                            </div>
                        </div>
                        <hr class="h-px last:hidden bg-neutral-200 border-0 dark:bg-neutral-700" />
                    @endforeach
                @endif
            </div>
        </section>
    </div>
</section>
