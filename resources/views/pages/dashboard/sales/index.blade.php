@include('pages.dashboard.sales.detail.client-status')
<div class="d-flex justify-content-between align-items-center">
    <ul class="nav nav-tabs">
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('sales','client-program')">
            <div class="nav-link sales client-program active">Client Program</div>
        </li>
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('sales','sales-target')">
            <div class="nav-link sales sales-target">Sales Target</div>
        </li>
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('sales','program-comparison')">
            <div class="nav-link sales program-comparison">Program Comparison</div>
        </li>
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('sales','client-event')">
            <div class="nav-link sales client-event">Client Event</div>
        </li>
    </ul>
    <select name="" id="" class="select w-25">
        <option value="employee">Employee Name</option>
    </select>
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
