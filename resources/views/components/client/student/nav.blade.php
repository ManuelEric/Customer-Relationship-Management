<ul class="nav nav-tabs flex-nowrap overflow-auto w-100 mb-3" style="overflow-y: hidden !important;">
    @env(['local', 'production'])
    @if ($isSalesAdmin || $isSuperAdmin)
    <li class="nav-item">
        <a @class([
            'nav-link',
            'text-nowrap',
            'active' => Request::segment(3) == 'raw' && Request::get('st') == NULL
        ]) aria-current="page" href="{{ url('client/student/raw') }}">Raw Data</a>
    </li>
    @endif
    <li class="nav-item">
        <a @class([
            'nav-link',
            'text-nowrap',
            'active' => Request::get('st') == 'new-leads'
        ]) aria-current="page" href="{{ url('client/student?st=new-leads') }}">New Leads</a>
    </li>
    <li class="nav-item">
        <a @class([
            'nav-link',
            'text-nowrap',
            'active' => Request::get('st') == 'potential'
        ]) aria-current="page" href="{{ url('client/student?st=potential') }}">Potential</a>
    </li>
    <li class="nav-item">
        <a @class([
            'nav-link',
            'text-nowrap',
            'active' => Request::get('st') == 'mentee'
        ]) aria-current="page" href="{{ url('client/student?st=mentee') }}">Mentee</a>
    </li>
    <li class="nav-item">
        <a @class([
            'nav-link',
            'text-nowrap',
            'active' => Request::get('st') == 'non-mentee'
        ]) aria-current="page" href="{{ url('client/student?st=non-mentee') }}">Non-Mentee</a>
    </li>
    <li class="nav-item">
        <a @class([
            'nav-link',
            'text-nowrap',
            'active' => Request::segment(3) == NULL && Request::get('st') == NULL
        ]) aria-current="page" href="{{ url('client/student') }}">All</a>
    </li>
    <li class="nav-item">
        <a @class([
            'nav-link',
            'text-nowrap',
            'active' => Request::get('st') == 'inactive'
        ]) aria-current="page" href="{{ url('client/student?st=inactive') }}">Inactive</a>
    </li>
    @endenv
</ul>