<x-web-layout>
    <!-- Top Category News -->
    @if ($homePageTopNews->isNotEmpty())
        @include('themes.penmark.components.top-category-news')
    @endif

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
    
    @if ($sectionThreeAllNews->isNotEmpty())
        @include ('themes.penmark.components.latest-category-news')
    @endif

    <!-- banner section -->
    <section class="container my-8">
        <picture class="2xl:w-5/6 mx-auto block">
            @php $ad = get_advertisements(1, 2); @endphp

            @if ($ad && $ad->status == 1)
                {!! $ad->embed_code !!}
            @elseif(!$ad)
                <img class="w-full h-full object-cover" src="{{ asset('assets/banner-large.png') }}" alt="" />
            @endif
        </picture>
    </section>

    @if ($trendingNews->isNotEmpty())
        @include('themes.penmark.components.trending-news')
    @endif

    @if ($sectionTwoNews['leftNews']->isNotEmpty())
        <div class="container my-8">
            @include('themes.penmark.components.category-post', ['categoryNews' => $sectionTwoNews['leftNews']])
        </div>
    @endif

    <!-- banner section -->
    <section class="container my-8">
        <picture class="2xl:w-5/6 mx-auto block">
            @php $ad = get_advertisements(1, 3); @endphp

            @if ($ad && $ad->status == 1)
                {!! $ad->embed_code !!}
            @elseif(!$ad)
                <img class="w-full h-full object-cover" src="{{ asset('assets/banner-large.png') }}" alt="" />
            @endif
        </picture>
    </section>

    @if ($sectionTwoNews['rightNews']->isNotEmpty())
        <div class="container my-8">
            @include('themes.penmark.components.category-post', ['categoryNews' => $sectionTwoNews['rightNews']])
        </div>
    @endif


    @if ($sectionFourNews->isNotEmpty())
        <div class="container my-8">
            @include('themes.penmark.components.category-post', ['categoryNews' => $sectionFourNews])
        </div>
    @endif


    @if ($sectionFiveNews['firstNews']->isNotEmpty())
        <div class="container my-8">
            @include('themes.penmark.components.category-post', ['categoryNews' => $sectionFiveNews['firstNews']])
        </div>
    @endif


    @if ($sectionFiveNews['secondNews']->isNotEmpty())
        <div class="container my-8">
            @include('themes.penmark.components.category-post', ['categoryNews' => $sectionFiveNews['secondNews']])
        </div>
    @endif


    @if ($sectionFiveNews['thirdNews']->isNotEmpty())
        <div class="container my-8">
            @include('themes.penmark.components.category-post', ['categoryNews' => $sectionFiveNews['thirdNews']])
        </div>
    @endif


    @if ($sectionFiveNews['fourthNews']->isNotEmpty())
        <div class="container my-8">
            @include('themes.penmark.components.category-post', ['categoryNews' => $sectionFiveNews['fourthNews']])
        </div>
    @endif


    @if ($sectionSixNews->isNotEmpty())
        <div class="container my-8">
            @include('themes.penmark.components.category-post', ['categoryNews' => $sectionSixNews])
        </div>
    @endif


    @if (!empty($commonSectionNews))
        @foreach ($commonSectionNews as $commonSectionNewsList)
            <div class="container my-8">
                @include('themes.penmark.components.category-post', ['categoryNews' => $commonSectionNewsList])
            </div>
        @endforeach
    @endif
 

    @if ($latestNews->isNotEmpty())
        <div class="container my-8">
            <!-- Initial News -->
            <div id="latest-news-wrapper">
                @include('themes.penmark.components.latest-post', ['latestPost' => $latestNews])
            </div>

            <div class="flex justify-center">
                <button id="loadMoreBtn" type="button"
                    data-offset="8"
                    class="text-sky-800 mt-6 text-center bg-sky-200 dark:bg-sky-700 dark:text-sky-200 px-6 py-2 hover:bg-sky-600 dark:hover:bg-sky-800 hover:text-white transition_3">
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
