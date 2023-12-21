<ul class="nav nav-tabs flex-nowrap overflow-auto w-100 mb-3" style="overflow-y: hidden !important;">
    @env(['local', 'production'])
    <li class="nav-item">
        <a @class([
            'nav-link',
            'text-nowrap',
            'active' => Request::is('client/parent/raw')
        ]) aria-current="page" href="{{ url('client/parent/raw') }}">Raw Data</a>
    </li>
    <li class="nav-item">
        <a @class([
            'nav-link',
            'text-nowrap',
            'active' => Request::is('client/parent') && !Request::get('st')
        ]) aria-current="page" href="{{ url('client/parent') }}">Parents</a>
    </li>
    @endenv

    @env('local')
    <li class="nav-item">
        <a @class([
            'nav-link',
            'text-nowrap',
            'active' => Request::get('st') == "inactive"
        ]) aria-current="page" href="{{ url('client/parent?st=inactive') }}">Inactive</a>
    </li>
    @endenv
</ul>