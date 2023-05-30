@include('pages.dashboard.partnership.detail.partner-status')
<div class="d-flex justify-content-between align-items-center">
    <ul class="nav nav-tabs flex-nowrap">
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('partnership','agenda')">
            <div class="nav-link partnership agenda active">Speaker Agenda</div>
        </li>
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('partnership','partner-program')">
            <div class="nav-link partnership partner-program">Partnership Program</div>
        </li>
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('partnership','program-comparison')">
            <div class="nav-link partnership program-comparison">Program Comparison</div>
        </li>
        <li class="nav-item" style="cursor: pointer" onclick="dashboardTab('partnership','client-event')">
            <div class="nav-link partnership client-event">Client Event</div>
        </li>
    </ul>
</div>

<section id="agenda" class="dashboard-partnership">
    @include('pages.dashboard.partnership.detail.agenda')
</section>
<section id="partner-program" class="dashboard-partnership d-none">
    @include('pages.dashboard.partnership.detail.partner-program')
</section>
<section id="program-comparison" class="dashboard-partnership d-none">
    @include('pages.dashboard.partnership.detail.program-comparison')
</section>
@if (!$isAdmin)
<section id="client-event" class="dashboard-partnership d-none">
    @include('pages.dashboard.sales.detail.client-event')
</section>
@endif