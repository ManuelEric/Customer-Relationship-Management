<div class="d-flex align-items-center justify-content-between">
    <div>
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
    </div>
    <div>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="bundleChecked" @checked(Request::get('b') == true)>
            <label class="form-check-label" for="bundleChecked">Bundle</label>
          </div>
    </div>
</div>

<script>
    $('#bundleChecked').click(function() {
        var url = '{{ Request::url() }}';

        var searchParams = new URLSearchParams(window.location.search);
        
        if($('#bundleChecked').is(':checked')){
            searchParams.set('b','true')
            var newParams = searchParams.toString()
        }else{
            searchParams.delete('b')
            var newParams = searchParams.toString()
            
        }

        window.location.href = url + '?' + newParams;
    });
</script>



