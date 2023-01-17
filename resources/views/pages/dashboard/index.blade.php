@extends('layout.main')

@section('title', 'Dashboard - Bigdata Platform')

@section('content')

    @include('pages.dashboard.sales.client-status')
    <ul class="nav nav-tabs">
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('sales','client-program')">
            <div class="nav-link sales client-program active">Client Program Status</div>
        </li>
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('sales','program-lead')">
            <div class="nav-link sales program-lead">Client Program Leads</div>
        </li>
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('sales','sales-target')">
            <div class="nav-link sales sales-target">Sales Target</div>
        </li>
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('sales','program-comparison')">
            <div class="nav-link sales program-comparison">Program Comparison</div>
        </li>
    </ul>
    <section id="client-program" class="dashboard-sales">
        @include('pages.dashboard.sales.client-program')
    </section>
    <section id="program-lead" class="dashboard-sales d-none">
        @include('pages.dashboard.sales.program-lead')
    </section>
    <section id="sales-target" class="dashboard-sales d-none">
        @include('pages.dashboard.sales.sales-target')
    </section>
    <section id="program-comparison" class="dashboard-sales d-none">
        @include('pages.dashboard.sales.program-comparison')
    </section>

    <script>
        function dashboardTab(type, tab) {
            $('.dashboard-' + type).addClass('d-none')
            $('#' + tab).removeClass('d-none')
            $('.nav-link.' + type).removeClass('active')
            $('.nav-link.' + tab).addClass('active')
        }
    </script>

@endsection
