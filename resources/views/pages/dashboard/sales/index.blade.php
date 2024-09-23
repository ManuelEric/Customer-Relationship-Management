<div class="card bg-secondary mb-1">
    <div class="d-flex justify-content-between align-items-center px-3 text-white">
        <h3 class="mb-0">Sales Dashboard</h3>
        <h1><i class="bi bi-person me-2 opacity-50"></i></h1>
    </div>
</div>

@include('pages.dashboard.sales.detail.client-status')

@includeWhen(!$isSuperAdmin && !$isSalesAdmin, 'pages.dashboard.sales.detail.leads')

<div class="d-flex flex-md-row flex-column-reverse justify-content-between align-items-center">
    <ul class="nav nav-tabs flex-nowrap w-100 overflow-auto mt-md-0 mt-1" style="overflow-y: hidden !important;">
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('sales','client-program')">
            <div class="nav-link text-nowrap sales client-program active">Client Program</div>
        </li>
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('sales','sales-target')">
            <div class="nav-link text-nowrap sales sales-target">Sales Target</div>
        </li>
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('sales','program-comparison')">
            <div class="nav-link text-nowrap sales program-comparison">Program Comparison</div>
        </li>
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('sales','client-event')">
            <div class="nav-link text-nowrap sales client-event">Client Event</div>
        </li>
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('sales','outstanding-payment')">
            <div class="nav-link text-nowrap sales outstanding-payment">Outstanding Payment</div>
        </li>
    </ul>
    @if (Session::get('user_role') != "Employee")
        <select name="" id="cp_employee" class="select w-25">
            <option value="all">All</option>
            @foreach ($employees as $employee)
                @if ($isSales && $loggedIn_user == $employee->id)
                    <option value="{{ $employee->id }}" @selected($employee->id == Request::get('quser'))>{{ $employee->full_name }}</option>
                @elseif ($isSuperAdmin)
                    <option value="{{ $employee->id }}" @selected($employee->id == Request::get('quser'))>{{ $employee->full_name }}</option>
                @endif
            @endforeach
        </select>
    @endif
</div>

<section id="client-program" class="dashboard-sales">
    @include('pages.dashboard.sales.detail.client-program')
</section>
<section id="sales-target" class="dashboard-sales d-none">
    @include('pages.dashboard.sales.detail.sales-target')
</section>
<section id="program-comparison" class="dashboard-sales d-none">
    @include('pages.dashboard.sales.detail.program-comparison')
</section>
<section id="client-event" class="dashboard-sales d-none">
    @include('pages.dashboard.sales.detail.client-event')
</section>
<section id="outstanding-payment" class="dashboard-sales d-none">
    @include('pages.dashboard.sales.detail.outstanding-payment')
</section>