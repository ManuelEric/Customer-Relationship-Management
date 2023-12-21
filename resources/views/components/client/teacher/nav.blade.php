<ul class="nav nav-tabs flex-nowrap overflow-auto w-100 mb-3" style="overflow-y: hidden !important;">
    @env(['local', 'production'])
    <li class="nav-item">
        <a @class([
            'nav-link',
            'text-nowrap',
            'active' => Request::is('client/teacher-counselor/raw')
        ]) aria-current="page" href="{{ url('client/teacher-counselor/raw') }}">Raw Data</a>
    </li>
    <li class="nav-item">
        <a @class([
            'nav-link',
            'text-nowrap',
            'active' => Request::is('client/teacher-counselor') && !Request::get('st')
        ]) aria-current="page" href="{{ url('client/teacher-counselor') }}">Teacher</a>
    </li>
    @endenv
    @env('local')
    <li class="nav-item">
        <a @class([
            'nav-link',
            'text-nowrap',
            'active' => Request::get('st') == "inactive"
        ]) aria-current="page" href="{{ url('client/teacher-counselor?st=inactive') }}">Inactive</a>
    </li>
    @endenv
</ul>