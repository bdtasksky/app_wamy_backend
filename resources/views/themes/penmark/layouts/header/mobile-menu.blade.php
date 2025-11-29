<section class="container xl:hidden">
    <ul class="mobile_category_menu flex items-center gap-4 overflow-x-auto overflow-y-hidden">
        <li>
            <a href="{{ __url('/') }}"
                class="uppercase text-nowrap whitespace-nowrap text-base transition_3 text-neutral-900 dark:text-neutral-50 {{ request()->segment(1) === null ? 'text-sky-600 underline' : '' }}">
                {{ localize('home') }}
            </a>
        </li>
        @foreach ($mainMenus as $menu)
            <li>
                <a href="{{ __url($menu->slug) }}"
                    class="uppercase text-nowrap whitespace-nowrap text-base transition_3 text-neutral-900 dark:text-neutral-50 {{ request()->segment(1) === $menu->slug ? 'text-sky-600 underline' : '' }}">
                    {{ $menu->menu_level }}
                </a>
            </li>
        @endforeach
    </ul>
</section>
  