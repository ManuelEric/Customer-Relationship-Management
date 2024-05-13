<ul class="nav nav-tabs flex-nowrap w-100 overflow-auto mb-3" style="overflow-y: hidden !important;">
    <li class="nav-item">
        <a class="nav-link text-nowrap {{ $needed ? 'active' : null }}" aria-current="page"
            href="{{ url('invoice/client-program?s=needed') }}">Invoice
            Needed</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-nowrap {{ $list ? 'active' : null }}" aria-current="page"
            href="{{ url('invoice/client-program?s=list') }}">Invoice List</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-nowrap {{ $reminder ? 'active' : null }}"
            href="{{ url('invoice/client-program?s=reminder') }}">Due Date Reminder</a>
    </li>
</ul>