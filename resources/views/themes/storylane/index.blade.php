<x-web-layout>
    <!-- Top Category News -->
    @include('themes.storylane.components.top-category-news')

    <div class="container my-8">
        @if ($sectionTwoNews['leftNews']->isNotEmpty())
            @include('themes.storylane.components.blog-category-one')
        @endif
    </div>

    <!-- banner section -->
    <section class="container my-8">
        <picture class="2xl:w-5/6 mx-auto block">
            @php $ad = get_advertisements(1, 1); @endphp

            @if ($ad && $ad->status == 1)
                {!! $ad->embed_code !!}
            @elseif(!$ad)
                <img class="w-full h-full object-cover" src="{{ asset('assets/banner-large.png') }}" alt="" />
            @endif
        </picture>
    </section>

    {{-- grid view start --}}
    @if ($sectionTwoNews['rightNews']->isNotEmpty())
        @php
            $secTwoFirstThree = $sectionTwoNews['rightNews']->take(3);
            $secTwoRemaining  = $sectionTwoNews['rightNews']->skip(3);
        @endphp

        <div class="container my-4">
            <h1 class="text-xl font-bold mb-4 bg-sky-600 text-white px-3 py-1.5 lg:bg-transparent lg:px-0 lg:py-0 lg:text-sky-600">
                {{ $sectionTwoNews['rightNews'][0]->category->category_name }}
            </h1>

            <section class="my-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-4 ">
                {{-- Left Section --}}
                <section class="lg:col-span-2 2xl:col-span-3 space-y-6">

                    @if ($secTwoFirstThree->isNotEmpty())
                        @include('themes.storylane.components.blog-category-two', [
                            'secTwoFirstThreeNews' => $secTwoFirstThree,
                        ])
                    @endif

                    @if ($secTwoRemaining->isNotEmpty())
                        @include('themes.storylane.components.blog-category-three', [
                            'secTwoRemainingNews' => $secTwoRemaining,
                        ])
                    @endif

                    <!-- banner section -->
                    <section class="my-8">
                        <picture class="">
                            @php $ad = get_advertisements(1, 2); @endphp

                            @if ($ad && $ad->status == 1)
                                {!! $ad->embed_code !!}
                            @elseif(!$ad)
                                <img class="w-full h-full object-cover" src="{{ asset('assets/banner-large.png') }}" alt="" />
                            @endif
                        </picture>
                    </section>
                </section>

                <!-- Right section news -->
                <section class=''>
                    <div class="space-y-6 md:sticky md:top-16">
                        <!-- Popular post -->
                        @include('themes.storylane.components.common.popular-post')

                        <!-- Ads section -->
                        <figure class="">
                            @php $ad = get_advertisements(1, 3); @endphp

                            @if ($ad && $ad->status == 1)
                                {!! $ad->embed_code !!}
                            @elseif(!$ad)
                                <img class="w-full h-full object-cover" src="{{ asset('assets/ads-electronic.png') }}" alt="" />
                            @endif
                        </figure>
                    </div>
                </section>
            </section>
        </div>
    @endif
    {{-- grid view end --}}

    @if ($sectionThreeAllNews->isNotEmpty())
        <div class="container my-8">
            @include('themes.storylane.components.category-post', ['categoryNews' => $sectionThreeAllNews])
        </div>
    @endif


    @if ($sectionFourNews->isNotEmpty())
        <div class="container my-8">
            @include('themes.storylane.components.category-post', ['categoryNews' => $sectionFourNews])
        </div>
    @endif


    @if ($sectionFiveNews['firstNews']->isNotEmpty())
        <div class="container my-8">
            @include('themes.storylane.components.category-post', ['categoryNews' => $sectionFiveNews['firstNews']])
        </div>
    @endif


    @if ($sectionFiveNews['secondNews']->isNotEmpty())
        <div class="container my-8">
            @include('themes.storylane.components.category-post', ['categoryNews' => $sectionFiveNews['secondNews']])
        </div>
    @endif


    @if ($sectionFiveNews['thirdNews']->isNotEmpty())
        <div class="container my-8">
            @include('themes.storylane.components.category-post', ['categoryNews' => $sectionFiveNews['thirdNews']])
        </div>
    @endif


    @if ($sectionFiveNews['fourthNews']->isNotEmpty())
        <div class="container my-8">
            @include('themes.storylane.components.category-post', ['categoryNews' => $sectionFiveNews['fourthNews']])
        </div>
    @endif


    @if ($sectionSixNews->isNotEmpty())
        <div class="container my-8">
            @include('themes.storylane.components.category-post', ['categoryNews' => $sectionSixNews])
        </div>
    @endif


    @if (!empty($commonSectionNews))
        @foreach ($commonSectionNews as $commonSectionNewsList)
            <div class="container my-8">
                @include('themes.storylane.components.category-post', ['categoryNews' => $commonSectionNewsList])
            </div>
        @endforeach
    @endif


    {{-- latest post --}}
    @if ($latestNews->isNotEmpty())
        <div class="container my-8">
            <div id="latest-news-wrapper">
                @include('themes.storylane.components.latest-post', ['latestPost' => $latestNews])
            </div>
            
            <div class="flex justify-center">
                <button id="loadMoreBtn" type="button"
                    data-offset="8"
                    class="mt-6 text-center bg-neutral-800 text-white  dark:bg-sky-700 dark:text-sky-200 px-6 py-2 hover:bg-sky-600 dark:hover:bg-sky-800 hover:text-white transition_3">
                    {{ localize('load_more') }}
                </button>
            </div>
        </div>
    @endif

    <input type="hidden" id="latest-post-loding" value="{{ localize('loading') }}">
    <input type="hidden" id="latest-post-url" value="{{ __url('load-more-latest-posts') }}">
    <input type="hidden" id="latest-post-load_more" value="{{ localize('load_more') }}">
    <input type="hidden" id="latest-post-no_more_posts" value="{{ localize('no_more_posts') }}">

</x-web-layout>
