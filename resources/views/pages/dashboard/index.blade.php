@extends('layout.main')

@section('title', 'Dashboard - Bigdata Platform')

@section('content')

    {{-- Sales --}}
    @include('pages.dashboard.sales.index')
    {{-- Partnership --}}
    {{-- @include('pages.dashboard.partnership.index') --}}
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
@endsection
