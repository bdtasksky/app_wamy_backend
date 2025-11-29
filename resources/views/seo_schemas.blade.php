{{-- Organization Schema --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "@id": "{{ app_setting()->website }}",
    "name": "{{ app_setting()->title }}",
    "url": "{{ url('/') }}",
    "telephone": "{{ app_setting()->phone }}",
    "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "{{ app_setting()->phone }}",
        "address": "{{ app_setting()->address }}",
        "email": "{{ app_setting()->email }}",
        "url": "{{ url('/') }}"
    },
    "logo": {
        "@type": "ImageObject",
        "url": "{{ app_setting()->logo }}"
    },
    "sameAs": [
        @php
        $validLinks = collect($socialLinks)->filter(fn($item) => !empty($item->link));
        @endphp
        @foreach($validLinks as $social)
        "{{ $social->link }}"{!! !$loop->last ? ',' : '' !!}
        @endforeach
    ]
}
</script>
{{-- Website Schema --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "{{ app_setting()->title }}",
  "url": "{{ url('/') }}",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "{{ url('/search?q={search_term_string}') }}",
    "query-input": "required name=search_term_string"
  }
}
</script>
@if (isset($seoSchemasInfo) && !empty($seoSchemasInfo))
    @php
        $rawContent = $seoSchemasInfo->seoOnpage->meta_description ?? $seoSchemasInfo->news;
        $description = Str::limit(
            preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($rawContent))),
            250
        );

        $image = isset($seoSchemasInfo->photoLibrary->large_image) ? asset('storage/' . $seoSchemasInfo->photoLibrary->large_image) : asset('/assets/news-details-view.png');

        $publishDate = $seoSchemasInfo->publish_date;
        $timeStamp   = $seoSchemasInfo->time_stamp;
        $published = Carbon\Carbon::createFromTimestamp($timeStamp)
            ->setDate(
                substr($publishDate, 0, 4),
                substr($publishDate, 5, 2),
                substr($publishDate, 8, 2)
            );
        $updated = $seoSchemasInfo->last_update;

        $author = $seoSchemasInfo->postByUser->full_name ?? localize('admin');
        $url = __url($seoSchemasInfo->encode_title);

        $category_name = $seoSchemasInfo->category->category_name;
        $category_url = __url($seoSchemasInfo->category->slug);

        $sub_category_name = $seoSchemasInfo->subCategory->category_name ?? null;
        $sub_category_url = isset($seoSchemasInfo->subCategory->slug) ? __url($seoSchemasInfo->subCategory->slug) : null;
    @endphp
    @if (!empty($seoSchemasInfo->schemaPost))
    {{-- NewsArticle Schema --}}
    <script type="application/ld+json">
    {
    "@context": "https://schema.org",
    "@type": "NewsArticle",
    "headline": "{{ $seoSchemasInfo->title }}",
    "description": "{{ $description }}",
    "image": {
        "@type": "ImageObject",
        "url": "{{ $image }}"
    },
    "datePublished": "{{ $published->toW3cString() }}",
    "dateModified": "{{ $updated->toW3cString() }}",
    "author": {
        "@type": "Person",
        "name": "{{ $author }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "{{ app_setting()->title }}",
        "url": "{{ url('/') }}",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ app_setting()->logo }}"
        }
    },
    "mainEntityOfPage": "{{ $url }}"
    }
    </script>
    @endif
    {{-- Breadcrumb Schema --}}
    <script type="application/ld+json">
    {
    "@context":"https://schema.org",
    "@type":"BreadcrumbList",
    "itemListElement":[
        {
        "@type":"ListItem",
        "position":1,
        "item":{
            "@id":"{{ url('/') }}",
            "name":"{{ localize('home') }}"
        }
        },
        {
        "@type":"ListItem",
        "position":2,
        "item":{
            "@id":"{{ $category_url }}",
            "name":"{{ $category_name }}"
        }
        }
        @php
            if ($sub_category_name) {
        @endphp
        ,{
        "@type":"ListItem",
        "position":3,
        "item":{
            "@id":"{{ $sub_category_url }}",
            "name":"{{ $sub_category_name }}"
        }
        }
        @php
        }
        @endphp
        ,{
            "@type":"ListItem",
            "position": {{ $sub_category_name ? 4 : 3 }},
            "item": {
                "@id": "{{ $url }}",
                "name": "{{ $seoSchemasInfo->title }}"
            }
        }
    ]
    }
    </script>
@endif
@if (isset($breadcrumbInfo) && !empty($breadcrumbInfo))
    @php
        $parent_category_name = $breadcrumbInfo->get_parent_category->category_name ?? null;
        $parent_category_url = isset($breadcrumbInfo->get_parent_category->slug) ? __url($breadcrumbInfo->get_parent_category->slug) : null;

        $category_name = $breadcrumbInfo->category_name;
        $category_url = __url($breadcrumbInfo->slug);
    @endphp
    {{-- Breadcrumb Schema --}}
    <script type="application/ld+json">
    {
    "@context":"https://schema.org",
    "@type":"BreadcrumbList",
    "itemListElement":[
        {
        "@type":"ListItem",
        "position":1,
        "item":{
            "@id":"{{ url('/') }}",
            "name":"{{ localize('home') }}"
        }
        }
        @php
            if ($parent_category_name) {
        @endphp
        ,{
        "@type":"ListItem",
        "position":2,
        "item":{
            "@id":"{{ $parent_category_url }}",
            "name":"{{ $parent_category_name }}"
        }
        }
        @php
        }
        @endphp
        ,{
            "@type":"ListItem",
            "position": {{ $parent_category_name ? 3 : 2 }},
            "item": {
                "@id": "{{ $category_url }}",
                "name": "{{ $category_name }}"
            }
        }
    ]
    }
    </script>
@endif
{{-- For Video News --}}
@if (isset($videoSchemasInfo) && !empty($videoSchemasInfo))
    @php
        $rawContent = $videoSchemasInfo->meta_description ?? $videoSchemasInfo->details;
        $description = Str::limit(
            preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($rawContent))),
            250
        );

        $image = isset($videoSchemasInfo->thumbnail_image) ? asset('storage/' . $videoSchemasInfo->thumbnail_image) : asset('/assets/news-details-view.png');

        $publishDate = Carbon\Carbon::parse($videoSchemasInfo->publish_date)->format('Y-m-d');
        $createdAt   = $videoSchemasInfo->created_at;
        $time        = Carbon\Carbon::parse($createdAt)->format('H:i:s');

        // Combine clean date + time
        $published = Carbon\Carbon::parse("$publishDate $time");

        $updated = $videoSchemasInfo->updated_at;

        $author = $videoSchemasInfo->postByUser->full_name ?? localize('admin');
        $url = __url($videoSchemasInfo->encode_title);
    @endphp
    {{-- NewsArticle Schema --}}
    <script type="application/ld+json">
    {
    "@context": "https://schema.org",
    "@type": "NewsArticle",
    "headline": "{{ $videoSchemasInfo->title }}",
    "description": "{{ $description }}",
    "image": {
        "@type": "ImageObject",
        "url": "{{ $image }}"
    },
    "datePublished": "{{ $published->toW3cString() }}",
    "dateModified": "{{ $updated->toW3cString() }}",
    "author": {
        "@type": "Person",
        "name": "{{ $author }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "{{ app_setting()->title }}",
        "url": "{{ url('/') }}",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ app_setting()->logo }}"
        }
    },
    "mainEntityOfPage": "{{ $url }}"
    }
    </script>
    {{-- Breadcrumb Schema --}}
    <script type="application/ld+json">
    {
    "@context":"https://schema.org",
    "@type":"BreadcrumbList",
    "itemListElement":[
        {
            "@type":"ListItem",
            "position":1,
            "item":{
                "@id":"{{ url('/') }}",
                "name":"{{ localize('home') }}"
            }
        },
        {
            "@type":"ListItem",
            "position": 2,
            "item": {
                "@id": "{{ __url('videos') }}",
                "name": "{{ localize('videos') }}"
            }
        }
    ]
    }
    </script>
@endif
@if (isset($brdcVideoInfo) && !empty($brdcVideoInfo))
    {{-- Breadcrumb Schema --}}
    <script type="application/ld+json">
    {
    "@context":"https://schema.org",
    "@type":"BreadcrumbList",
    "itemListElement":[
        {
            "@type":"ListItem",
            "position":1,
            "item":{
                "@id":"{{ url('/') }}",
                "name":"{{ localize('home') }}"
            }
        },
        {
            "@type":"ListItem",
            "position": 2,
            "item": {
                "@id": "{{ __url('videos') }}",
                "name": "{{ localize('videos') }}"
            }
        }
    ]
    }
    </script>
@endif
{{-- For Opinion --}}
@if (isset($opinionSchemasInfo) && !empty($opinionSchemasInfo))
    @php
        $rawContent = $opinionSchemasInfo->meta_description ?? $opinionSchemasInfo->content;
        $description = Str::limit(
            preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($rawContent))),
            250
        );

        $image = isset($opinionSchemasInfo->news_image) ? asset('storage/' . $opinionSchemasInfo->news_image) : asset('/assets/news-details-view.png');

        $published = $opinionSchemasInfo->created_at;
        $updated = $opinionSchemasInfo->updated_at;

        $author = $opinionSchemasInfo->name ?? localize('admin');
        $url = __url($opinionSchemasInfo->encode_title);
    @endphp
    {{-- NewsArticle Schema --}}
    <script type="application/ld+json">
    {
    "@context": "https://schema.org",
    "@type": "NewsArticle",
    "headline": "{{ $opinionSchemasInfo->title }}",
    "description": "{{ $description }}",
    "image": {
        "@type": "ImageObject",
        "url": "{{ $image }}"
    },
    "datePublished": "{{ $published->toW3cString() }}",
    "dateModified": "{{ $updated->toW3cString() }}",
    "author": {
        "@type": "Person",
        "name": "{{ $author }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "{{ app_setting()->title }}",
        "url": "{{ url('/') }}",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ app_setting()->logo }}"
        }
    },
    "mainEntityOfPage": "{{ $url }}"
    }
    </script>
    {{-- Breadcrumb Schema --}}
    <script type="application/ld+json">
    {
    "@context":"https://schema.org",
    "@type":"BreadcrumbList",
    "itemListElement":[
        {
            "@type":"ListItem",
            "position":1,
            "item":{
                "@id":"{{ url('/') }}",
                "name":"{{ localize('home') }}"
            }
        },
        {
            "@type":"ListItem",
            "position": 2,
            "item": {
                "@id": "{{ __url('opinion') }}",
                "name": "{{ localize('opinion') }}"
            }
        }
    ]
    }
    </script>
@endif
{{-- For Archive News --}}
@if (isset($brdcArchiveInfo) && !empty($brdcArchiveInfo))
    {{-- Breadcrumb Schema --}}
    <script type="application/ld+json">
    {
    "@context":"https://schema.org",
    "@type":"BreadcrumbList",
    "itemListElement":[
        {
            "@type":"ListItem",
            "position":1,
            "item":{
                "@id":"{{ url('/') }}",
                "name":"{{ localize('home') }}"
            }
        },
        {
            "@type":"ListItem",
            "position": 2,
            "item": {
                "@id": "{{ __url('archive') }}",
                "name": "{{ localize('archive') }}"
            }
        }
    ]
    }
    </script>
@endif
{{-- For RSS News --}}
@if (isset($brdcRssInfo) && !empty($brdcRssInfo))
    @php
        $feed_name = $brdcRssInfo['feed_name'] ?? null;
        $feed_url = isset($brdcRssInfo['slug']) ? __url($brdcRssInfo['slug']) : null;
    @endphp
    {{-- Breadcrumb Schema --}}
    <script type="application/ld+json">
    {
    "@context":"https://schema.org",
    "@type":"BreadcrumbList",
    "itemListElement":[
        {
            "@type":"ListItem",
            "position":1,
            "item":{
                "@id":"{{ url('/') }}",
                "name":"{{ localize('home') }}"
            }
        },
        {
            "@type":"ListItem",
            "position": 2,
            "item": {
                "@id": "{{ __url('rss-news') }}",
                "name": "{{ localize('rss_news') }}"
            }
        }
        @php
            if ($feed_name) {
        @endphp
        ,{
        "@type":"ListItem",
        "position":3,
        "item":{
            "@id":"{{ $feed_url }}",
            "name":"{{ $feed_name }}"
        }
        }
        @php
        }
        @endphp
    ]
    }
    </script>
@endif
@if (isset($rssSchemasInfo) && !empty($rssSchemasInfo))
    @php
        $rawContent = $rssSchemasInfo['description'];
        $description = Str::limit(
            preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($rawContent))),
            250
        );

        $image = isset($rssSchemasInfo['image']) ? $rssSchemasInfo['image'] : asset('/assets/news-details-view.png');

        $published = Carbon\Carbon::parse($rssSchemasInfo['pubDate']);
        $updated = Carbon\Carbon::parse($rssSchemasInfo['pubDate']);

        $author = $rssSchemasInfo['author'] ?? localize('admin'); 
        $url = __url($rssSchemasInfo['encode_title']);

        $feed_name = $rssSchemasInfo['feed_name'] ?? null;
        $feed_url = isset($rssSchemasInfo['feed_slug']) ? __url($rssSchemasInfo['feed_slug']) : null;
    @endphp
    {{-- NewsArticle Schema --}}
    <script type="application/ld+json">
    {
    "@context": "https://schema.org",
    "@type": "NewsArticle",
    "headline": "{{ $rssSchemasInfo['title'] }}",
    "description": "{{ $description }}",
    "image": {
        "@type": "ImageObject",
        "url": "{{ $image }}"
    },
    "datePublished": "{{ $published->toW3cString() }}",
    "dateModified": "{{ $updated->toW3cString() }}",
    "author": {
        "@type": "Person",
        "name": "{{ $author }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "{{ app_setting()->title }}",
        "url": "{{ url('/') }}",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ app_setting()->logo }}"
        }
    },
    "mainEntityOfPage": "{{ $url }}"
    }
    </script>
    {{-- Breadcrumb Schema --}}
    <script type="application/ld+json">
    {
    "@context":"https://schema.org",
    "@type":"BreadcrumbList",
    "itemListElement":[
        {
            "@type":"ListItem",
            "position":1,
            "item":{
                "@id":"{{ url('/') }}",
                "name":"{{ localize('home') }}"
            }
        },
        {
            "@type":"ListItem",
            "position": 2,
            "item": {
                "@id": "{{ __url('rss-news') }}",
                "name": "{{ localize('rss_news') }}"
            }
        }
        @php
            if ($feed_name) {
        @endphp
        ,{
        "@type":"ListItem",
        "position":3,
        "item":{
            "@id":"{{ $feed_url }}",
            "name":"{{ $feed_name }}"
        }
        }
        @php
        }
        @endphp
    ]
    }
    </script>
@endif