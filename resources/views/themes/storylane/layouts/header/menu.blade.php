<section class="hidden xl:block">
    <ul class="flex items-center gap-1">
        <li> 
            <a 
                href="{{ __url('/') }}"
                class="text-[18px] p-2  
                       text-neutral-900 dark:text-neutral-50 
                       hover:text-sky-600 hover:underline transition_3
                       {{ request()->is('/') ? 'text-sky-600 underline' : '' }}">
                {{ Str::upper(localize('home')) }}
            </a>
        </li>

        @foreach ($mainMenus as $menu)
            <li> 
                <a 
                    href="{{ __url($menu->slug) }}"
                    class="text-[18px] p-2  
                           text-neutral-900 dark:text-neutral-50 
                           hover:text-sky-600 hover:underline transition_3
                           {{ request()->is($menu->slug . '*') ? 'text-sky-600 underline' : '' }}">
                    {{ Str::upper($menu->menu_level) }}
                </a>
            </li>
        @endforeach
    </ul>
</section>
