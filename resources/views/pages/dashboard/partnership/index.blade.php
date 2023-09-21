<div class="card bg-secondary mb-1">
    <div class="d-flex justify-content-between align-items-center px-3 text-white">
        <h3 class="mb-0"> Partnership Dashboard</h3>
        <h1><i class="bi bi-building me-2 opacity-50"></i></h1>
    </div>
</div>

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

@includeWhen(!$isAdmin, 'pages.dashboard.sales.detail.client-event')