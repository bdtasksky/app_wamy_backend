<div class="card mb-3">
    <div class="fixed-tab card-header bg-white py-3 pl-0">
        <ul class="nav nav-tabs">

            @can('subcode_read')
                <li class="nav-item">
                    <a class="nav-link mt-0 py-1 {{ request()->routeIs('subcodes.index') ? 'active' : '' }}"
                        href="{{ route('subcodes.index') }}">{{ __('language.subcode') }}</a>
                </li>
            @endcan

            @can('subtype_read')
                <li class="nav-item">
                    <a class="nav-link py-1  pl-0 {{ request()->routeIs('subtypes.index') ? 'active' : '' }}"
                        href="{{ route('subtypes.index') }}">{{ __('language.subtype') }}</a>
                </li>
            @endcan
        </ul>
    </div>
</div>
