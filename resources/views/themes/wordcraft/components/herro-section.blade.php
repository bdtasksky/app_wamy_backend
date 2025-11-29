<div class="container max-w-xl xl:max-w-3xl py-8 text-center space-y-6 bg-white md:bg-transparent dark:bg-neutral-800 mt-4 md:mt-0">
    <h1 class="text-2xl xl:text-4xl font-bold dark:text-white">
        {{ $themeSettings->hero_title ?? localize('hero_title_content') }}
    </h1>
    <p class="text-neutral-500 dark:text-neutral-50">
        {{ $themeSettings->hero_description ?? localize('hero_description_content') }}
    </p>

    <form action="{{ __url('search') }}" method="GET" class="search_section  bg-white dark:bg-neutral-800 border dark:border-neutral-700 relative max-w-md mx-auto ">
        <input type="text" required="" name="q" id="qq" placeholder="Search..." autocomplete="off" class="px-3 py-2 w-full bg-transparent dark:text-white pr-12">
        <button type="submit" class="w-9 h-[40px] leading-[40px] flex justify-center items-center text-white bg-neutral-800 xl:bg-sky-600 border border-sky-600 dark:border-neutral-600 dark:bg-neutral-600 absolute right-0 top-0 rtl:right-auto rtl:left-0">
            <!-- SVG here -->
            <svg width="16" height="16" class="rtl:scale-x-[-1]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z" fill="currentColor"></path>
            </svg>
        </button>
    </form>
</div>

