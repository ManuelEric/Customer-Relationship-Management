@extends('layout.main')

@section('title', 'Dashboard - Bigdata Platform')

@section('content')

    {{-- Sales --}}
    {{-- @include('pages.dashboard.sales.index') --}}
    {{-- Partnership --}}
    @include('pages.dashboard.partnership.index')
    {{-- Finance  --}}
    {{-- @include('pages.dashboard.finance.index') --}}


    <script>
        function dashboardTab(type, tab) {
            $('.dashboard-' + type).addClass('d-none')
            $('#' + tab + '.dashboard-' + type).removeClass('d-none')
            $('.nav-link.' + type).removeClass('active')
            $('.nav-link.' + type + '.' + tab).addClass('active')
        }
    </script>
    <script type="text/javascript">
        $("#cp_employee").on('change', function() {
            if ($(this).val() != "all") {

                const searchParams = new URLSearchParams({'quser': $(this).val()})
                location.href = "?" + searchParams
            } else {
                let url = window.location.href
                let urlObj = new URL(url)
                urlObj.search = ''
                const result = urlObj.toString()
                window.location = result
            }
        })
    </script>
@endsection
