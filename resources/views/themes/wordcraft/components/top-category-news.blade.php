<div class="overflow-auto">
    <div class="flex gap-12">
        @foreach ($mostNewsCategory as $mostNewsCategoryItem)
            <a href="{{ __url($mostNewsCategoryItem->slug) }}" class="w-[460px] text-center pb-2 group">
                <div>
                    <figure class=" w-[160px] h-[90px] overflow-hidden rounded-[50px]">
                        <img class="w-full h-full object-cover group-hover:scale-110 transition-all duration-500 ease-in-out" 
                            src="{{ (isset($mostNewsCategoryItem->category_imgae) && $mostNewsCategoryItem->category_imgae != '') ? asset('storage/' . $mostNewsCategoryItem->category_imgae) : asset('/assets/category-grid-view.png') }}" 
                            alt="{{ $mostNewsCategoryItem->category_name }}">
                    </figure>
                </div>
                <h1 class="text-xl font-semibold my-2 group-hover:text-sky-600 dark:group-hover:text-sky-600 dark:text-neutral-50 transition_3">
                    {{ $mostNewsCategoryItem->category_name }}
                </h1>
                <p class="text-sm line-clamp-2 group-hover:text-sky-600 dark:group-hover:text-sky-600 dark:text-neutral-50 transition_3">
                    {{ $mostNewsCategoryItem->description }}
                </p>
            </a>
        @endforeach
    </div>
 </div>
