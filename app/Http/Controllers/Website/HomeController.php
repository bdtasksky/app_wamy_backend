<?php

namespace App\Http\Controllers\Website;

use Carbon\Carbon;
use App\Models\NewsMst;
use App\Models\Subscriber;
use Illuminate\Support\Str;
use App\Helpers\ThemeHelper;
use Illuminate\Http\Request;
use App\Models\NewsPositionMap;
use Modules\Page\Entities\Page;
use Modules\Poll\Entities\Poll;
use App\Models\HomePagePosition;
use App\Services\HomeDataService;
use App\Enums\ActivationStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Opinion\Entities\Opinion;
use Modules\Poll\Entities\PollOption;
use Modules\Setting\Entities\Setting;
use Artesaos\SEOTools\Facades\SEOMeta;
use Modules\RssFeeds\Entities\RssFeed;
use Modules\Setting\Entities\Language;
use Artesaos\SEOTools\Facades\SEOTools;
use Modules\Category\Entities\Category;
use Illuminate\Support\Facades\Validator;
use Modules\Archive\Entities\NewsArchive;
use Modules\Setting\Entities\Application;
use Modules\VideoNews\Entities\VideoNews;

class HomeController extends Controller
{
    protected $homeDataService;

    /**
     * Summary of __construct
     * @param \App\Services\HomeDataService $homeDataService
     */
    public function __construct(HomeDataService $homeDataService)
    {
        $this->homeDataService = $homeDataService;
    }

    public function resolvePermalink($path)
    {
        $segments = explode('/', trim($path, '/'));
        $total = count($segments);

        $first = $segments[0] ?? null;
        $last = $segments[$total - 1] ?? null;

        $permalinkType = Application::value('permalink');
        $hasLanguage = $first && Language::where('value', $first)->exists();

        if (in_array($permalinkType, ['category', 'date'])) {

            $param = ($total === 1) ? $first : $last;

            if ($total === 1 && $hasLanguage) {
                return $this->index($first);
            }

            $news = NewsMst::with(['category', 'subCategory'])
                ->where('encode_title', $param)
                ->whereDate('publish_date', '<=', Carbon::today())
                ->where('status', 1)
                ->first();
            if ($news) {
                if ($permalinkType === 'category') {
                    $expectedSegments = $hasLanguage
                        ? ($news->subCategory ? 4 : 3)
                        : ($news->subCategory ? 3 : 2);

                    if ($total < $expectedSegments) {
                        return $this->errorPage();
                    }

                    // Validate category structure
                    $categorySlug = $news->category->slug ?? null;
                    $subCategorySlug = $news->subCategory->slug ?? null;
                    $expectedCat = $hasLanguage ? $segments[1] ?? null : $segments[0] ?? null;
                    $expectedSubCat = $hasLanguage ? $segments[2] ?? null : $segments[1] ?? null;

                    // Ensure category slugs match
                    if ($news->subCategory) {
                        if ($expectedCat !== $categorySlug || $expectedSubCat !== $subCategorySlug) {
                            return $this->errorPage();
                        }
                    } else {
                        if ($expectedCat !== $categorySlug) {
                            return $this->errorPage();
                        }
                    }

                } elseif ($permalinkType === 'date') {
                    // Calculate expected segment positions
                    $yearSegment   = $hasLanguage ? $segments[1] ?? null : $segments[0] ?? null;
                    $monthSegment  = $hasLanguage ? $segments[2] ?? null : $segments[1] ?? null;
                    $daySegment    = $hasLanguage ? $segments[3] ?? null : $segments[2] ?? null;

                    $expectedDateSegments = permalink_date($news->publish_date);
                    [$expectedYear, $expectedMonth, $expectedDay] = explode('/', $expectedDateSegments);

                    // Check total segments
                    $requiredSegments = $hasLanguage ? 5 : 4;
                    if ($total != $requiredSegments) {
                        return $this->errorPage();
                    }

                    // Validate each date part
                    if ($yearSegment != $expectedYear || $monthSegment != $expectedMonth || $daySegment != $expectedDay) {
                        return $this->errorPage();
                    }

                    // If all checks pass, handle normally
                    return $hasLanguage
                        ? $this->_handle($first, $param)
                        : $this->handle($param);
                }

                return $hasLanguage
                    ? $this->_handle($first, $param)
                    : $this->handle($param);
            }

            $archive = NewsArchive::with(['category', 'subCategory'])->where('encode_title', $param)->first();
            if ($archive) {

                $pre_path = Str::slug(strtolower(localize('archive')));
                $expectedPrefix = Str::slug(strtolower($hasLanguage ? $segments[1] ?? null : $segments[0] ?? null));

                if ($pre_path !== $expectedPrefix) {
                    return $this->errorPage();
                }

                if ($permalinkType === 'category') {
                    $categoryIndex = $hasLanguage ? 2 : 1;
                    $subCategoryIndex = $hasLanguage ? 3 : 2;

                    $expectedSegments = $hasLanguage
                        ? ($archive->subCategory ? 5 : 4)
                        : ($archive->subCategory ? 4 : 3);

                    if ($total != $expectedSegments) {
                        return $this->errorPage();
                    }

                    $categorySlug = $archive->category->slug ?? null;
                    $subCategorySlug = $archive->subCategory->slug ?? null;

                    if ($archive->subCategory) {
                        if (($segments[$categoryIndex] ?? null) !== $categorySlug || ($segments[$subCategoryIndex] ?? null) !== $subCategorySlug) {
                            return $this->errorPage();
                        }
                    } else {
                        if (($segments[$categoryIndex] ?? null) !== $categorySlug) {
                            return $this->errorPage();
                        }
                    }

                } elseif ($permalinkType === 'date') {
                    $yearIndex  = $hasLanguage ? 2 : 1;
                    $monthIndex = $hasLanguage ? 3 : 2;
                    $dayIndex   = $hasLanguage ? 4 : 3;

                    [$expectedYear, $expectedMonth, $expectedDay] = explode('/', permalink_date($archive->publish_date));

                    $requiredSegments = $hasLanguage ? 6 : 5;
                    if ($total != $requiredSegments) {
                        return $this->errorPage();
                    }

                    if (($segments[$yearIndex] ?? null) != $expectedYear ||
                        ($segments[$monthIndex] ?? null) != $expectedMonth ||
                        ($segments[$dayIndex] ?? null) != $expectedDay) {
                        return $this->errorPage();
                    }
                }

                return $hasLanguage
                    ? $this->_handle($first, $param)
                    : $this->handle($param);
            }

            $category = Category::with('get_parent_category')->where('slug', $param)->first();
            if ($category) {
                $expectedSegments = $hasLanguage
                    ? ($category->get_parent_category ? 3 : 2)
                    : ($category->get_parent_category ? 2 : 1);

                // Total segment mismatch → invalid URL
                if ($total != $expectedSegments) {
                    return $this->errorPage();
                }

                // If subcategory, verify parent slug
                if ($category->get_parent_category) {
                    $parentSlug = $category->get_parent_category->slug;
                    $actualParentSlug = $hasLanguage ? $segments[1] ?? null : $segments[0] ?? null;

                    if ($parentSlug !== $actualParentSlug) {
                        return $this->errorPage();
                    }
                }

                return $hasLanguage
                    ? $this->_handle($first, $param)
                    : $this->handle($param);
            }

            $pagesData = Page::where('page_slug', $param)->first();
            if ($pagesData) {
                if ($permalinkType === 'category') {
                    $pre_path = Str::slug(strtolower(localize('page')));
                    $expectedPrefix = Str::slug(strtolower($hasLanguage ? $segments[1] ?? null : $segments[0] ?? null));

                    if ($pre_path !== $expectedPrefix || $total < ($hasLanguage ? 3 : 2)) {
                        return $this->errorPage();
                    }
                }

                return $hasLanguage
                    ? $this->_handle($first, $param)
                    : $this->handle($param);
            }

            $videoNewsData = VideoNews::where('encode_title', $param)->first();
            if ($videoNewsData) {
                $pre_path = Str::slug(strtolower(localize('video')));
                $expectedPrefix = Str::slug(strtolower($hasLanguage ? $segments[1] ?? null : $segments[0] ?? null));

                if ($pre_path !== $expectedPrefix) {
                    return $this->errorPage();
                }

                if ($permalinkType === 'category') {
                    if ($total < ($hasLanguage ? 3 : 2)) {
                        return $this->errorPage();
                    }
                } elseif ($permalinkType === 'date') {
                    $yearIndex  = $hasLanguage ? 2 : 1;
                    $monthIndex = $hasLanguage ? 3 : 2;
                    $dayIndex   = $hasLanguage ? 4 : 3;

                    [$expectedYear, $expectedMonth, $expectedDay] = explode('/', permalink_date($videoNewsData->publish_date));

                    $requiredSegments = $hasLanguage ? 6 : 5;
                    if ($total != $requiredSegments) {
                        return $this->errorPage();
                    }

                    if (($segments[$yearIndex] ?? null) != $expectedYear ||
                        ($segments[$monthIndex] ?? null) != $expectedMonth ||
                        ($segments[$dayIndex] ?? null) != $expectedDay) {
                        return $this->errorPage();
                    }
                }

                return $hasLanguage
                    ? $this->_handle($first, $param)
                    : $this->handle($param);
            }

            $opinion = Opinion::where('encode_title', $param)->first();
            if ($opinion) {
                $pre_path = Str::slug(strtolower(localize('opinion')));
                $expectedPrefix = Str::slug(strtolower($hasLanguage ? $segments[1] ?? null : $segments[0] ?? null));

                if ($pre_path !== $expectedPrefix) {
                    return $this->errorPage();
                }

                if ($permalinkType === 'category') {
                    if ($total < ($hasLanguage ? 3 : 2)) {
                        return $this->errorPage();
                    }
                } elseif ($permalinkType === 'date') {
                    $yearIndex  = $hasLanguage ? 2 : 1;
                    $monthIndex = $hasLanguage ? 3 : 2;
                    $dayIndex   = $hasLanguage ? 4 : 3;

                    [$expectedYear, $expectedMonth, $expectedDay] = explode('/', permalink_date($opinion->updated_at));

                    $requiredSegments = $hasLanguage ? 6 : 5;
                    if ($total != $requiredSegments) {
                        return $this->errorPage();
                    }

                    if (($segments[$yearIndex] ?? null) != $expectedYear ||
                        ($segments[$monthIndex] ?? null) != $expectedMonth ||
                        ($segments[$dayIndex] ?? null) != $expectedDay) {
                        return $this->errorPage();
                    }
                }
                
                return $hasLanguage
                    ? $this->_handle($first, $param)
                    : $this->handle($param);
            }

            $rssFeed = RssFeed::where('slug', $param)->first();
            if ($rssFeed) {
                if ($permalinkType === 'category') {
                    $pre_path = Str::slug(strtolower(localize('rss_feeds')));
                    $expectedPrefix = Str::slug(strtolower($hasLanguage ? $segments[1] ?? null : $segments[0] ?? null));

                    if ($pre_path !== $expectedPrefix || $total < ($hasLanguage ? 3 : 2)) {
                        return $this->errorPage();
                    }
                }
                
                return $hasLanguage
                    ? $this->_handle($first, $param)
                    : $this->handle($param);
            }

            if (Str::startsWith($param, 'rss-details-')) {
                $newsDetail = null;
                $feeds = RssFeed::all();
                
                foreach ($feeds as $feed) {
                    $cacheKey = 'rss_feed_items_' . $feed->id;

                    if (Cache::has($cacheKey)) {
                        $newsItems = Cache::get($cacheKey);

                        foreach ($newsItems as $news) {
                            if ($news['encode_title'] === $param) {
                                $newsDetail = $news;
                                break 2;
                            }
                        }
                    }
                }

                if ($newsDetail) {
                    $pre_path = Str::slug(strtolower(localize('rss_feeds')));
                    $expectedPrefix = Str::slug(strtolower($hasLanguage ? $segments[1] ?? null : $segments[0] ?? null));

                    if ($pre_path !== $expectedPrefix) {
                        return $this->errorPage();
                    }

                    if ($permalinkType === 'category') {
                        $feed_name = Str::slug(strtolower($newsDetail['feed_name']));
                        $expectedPrefix = Str::slug(strtolower($hasLanguage ? $segments[2] ?? null : $segments[1] ?? null));

                        if ($feed_name !== $expectedPrefix || $total < ($hasLanguage ? 4 : 3)) {
                            return $this->errorPage();
                        }
                    } elseif ($permalinkType === 'date') {
                        $yearIndex  = $hasLanguage ? 2 : 1;
                        $monthIndex = $hasLanguage ? 3 : 2;
                        $dayIndex   = $hasLanguage ? 4 : 3;

                        [$expectedYear, $expectedMonth, $expectedDay] = explode('/', permalink_date($newsDetail['pubDate']));

                        $requiredSegments = $hasLanguage ? 6 : 5;
                        if ($total != $requiredSegments) {
                            return $this->errorPage();
                        }

                        if (($segments[$yearIndex] ?? null) != $expectedYear ||
                            ($segments[$monthIndex] ?? null) != $expectedMonth ||
                            ($segments[$dayIndex] ?? null) != $expectedDay) {
                            return $this->errorPage();
                        }
                    }
                
                    return $hasLanguage
                        ? $this->_handle($first, $param)
                        : $this->handle($param);

                } else {
                    return $this->errorPage();
                }
            }
        }

        // Default permalink
        if ($total === 1) {
            return $this->handle($first);
        }

        return $hasLanguage
            ? $this->_handle($first, $last)
            : $this->handle($last);
    }

    /**
     * Display a listing of the resource.
     */
    public function index($language='')
    {
        $metaData = Setting::select('details')->where('event','meta')->first();
        $metaInfo = json_decode($metaData->details);

        $title = $metaInfo->title ?? 'News365';
        $description = $metaInfo->meta_description ?? '';
        $metaKeywords = $metaInfo->meta_tag ?? '';

        SEOTools::setTitle($title);
        SEOMeta::addKeyword($metaKeywords);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::twitter()->setTitle($title);

        $data = $this->homeDataService->getHomePageData();

        return ThemeHelper::view('index', $data);
    }

    public function handle($param)
    {
        $language = Language::where('value', $param)->value('value');
        if ($language) {
            return $this->index($language);
        }

        if (Auth::guard('web')->check()) {
            $news = NewsMst::where('encode_title', $param)->whereDate('publish_date', '<=', Carbon::today())->first();
        } else {
            $news = NewsMst::where('encode_title', $param)->whereDate('publish_date', '<=', Carbon::today())->where('status', 1)->first();
        }
        if ($news) {
            return $this->detailNews($param);
        }

        $news = NewsArchive::where('encode_title', $param)->first();
        if ($news) {
            return $this->archiveNewsDetails($param);
        }

        $category = Category::where('slug', $param)->first();
        if ($category) {
            return $this->categoryNews($param);
        }

        $pagesData = Page::where('page_slug', $param)->first();
        if ($pagesData) {
            return $this->customPage($param);
        }

        $videoNewsData = VideoNews::where('encode_title', $param)->first();
        if ($videoNewsData) {
            return $this->VideoDetailsNews($param);
        }

        $opinion = Opinion::where('encode_title', $param)->first();
        if ($opinion) {
            return $this->opinionDetails($param);
        }

        if ($param === 'rss-news') {
            return $this->showRssFeedNews();
        }

        $rssFeed = RssFeed::where('slug', $param)->first();
        if ($rssFeed) {
            return $this->showSingleRssFeedNews($param);
        }

        if (Str::startsWith($param, 'rss-details-')) {
            return $this->rssNewsDetails($param);
        }

        if ($param === 'videos') {
            return $this->videoNewsList();
        }

        return $this->errorPage();
    }

    public function _handle($lang, $param)
    {
        $language = Language::where('value', $lang)->value('value');
        $defaultLang = Language::getDefault()->value;
        if ($language && $language != $defaultLang) {
            
            if (Auth::guard('web')->check()) {
                $news = NewsMst::where('encode_title', $param)->whereDate('publish_date', '<=', Carbon::today())->first();
            } else {
                $news = NewsMst::where('encode_title', $param)->whereDate('publish_date', '<=', Carbon::today())->where('status', 1)->first();
            }

            if ($news) {
                return $this->detailNews($param, $language);
            }

            $news = NewsArchive::where('encode_title', $param)->first();
            if ($news) {
                return $this->archiveNewsDetails($param);
            }

            $category = Category::where('slug', $param)->first();
            if ($category) {
                return $this->categoryNews($param, $language);
            }

            $pagesData = Page::where('page_slug', $param)->first();
            if ($pagesData) {
                return $this->customPage($param, $language);
            }

            $videoNewsData = VideoNews::where('encode_title', $param)->first();
            if ($videoNewsData) {
                return $this->VideoDetailsNews($param, $language);
            }

            $opinion = Opinion::where('encode_title', $param)->first();
            if ($opinion) {
                return $this->opinionDetails($param, $language);
            }

            if ($param === 'rss-news') {
                return $this->showRssFeedNews();
            }

            $rssFeed = RssFeed::where('slug', $param)->first();
            if ($rssFeed) {
                return $this->showSingleRssFeedNews($param);
            }

            if (Str::startsWith($param, 'rss-details-')) {
                return $this->rssNewsDetails($param);
            }

            if ($param === 'videos') {
                return $this->videoNewsList();
            }

            return $this->errorPage();

        } else {
            return redirect('/');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        ThemeHelper::view('auth.login');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(...$params)
    {
        $index = app()->getLocale() == Language::getDefault()->value ? 0 : 1;
        if (!isset($params[$index])) {
            return redirect('/');
        }

        $slug = $params[$index];

        echo $slug;

        return ThemeHelper::view('details');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function detailNews($param, $lang = '')
    {
        $data = $this->homeDataService->getNewsDetails($param);
        $news = $data['newsDetail'];

        $title = $news->seo_title ?? $news->title;
        $rawContent = $news->seoOnpage->meta_description ?? $news->news;
        $description = Str::limit(
            preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($rawContent))),
            250
        );

        $url = __url($news->encode_title);
        $image = isset($news->photoLibrary->image_base_url) ? $news->photoLibrary->image_base_url : asset('/assets/details-lg-image.png');
        $published = $news->publish_date;
        $updated = $news->last_update;

        // -- Basic Meta
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::metatags()->addMeta('article:published_time', $published->toW3cString(), 'property');
        SEOTools::metatags()->addMeta('article:modified_time', $updated->toW3cString(), 'property');
        SEOTools::setCanonical($url);

        if (isset($news->seoOnpage->meta_keyword) && !empty($news->seoOnpage->meta_keyword)) {
            SEOMeta::addKeyword(explode(',', $news->seoOnpage->meta_keyword));
        }

        // -- OpenGraph (Facebook, etc.)
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::opengraph()->setUrl($url);
        SEOTools::opengraph()->addImage($image);
        SEOTools::opengraph()->setType('article');
        SEOTools::opengraph()->addProperty('locale', 'en_US');
        SEOTools::opengraph()->addProperty('article:published_time', $published->toW3cString());
        SEOTools::opengraph()->addProperty('article:modified_time', $updated->toW3cString());

        // -- Twitter Card
        SEOTools::twitter()->setTitle($title);
        SEOTools::twitter()->setDescription($description);
        SEOTools::twitter()->setImage($image);
        SEOTools::twitter()->setType('summary_large_image');

        return ThemeHelper::view('details', $data);
    }

    public function archiveNewsDetails($param)
    {
        $data = $this->homeDataService->getArchiveNewsDetails($param);
        $news = $data['newsDetail'];

        $title = $news->seoOnpage->seo_title ?? $news->title;
        $rawContent = $news->seoOnpage->meta_description ?? $news->news;
        $description = Str::limit(
            preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($rawContent))),
            250
        );

        $url = __url($news->encode_title);
        $image = isset($news->photoLibrary->image_base_url) ? $news->photoLibrary->image_base_url : asset('/assets/details-lg-image.png');
        $published = $news->publish_date;
        $updated = $news->last_update;

        // -- Basic Meta
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::metatags()->addMeta('article:published_time', $published->toW3cString(), 'property');
        SEOTools::metatags()->addMeta('article:modified_time', $updated->toW3cString(), 'property');
        SEOTools::setCanonical($url);

        if (isset($news->seoOnpage->meta_keyword) && !empty($news->seoOnpage->meta_keyword)) {
            SEOMeta::addKeyword(explode(',', $news->seoOnpage->meta_keyword));
        }

        // -- OpenGraph (Facebook, etc.)
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::opengraph()->setUrl($url);
        SEOTools::opengraph()->addImage($image);
        SEOTools::opengraph()->setType('article');
        SEOTools::opengraph()->addProperty('locale', 'en_US');
        SEOTools::opengraph()->addProperty('article:published_time', $published->toW3cString());
        SEOTools::opengraph()->addProperty('article:modified_time', $updated->toW3cString());

        // -- Twitter Card
        SEOTools::twitter()->setTitle($title);
        SEOTools::twitter()->setDescription($description);
        SEOTools::twitter()->setImage($image);
        SEOTools::twitter()->setType('summary_large_image');

        return ThemeHelper::view('details', $data);
    }

    public function categoryNews(string $categorySlug, $lang = '')
    {
        $data = $this->homeDataService->getCategoryNewsView($categorySlug);
        if (!$data) {
            return redirect('/');
        }

        $metaData = Setting::select('details')->where('event','meta')->first();
        $metaInfo = json_decode($metaData->details);

        $title = ucfirst($categorySlug) ?? $metaInfo->title ?? '';
        $description = $data['category']->meta_description ?? $metaInfo->meta_description ?? '';
        $metaKeywords = $data['category']->meta_keyword ?? '';

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        if (isset($metaKeywords) && !empty($metaKeywords)) {
            SEOMeta::addKeyword(explode(',', $metaKeywords));
        }
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::twitter()->setTitle($title);

        return ThemeHelper::view('category_news', $data);
    }

    public function CategoryNewsDetailsListView($categorySlug)
    {
        $data = $this->homeDataService->getCategoryNewsView($categorySlug);

        if (!$data) {
            return redirect('/');
        }

        return ThemeHelper::view('category-details-list-view', $data);
    }

    public function opinionDetails(string $categorySlug, $lang = '')
    {
        $data = $this->homeDataService->getOpinionDetails($categorySlug);
        if (!$data) {
            return redirect('/');
        }
        $opinion = $data['opinion'];

        $title = $opinion->title;
        $rawContent = $opinion->meta_description ?? $opinion->content;
        $description = Str::limit(
            preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($rawContent))),
            250
        );

        $url = __url($opinion->encode_title);
        $image = isset($opinion->news_image) ? asset('storage/' . $opinion->news_image) : asset('/assets/details-lg-image.png');
        $published = $opinion->created_at;
        $updated = $opinion->updated_at;

        // -- Basic Meta
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::metatags()->addMeta('article:published_time', $published->toW3cString(), 'property');
        SEOTools::metatags()->addMeta('article:modified_time', $updated->toW3cString(), 'property');
        SEOTools::setCanonical($url);

        if (isset($opinion->meta_keyword) && !empty($opinion->meta_keyword)) {
            SEOMeta::addKeyword(explode(',', $opinion->meta_keyword));
        }

        // -- OpenGraph (Facebook, etc.)
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::opengraph()->setUrl($url);
        SEOTools::opengraph()->addImage($image);
        SEOTools::opengraph()->setType('article');
        SEOTools::opengraph()->addProperty('locale', 'en_US');
        SEOTools::opengraph()->addProperty('article:published_time', $published->toW3cString());
        SEOTools::opengraph()->addProperty('article:modified_time', $updated->toW3cString());

        // -- Twitter Card
        SEOTools::twitter()->setTitle($title);
        SEOTools::twitter()->setDescription($description);
        SEOTools::twitter()->setImage($image);
        SEOTools::twitter()->setType('summary_large_image');

        return ThemeHelper::view('opinion-details', $data);
    }

    public function storyGallery($param)
    {
        $storyDetail = $this->homeDataService->getStoryDetails($param);

        $metaData = Setting::select('details')->where('event','meta')->first();
        $metaInfo = json_decode($metaData->details);

        $title = localize('web_story_details');
        $description = $metaInfo->meta_description ?? '';
        $metaKeywords = $metaInfo->meta_tag ?? '';

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::twitter()->setTitle($title);

        return ThemeHelper::view('story-gallery', compact('storyDetail'));
    }

    public function storyLangGallery($language = '', $param)
    {
        $storyDetail = $this->homeDataService->getStoryDetails($param);

        $metaData = Setting::select('details')->where('event','meta')->first();
        $metaInfo = json_decode($metaData->details);

        $title = localize('web_story_details');
        $description = $metaInfo->meta_description ?? '';
        $metaKeywords = $metaInfo->meta_tag ?? '';

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::twitter()->setTitle($title);

        return ThemeHelper::view('story-gallery', compact('storyDetail'));
    }
    public function videoNewsList()
    {
        $metaData = Setting::select('details')->where('event','meta')->first();
        $metaInfo = json_decode($metaData->details);

        $title = localize('video_news');
        $description = $metaInfo->meta_description ?? '';
        $metaKeywords = $metaInfo->meta_tag ?? '';

        SEOTools::setTitle($title);
        SEOMeta::addKeyword($metaKeywords);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::twitter()->setTitle($title);

        $data = $this->homeDataService->getVideoNewsList();

        if (!$data) {
            return redirect('/');
        }

        return ThemeHelper::view('video_news_list', $data);
    }
    public function VideoDetailsNews($slug, $language = '')
    {
        $data = $this->homeDataService->getVideoNewsDetails($slug);

        if (!$data) {
            return redirect('/');
        }

        $vedio = $data['videoNews'];

        $title = $vedio->title;
        $rawContent = $vedio->meta_description ?? $vedio->details;
        $description = Str::limit(
            preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($rawContent))),
            250
        );

        $url = __url($vedio->encode_title);
        $image = $vedio->thumbnail_image
                    ? asset('storage/' . $vedio->thumbnail_image)
                    : asset('/assets/thumbnail-image.jpg');
        $published = $vedio->publish_date;
        $updated = $vedio->updated_at;

        // -- Basic Meta
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::metatags()->addMeta('article:published_time', $published->toW3cString(), 'property');
        SEOTools::metatags()->addMeta('article:modified_time', $updated->toW3cString(), 'property');
        SEOTools::setCanonical($url);

        if (isset($opinion->meta_keyword)) {
            SEOMeta::addKeyword(explode(',', $vedio->meta_keyword));
        }

        // -- OpenGraph (Facebook, etc.)
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::opengraph()->setUrl($url);
        SEOTools::opengraph()->addImage($image);
        SEOTools::opengraph()->setType('article');
        SEOTools::opengraph()->addProperty('locale', 'en_US');
        SEOTools::opengraph()->addProperty('article:published_time', $published->toW3cString());
        SEOTools::opengraph()->addProperty('article:modified_time', $updated->toW3cString());

        // -- Twitter Card
        SEOTools::twitter()->setTitle($title);
        SEOTools::twitter()->setDescription($description);
        SEOTools::twitter()->setImage($image);
        SEOTools::twitter()->setType('summary_large_image');

        return ThemeHelper::view('video-details', $data);
    }
    public function Registration()
    {
        $metaData = Setting::select('details')->where('event','meta')->first();
        $metaInfo = json_decode($metaData->details);

        $title = localize('registration');
        $description = $metaInfo->meta_description ?? '';

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::twitter()->setTitle($title);

        $recaptchaData = Setting::select('details')->where('event','google_recaptcha')->first();
        $data['recaptchaInfo'] = json_decode($recaptchaData->details);

        return ThemeHelper::view('registration', $data);
    }

    /**
     * Get category_id(s) by position from home_page_positions table.
     * Always fetch up to 9 positions for the given language_id, ignoring status.
     * For each requested position, return the category_id if status is 1, otherwise null.
     */
    private function getCategoryPositionIds(...$positions)
    {
        $languageId = get_language_id();

        // Fetch up to 9 HomePagePosition records for this language ordered by position_no
        $positionsData = HomePagePosition::where('language_id', $languageId)
            ->orderBy('position_no', 'ASC')
            ->limit(9)
            ->get(['position_no', 'category_id', 'status'])
            ->keyBy('position_no');

        $result = [];

        foreach ($positions as $position) {
            if (isset($positionsData[$position])) {
                $entry = $positionsData[$position];
                $result[] = $entry->status == 1 ? $entry->category_id : null;
            } else {
                $result[] = null;
            }
        }

        return $result;
    }

    /**
     * Summary of getSectionFourNewsAjax
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getSectionFourNewsAjax(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 6;
        $offset = ($page - 1) * $perPage;

        $languageId = get_language_id();
        $categoryId = $this->getCategoryPositionIds(4);

        // Base query
        $baseQuery = NewsPositionMap::where('category_id', $categoryId[0])
            ->whereNotNull('category_position')
            ->where('status', 1)
            ->whereHas('news', function ($query) use ($languageId) {
                $query->where('language_id', $languageId)->where('status', 1)->whereDate('publish_date', '<=', Carbon::today());
            });

        // Count total matching records (for calculating max pages)
        $totalRecords = $baseQuery->count();
        $maxSliders = ceil($totalRecords / $perPage);

        // Fetch paginated results
        $sectionFourNewsAjax = $baseQuery->with(['news' => function ($query) use ($languageId) {
                $query->select(
                    'id', 'news_id', 'encode_title', 'title', 'news', 'image',
                    'image_base_url', 'image_alt', 'image_title',
                    'post_by', 'publish_date', 'status',
                )
                ->withCount('comments')
                ->where('language_id', $languageId)
                ->whereDate('publish_date', '<=', Carbon::today())
                ->where('status', 1)
                ->with(['postByUser:id,full_name', 'comments']);
            }])
            ->orderBy('created_at', 'desc')
            ->orderBy('category_position', 'asc')
            ->skip($offset)
            ->take($perPage)
            ->get();

        return response()->json([
            'html' => view('themes.classic.components.slider.section-four-news-items', compact('sectionFourNewsAjax', 'page'))->render(),
            'maxSliders' => $maxSliders,
        ]);
    }

    public function getSectionFiveFirstNewsAjax(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 5;
        $offset = ($page - 1) * $perPage;

        $languageId = get_language_id();
        $categoryId = $this->getCategoryPositionIds(5);

        // Base query
        $baseQuery = NewsPositionMap::where('category_id', $categoryId[0])
            ->whereNotNull('category_position')
            ->where('status', 1)
            ->whereHas('news', function ($query) use ($languageId) {
                $query->where('language_id', $languageId)->where('status', 1)->whereDate('publish_date', '<=', Carbon::today());
            });

        // Count total matching records (for calculating max pages)
        $totalRecords = $baseQuery->count();
        $maxSliders = ceil($totalRecords / $perPage);

        // Fetch paginated results
        $sectionFiveFirstNewsAjax = $baseQuery->with(['news' => function ($query) use ($languageId) {
                $query->select(
                    'id', 'news_id', 'encode_title', 'title', 'news', 'image',
                    'image_base_url', 'image_alt', 'image_title',
                    'post_by', 'publish_date', 'status',
                )
                ->withCount('comments')
                ->where('language_id', $languageId)
                ->whereDate('publish_date', '<=', Carbon::today())
                ->where('status', 1)
                ->with(['postByUser:id,full_name', 'comments']);
            }])
            ->orderBy('created_at', 'desc')
            ->orderBy('category_position', 'asc')
            ->skip($offset)
            ->take($perPage)
            ->get();

        return response()->json([
            'html' => view('themes.classic.components.slider.section-five-first-news-items', compact('sectionFiveFirstNewsAjax', 'page'))->render(),
            'maxSliders' => $maxSliders,
        ]);
    }

    public function getSectionFiveSecondNewsAjax(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 5;
        $offset = ($page - 1) * $perPage;

        $languageId = get_language_id();
        $categoryId = $this->getCategoryPositionIds(6);

        // Base query
        $baseQuery = NewsPositionMap::where('category_id', $categoryId[0])
            ->whereNotNull('category_position')
            ->where('status', 1)
            ->whereHas('news', function ($query) use ($languageId) {
                $query->where('language_id', $languageId)->where('status', 1)->whereDate('publish_date', '<=', Carbon::today());
            });

        // Count total matching records (for calculating max pages)
        $totalRecords = $baseQuery->count();
        $maxSliders = ceil($totalRecords / $perPage);

        // Fetch paginated results
        $sectionFiveSecondNewsAjax = $baseQuery->with(['news' => function ($query) use ($languageId) {
                $query->select(
                    'id', 'news_id', 'encode_title', 'title', 'news', 'image',
                    'image_base_url', 'image_alt', 'image_title',
                    'post_by', 'publish_date', 'status',
                )
                ->withCount('comments')
                ->where('language_id', $languageId)
                ->whereDate('publish_date', '<=', Carbon::today())
                ->where('status', 1)
                ->with(['postByUser:id,full_name', 'comments']);
            }])
            ->orderBy('created_at', 'desc')
            ->orderBy('category_position', 'asc')
            ->skip($offset)
            ->take($perPage)
            ->get();

        return response()->json([
            'html' => view('themes.classic.components.slider.section-five-second-news-items', compact('sectionFiveSecondNewsAjax', 'page'))->render(),
            'maxSliders' => $maxSliders,
        ]);
    }

    public function getSectionFiveThirdNewsAjax(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 5;
        $offset = ($page - 1) * $perPage;

        $languageId = get_language_id();
        $categoryId = $this->getCategoryPositionIds(7);

        // Base query
        $baseQuery = NewsPositionMap::where('category_id', $categoryId[0])
            ->whereNotNull('category_position')
            ->where('status', 1)
            ->whereHas('news', function ($query) use ($languageId) {
                $query->where('language_id', $languageId)->where('status', 1)->whereDate('publish_date', '<=', Carbon::today());
            });

        // Count total matching records (for calculating max pages)
        $totalRecords = $baseQuery->count();
        $maxSliders = ceil($totalRecords / $perPage);

        // Fetch paginated results
        $sectionFiveThirdNewsAjax = $baseQuery->with(['news' => function ($query) use ($languageId) {
                $query->select(
                    'id', 'news_id', 'encode_title', 'title', 'news', 'image',
                    'image_base_url', 'image_alt', 'image_title',
                    'post_by', 'publish_date', 'status',
                )
                ->withCount('comments')
                ->where('language_id', $languageId)
                ->whereDate('publish_date', '<=', Carbon::today())
                ->where('status', 1)
                ->with(['postByUser:id,full_name', 'comments']);
            }])
            ->orderBy('created_at', 'desc')
            ->orderBy('category_position', 'asc')
            ->skip($offset)
            ->take($perPage)
            ->get();

        return response()->json([
            'html' => view('themes.classic.components.slider.section-five-third-news-items', compact('sectionFiveThirdNewsAjax', 'page'))->render(),
            'maxSliders' => $maxSliders,
        ]);
    }

    public function getSectionFiveFourthNewsAjax(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 5;
        $offset = ($page - 1) * $perPage;

        $languageId = get_language_id();
        $categoryId = $this->getCategoryPositionIds(8);

        // Base query
        $baseQuery = NewsPositionMap::where('category_id', $categoryId[0])
            ->whereNotNull('category_position')
            ->where('status', 1)
            ->whereHas('news', function ($query) use ($languageId) {
                $query->where('language_id', $languageId)->where('status', 1)->whereDate('publish_date', '<=', Carbon::today());
            });

        // Count total matching records (for calculating max pages)
        $totalRecords = $baseQuery->count();
        $maxSliders = ceil($totalRecords / $perPage);

        // Fetch paginated results
        $sectionFiveFourthNewsAjax = $baseQuery->with(['news' => function ($query) use ($languageId) {
                $query->select(
                    'id', 'news_id', 'encode_title', 'title', 'news', 'image',
                    'image_base_url', 'image_alt', 'image_title',
                    'post_by', 'publish_date', 'status',
                )
                ->withCount('comments')
                ->where('language_id', $languageId)
                ->whereDate('publish_date', '<=', Carbon::today())
                ->where('status', 1)
                ->with(['postByUser:id,full_name', 'comments']);
            }])
            ->orderBy('created_at', 'desc')
            ->orderBy('category_position', 'asc')
            ->skip($offset)
            ->take($perPage)
            ->get();

        return response()->json([
            'html' => view('themes.classic.components.slider.section-five-fourth-news-items', compact('sectionFiveFourthNewsAjax', 'page'))->render(),
            'maxSliders' => $maxSliders,
        ]);
    }

    public function ajaxSubscribe(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:subscribers,email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->all()
                ], 422);
            }

            // Save subscriber
            Subscriber::create(['email' => $request->email]);

            return response()->json([
                'success' => true,
                'message' => localize('thanks_for_subscribing!')
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'errors' => ['A database error occurred.']
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function loadMoreCategoryNewsAjax(Request $request)
    {
        $offset = (int) $request->get('offset', 0);
        $limit = 3;

        $categoryId = (int) $request->get('category_id');
        $languageId = get_language_id();

        $news = NewsMst::select([
                'id', 'news_id', 'encode_title', 'title', 'news', 'image',
                'image_base_url', 'image_alt', 'image_title', 'post_by',
                'publish_date', 'status', 'reader_hit'
            ])
            ->withCount('comments')
            ->with(['postByUser:id,full_name,profile_image', 'comments', 'photoLibrary'])
            ->where('category_id', $categoryId)
            ->where('language_id', $languageId)
            ->whereDate('publish_date', '<=', Carbon::today())
            ->where('status', 1)
            ->latest('created_at')
            ->skip($offset)
            ->take($limit)
            ->get();

        $htmlGrid = '';
        $htmlList = '';

        foreach ($news as $single) {
            $htmlGrid .= ThemeHelper::view('single-category-news-card', ['news' => $single])->render();
            $htmlList .= ThemeHelper::view('single-category-news-list', ['news' => $single])->render();
        }

        $totalNewsCount = NewsMst::where('category_id', $categoryId)
                            ->where('language_id', $languageId)
                            ->whereDate('publish_date', '<=', Carbon::today())
                            ->where('status', 1)
                            ->count();

        return response()->json([
            'html_grid' => $htmlGrid,
            'html_list' => $htmlList,
            'count' => $news->count(),
            'hasMore' => $totalNewsCount > ($offset + $news->count()),
        ]);
    }
    public function loadMoreVideoNewsAjax(Request $request)
    {
        $offset = (int) $request->get('offset', 0);
        $limit = 3;

        $languageId = get_language_id();

        $news = VideoNews::with(['postByUser:id,full_name'])
            ->select(['id', 'publish_date', 'total_view', 'encode_title', 'title', 'details', 'thumbnail_image', 'image_alt', 'image_title', 'created_by'])
            ->where('language_id', $languageId)
            ->whereDate('publish_date', '<=', Carbon::today())
            ->where('status', ActivationStatusEnum::ACTIVE)
            ->latest('created_at')
            ->skip($offset)
            ->take($limit)
            ->get();

        $htmlGrid = '';
        $htmlList = '';

        foreach ($news as $single) {
            $htmlGrid .= ThemeHelper::view('single-video-news-card', ['news' => $single])->render();
            $htmlList .= ThemeHelper::view('single-video-news-list', ['news' => $single])->render();
        }

        $totalNewsCount = NewsMst::where('language_id', $languageId)
                            ->whereDate('publish_date', '<=', Carbon::today())
                            ->where('status', 1)
                            ->count();

        return response()->json([
            'html_grid' => $htmlGrid,
            'html_list' => $htmlList,
            'count' => $news->count(),
            'hasMore' => $totalNewsCount > ($offset + $news->count()),
        ]);
    }

    public function ajaxVote(Request $request)
    {
        $request->validate([
            'poll_id'   => 'required|exists:polls,id',
            'option_id' => 'required|exists:poll_options,id',
        ]);

        $votedPolls = session()->get('voted_polls', []);

        // Check if user already voted
        if (in_array($request->poll_id, $votedPolls)) {
            return response()->json(['status' => 'error', 'message' => localize('you_already_voted_for_this_poll') ], 403);
        }

        $option = PollOption::where('poll_id', $request->poll_id)
            ->where('id', $request->option_id)
            ->first();

        if ($option) {
            // Push poll ID into session BEFORE returning
            session()->push('voted_polls', $request->poll_id);

            $option->increment('total_vote');

            return response()->json(['status' => 'success', 'message' => localize('vote_submitted_successfully')]);
        }

        return response()->json(['status' => 'error', 'message' => localize('invalid_vote')], 422);
    }


    public function ajaxVoteResult(Poll $poll)
    {
        $poll->load('options');

        $totalVotes = $poll->options->sum('total_vote');

        $results = $poll->options->map(function ($option) use ($totalVotes) {
            $percentage = $totalVotes > 0 ? round(($option->total_vote / $totalVotes) * 100) : 0;
            return [
                'name'       => $option->name,
                'vote'       => $option->total_vote,
                'percentage' => $percentage,
            ];
        });

        return response()->json([
            'totalVotes' => $totalVotes,
            'results'    => $results,
        ]);
    }

    /**
     * Custom page handler for dynamic pages
     * @param string $param
     */
    public function customPage(string $param, $languageId = null)
    {
        $languageId = get_language_id();
        $pagesData = Page::where('page_slug', $param)
            ->where('language_id', $languageId)
            ->where('publish_date', '<=', Carbon::now())
            ->where('status', 1)
            ->first();

        if (!$pagesData) {
            return redirect('/');
        }

        $metaData = Setting::select('details')->where('event','meta')->first();
        $metaInfo = json_decode($metaData->details);

        $title = $pagesData->title ?? $metaInfo->title ?? '';
        $description = $pagesData->seo_description ?? $metaInfo->meta_description ?? '';
        $metaKeywords = $pagesData->seo_keyword ?? $metaInfo->meta_tag ?? '';

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        if (isset($metaKeywords) && !empty($metaKeywords)) {
            SEOMeta::addKeyword(explode(',', $metaKeywords));
        }
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::twitter()->setTitle($title);

        return ThemeHelper::view('custom-page', [
            'page' => $pagesData,
        ]);
    }

    /**
     * Search for news based on a search term
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function search(Request $request)
    {
        $searchTerm = $request->input('q', '');

        if (empty($searchTerm)) {
            return redirect()->back();
        }

        $data = $this->homeDataService->getSearchResults($searchTerm);

        $metaData = Setting::select('details')->where('event','meta')->first();
        $metaInfo = json_decode($metaData->details);

        $title = localize('search');
        $description = $metaInfo->meta_description ?? '';

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::twitter()->setTitle($title);

        return ThemeHelper::view('search-results', $data);
    }

    public function changeMode($mode)
    {
        if (!in_array($mode, ['light', 'dark'])) {
            return response()->json(['error' => 'Invalid mode'], 400);
        }

        session(['mode' => $mode]);
        return response()->json(['success' => true, 'mode' => $mode], 200);
    }
    public function showRssFeedNews()
    {

        $data = $this->homeDataService->getCategoryViewRssNews();
        if (!$data) {
            return redirect('/');
        }

        $metaData = Setting::select('details')->where('event','meta')->first();
        $metaInfo = json_decode($metaData->details);

        $title = $metaInfo->title ?? localize('rss_news');
        $description = $metaInfo->meta_description ?? '';
        $metaKeywords = $metaInfo->meta_keyword ?? '';

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        if (isset($metaKeywords) && !empty($metaKeywords)) {
            SEOMeta::addKeyword(explode(',', $metaKeywords));
        }
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::twitter()->setTitle($title);

        return ThemeHelper::view('rss_news', $data);
    }

    public function showSingleRssFeedNews($slug)
    {

        $data = $this->homeDataService->getSingleCategoryViewRssNews($slug);
        if (!$data) {
            return redirect('/');
        }

        $metaData = Setting::select('details')->where('event','meta')->first();
        $metaInfo = json_decode($metaData->details);

        $title = $metaInfo->title ?? localize('rss_news');
        $description = $metaInfo->meta_description ?? '';
        $metaKeywords = $metaInfo->meta_keyword ?? '';

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        if (isset($metaKeywords) && !empty($metaKeywords)) {
            SEOMeta::addKeyword(explode(',', $metaKeywords));
        }
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::twitter()->setTitle($title);

        return ThemeHelper::view('rss_category_news', $data);
    }

    public function rssNewsDetails($slug)
    {
        $data = $this->homeDataService->getRssNewsDetails($slug);

        if (!$data) {
            return redirect('/');
        }

        $title = $data['newsDetail']['title'];
        $description = $data['newsDetail']['description'];
        $url = __url($data['newsDetail']['encode_title']);
        $image = $data['newsDetail']['image'];
        $published =  Carbon::parse($data['newsDetail']['pubDate']);
        $updated =  Carbon::parse($data['newsDetail']['pubDate']);


        // -- Basic Meta
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::metatags()->addMeta('article:published_time', $published->toW3cString(), 'property');
        SEOTools::metatags()->addMeta('article:modified_time', $updated->toW3cString(), 'property');
        SEOTools::setCanonical($url);

        // -- OpenGraph (Facebook, etc.)
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::opengraph()->setUrl($url);
        SEOTools::opengraph()->addImage($image);
        SEOTools::opengraph()->setType('article');
        SEOTools::opengraph()->addProperty('locale', 'en_US');
        SEOTools::opengraph()->addProperty('article:published_time', $published->toW3cString());
        SEOTools::opengraph()->addProperty('article:modified_time', $updated->toW3cString());

        // -- Twitter Card
        SEOTools::twitter()->setTitle($title);
        SEOTools::twitter()->setDescription($description);
        SEOTools::twitter()->setImage($image);
        SEOTools::twitter()->setType('summary_large_image');

        return ThemeHelper::view('rss_details', $data);
    }

    public function archiveNews(Request $request)
    {
        $data = $this->homeDataService->getArchiveData($request);

        $metaData = Setting::select('details')->where('event','meta')->first();
        $metaInfo = json_decode($metaData->details);

        $title = localize('archive_news');
        $description = $metaInfo->meta_description ?? '';

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::twitter()->setTitle($title);

        return ThemeHelper::view('archive_news', $data);
    }

    public function storyDetailsNews()
    {
        $data = $this->homeDataService->getStoryNewsData();

        $metaData = Setting::select('details')->where('event','meta')->first();
        $metaInfo = json_decode($metaData->details);

        $title = localize('web_stories');
        $description = $metaInfo->meta_description ?? '';

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::twitter()->setTitle($title);

        return ThemeHelper::view('story_details_news', $data);
    }

    public function storyDetailsLangNews($language = '')
    {
        $data = $this->homeDataService->getStoryNewsData();

        $metaData = Setting::select('details')->where('event','meta')->first();
        $metaInfo = json_decode($metaData->details);

        $title = localize('web_stories');
        $description = $metaInfo->meta_description ?? '';

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::twitter()->setTitle($title);

        return ThemeHelper::view('story_details_news', $data);
    }
    public function errorPage()
    {
        $title = localize('Sorry!_Page_not_found');
        SEOTools::setTitle($title);
        return ThemeHelper::view('error-page');
    }

    public function langErrorPage($language = '')
    {
        $title = localize('Sorry!_Page_not_found');
        SEOTools::setTitle($title);
        return ThemeHelper::view('error-page');
    }
    
    public function loadMoreLatestPosts(Request $request)
    {
        $languageId = get_language_id();
        $offset = $request->input('offset', 0);
        $limit  = 8;

        $news = NewsMst::select([
                'id', 'news_id', 'encode_title', 'title', 'news', 'image',
                'image_base_url', 'image_alt', 'image_title',
                'post_by', 'publish_date', 'status', 'created_at', 'category_id', 'reader_hit'
            ])
            ->withCount('comments')
            ->with(['postByUser:id,full_name,profile_image', 'category', 'comments'])
            ->where('language_id', $languageId)
            ->whereDate('publish_date', '<=', Carbon::today())
            ->where('status', 1)
            ->latest('created_at')
            ->skip($offset)
            ->take($limit)
            ->get();

        // Return rendered HTML of partial
        $view = ThemeHelper::view('components.latest-post-items', compact('news'))->render();

        return response()->json([
            'html' => $view,
            'count' => $news->count()
        ]);
    }

    public function tagToTopicNews($lang = null, $slug = null)
    {
        if ($slug === null) {
            $slug = $lang;
        }

        $data = $this->homeDataService->getTagNews($slug);
        if (!$data) {
            return $this->errorPage();
        }

        $metaData = Setting::select('details')->where('event','meta')->first();
        $metaInfo = json_decode($metaData->details);

        $title = ucfirst($slug) ?? $metaInfo->title ?? '';
        $description = $metaInfo->meta_description ?? '';

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);

        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        SEOTools::twitter()->setTitle($title);

        return ThemeHelper::view('topic_news', $data);
    }

    public function loadMoreTopicNewsAjax(Request $request)
    {
        $offset = (int) $request->get('offset', 0);
        $limit = 3;

        $slug = $request->get('slug');
        $languageId = get_language_id();

        // Fetch news related to this tag
        $newsQuery = NewsMst::select([
                'id', 'news_id', 'encode_title', 'title', 'news', 'image',
                'image_base_url', 'image_alt', 'image_title', 'post_by',
                'publish_date', 'status', 'reader_hit'
            ])
            ->withCount('comments')
            ->with(['postByUser:id,full_name,profile_image', 'comments', 'photoLibrary'])
            ->where('language_id', $languageId)
            ->whereDate('publish_date', '<=', Carbon::today())
            ->where('status', 1)
            ->whereHas('postTags', function($q) use ($slug) {
                $q->where('slug', $slug);
            })
            ->latest('created_at');

        // Fetch current page
        $news = $newsQuery->skip($offset)
                        ->take($limit)
                        ->get();

        $htmlGrid = '';
        $htmlList = '';

        foreach ($news as $single) {
            $htmlGrid .= ThemeHelper::view('single-category-news-card', ['news' => $single])->render();
            $htmlList .= ThemeHelper::view('single-category-news-list', ['news' => $single])->render();
        }

        $totalNewsCount = $newsQuery->count();

        return response()->json([
            'html_grid' => $htmlGrid,
            'html_list' => $htmlList,
            'count' => $news->count(),
            'hasMore' => $totalNewsCount > ($offset + $news->count()),
        ]);
    }

}