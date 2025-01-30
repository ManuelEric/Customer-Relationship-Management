<ul class="nav nav-tabs flex-nowrap overflow-auto w-100 mb-3" style="overflow-y: hidden !important;">
    @env(['local', 'production'])
        <li class="nav-item">
            <a @class([
                'nav-link',
                'text-nowrap',
                'active' => Request::segment(2) == 'sales'
            ]) aria-current="page" href="{{ url('/dashboard/sales') }}">Sales</a>
        </li>
        <li class="nav-item">
            <a @class([
                'nav-link',
                'text-nowrap',
                'active' => Request::segment(2) == 'partnership'
            ]) aria-current="page" href="{{ url('/dashboard/partnership') }}">Partnership</a>
        </li>
        <li class="nav-item">
            <a @class([
                'nav-link',
                'text-nowrap',
                'active' => Request::segment(2) == 'digital'
            ]) aria-current="page" href="{{ url('/dashboard/digital') }}">Digital</a>
        </li>
        <li class="nav-item">
            <a @class([
                'nav-link',
                'text-nowrap',
                'active' => Request::segment(2) == 'finance'
            ]) aria-current="page" href="{{ url('/dashboard/finance') }}">Finance</a>
        </li>
    @endenv
</ul>