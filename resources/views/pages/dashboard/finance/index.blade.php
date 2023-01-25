@include('pages.dashboard.finance.detail.status')
<div class="d-flex justify-content-between align-items-center">
    <ul class="nav nav-tabs">
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('finance','outstanding-payment')">
            <div class="nav-link finance outstanding-payment active">Outstanding Payment</div>
        </li>
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('finance','revenue')">
            <div class="nav-link finance revenue">Revenue</div>
        </li>
    </ul>
</div>
<section id="outstanding-payment" class="dashboard-finance">
    @include('pages.dashboard.finance.detail.outstanding-payment')
</section>
<section id="revenue" class="dashboard-finance d-none">
    @include('pages.dashboard.finance.detail.revenue')
</section>
