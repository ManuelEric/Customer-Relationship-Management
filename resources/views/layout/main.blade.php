@extends('app')
@section('css')
    <link rel="stylesheet" href="{{ asset('dashboard-template/css/vertical-layout-light/style.css') }}">
@endsection
@section('script')
    <script src="{{ asset('dashboard-template/js/off-canvas.js') }}"></script>
    <script src="{{ asset('dashboard-template/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('dashboard-template/js/template.js') }}"></script>
    <script src="{{ asset('dashboard-template/js/settings.js') }}"></script>
    <script src="{{ asset('dashboard-template/js/jquery.cookie.js') }}" type="text/javascript"></script>
@endsection

@section('body')
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
                <div class="me-3">
                    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
                        <span class="bi bi-list"></span>
                    </button>
                </div>
                <div>
                    <a class="navbar-brand brand-logo" href="{{ url('dashboard') }}">
                        <img src="{{ asset('dashboard-template/images/logo.svg') }}" alt="logo" />
                    </a>
                    <a class="navbar-brand brand-logo-mini" href="{{ url('dashboard') }}">
                        <img src="{{ asset('dashboard-template/images/logo-mini.svg') }}" alt="logo" />
                    </a>
                </div>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-top border-bottom">
                <ul class="navbar-nav">
                    <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
                        <h1 class="welcome-text">Welcome Back, <span class="text-black fw-bold">John Doe</span></h1>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown d-none d-lg-block user-dropdown">
                        <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <img class="img-xs rounded-circle"
                                src="{{ asset('dashboard-template/images/faces/face8.jpg') }}" alt="Profile image"> </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                            <div class="dropdown-header text-center">
                                <img class="img-md rounded-circle"
                                    src="{{ asset('dashboard-template/images/faces/face8.jpg') }}" alt="Profile image">
                                <p class="mb-1 mt-3 font-weight-semibold">Allen Moreno</p>
                                <p class="fw-light text-muted mb-0">allenmoreno@gmail.com</p>
                            </div>
                            <a class="dropdown-item">
                                <i class="bi bi-person text-primary me-2"></i>
                                My
                                Profile
                                <a class="dropdown-item">
                                    <i class="bi bi-box-arrow-down-left text-primary me-2"></i> Sign
                                    Out</a>
                        </div>
                    </li>
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                    data-bs-toggle="offcanvas">
                    <span class="bi bi-list"></span>
                </button>
            </div>
        </nav>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_settings-panel.html -->
            <div class="theme-setting-wrapper">
                <div id="settings-trigger">
                    <i class="bi bi-gear"></i>
                </div>
                <div id="theme-settings" class="settings-panel">
                    <i class="settings-close bi bi-x"></i>
                    <p class="settings-heading">SIDEBAR SKINS</p>
                    <div class="sidebar-bg-options selected" id="sidebar-light-theme">
                        <div class="img-ss rounded-circle bg-light border me-3"></div>Light
                    </div>
                    <div class="sidebar-bg-options" id="sidebar-dark-theme">
                        <div class="img-ss rounded-circle bg-dark border me-3"></div>Dark
                    </div>
                    <p class="settings-heading mt-2">HEADER SKINS</p>
                    <div class="color-tiles mx-0 px-4">
                        <div class="tiles success"></div>
                        <div class="tiles warning"></div>
                        <div class="tiles danger"></div>
                        <div class="tiles info"></div>
                        <div class="tiles dark"></div>
                        <div class="tiles default"></div>
                    </div>
                </div>
            </div>
            <!-- partial -->
            <!-- partial:partials/_sidebar.html -->
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('dashboard') }}">
                            <i class="bi bi-speedometer2 mx-2"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item nav-category">Pages</li>
                    <li class="nav-item">
                        <a class="nav-link  {{ Request::is('master*') ? 'text-primary' : '' }}" data-bs-toggle="collapse"
                            href="#master" aria-expanded="false" aria-controls="master">
                            <i class="bi bi-bookmark mx-2"></i>
                            <span class="menu-title">Master</span>
                            <i class="menu-arrow bi bi-arrow-right"></i>
                        </a>
                        <div class="collapse {{ Request::is('master*') ? 'show' : 'hide' }}" id="master">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a
                                        class="nav-link {{ Request::is('master/asset*') ? 'active' : '' }}"
                                        href="{{ url('master/asset') }}">Assets</a>
                                </li>
                                <li class="nav-item"> <a
                                        class="nav-link {{ Request::is('master/position*') ? 'active' : '' }}"
                                        href="{{ url('master/position') }}">Position</a>
                                </li>
                                <li class="nav-item"> <a
                                        class="nav-link  {{ Request::is('master/lead*') ? 'active' : '' }}"
                                        href="{{ url('master/lead') }}">Lead
                                        Source</a>
                                </li>
                                <li class="nav-item"> <a
                                        class="nav-link {{ Request::is('master/major*') ? 'active' : '' }}"
                                        href="{{ url('master/major') }}">Major</a>
                                </li>
                                <li class="nav-item"> <a
                                        class="nav-link {{ Request::is('master/program*') ? 'active' : '' }}"
                                        href="{{ url('master/program') }}">Program</a>
                                </li>
                                <li class="nav-item"> <a
                                        class="nav-link {{ Request::is('master/event*') ? 'active' : '' }}"
                                        href="{{ url('master/event') }}">Event</a>
                                </li>

                                <li class="nav-item"> <a
                                        class="nav-link {{ Request::is('master/edufair*') ? 'active' : '' }}"
                                        href="{{ url('master/edufair') }}">Edufair</a>
                                </li>
                                <li class="nav-item"> <a
                                        class="nav-link {{ Request::is('master/purchase*') ? 'active' : '' }}"
                                        href="{{ url('master/purchase') }}">Purchase
                                        Request</a>
                                </li>
                                <li class="nav-item"> <a
                                        class="nav-link {{ Request::is('master/vendor*') ? 'active' : '' }}"
                                        href="{{ url('master/vendor') }}">Vendors</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('client*') ? 'text-primary' : '' }}" data-bs-toggle="collapse"
                            href="#client" aria-expanded="false" aria-controls="client">
                            <i class="bi bi-people-fill mx-2"></i>
                            <span class="menu-title">Client</span>
                            <i class="menu-arrow bi bi-arrow-right"></i>
                        </a>
                        <div class="collapse {{ Request::is('client*') ? 'show' : 'hide' }}" id="client">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a
                                        class="nav-link {{ Request::is('client/student*') ? 'active' : '' }}"
                                        href="{{ url('client/student?=potential') }}">Students</a>
                                </li>
                                <li class="nav-item"> <a
                                        class="nav-link {{ Request::is('client/mentee*') ? 'active' : '' }}"
                                        href="{{ url('client/mentee/active') }}">Mentees</a>
                                </li>
                                <li class="nav-item"> <a
                                        class="nav-link {{ Request::is('client/parent*') ? 'active' : '' }}"
                                        href="{{ url('client/parent') }}">Parents</a>
                                </li>
                                <li class="nav-item"> <a
                                        class="nav-link {{ Request::is('client/teacher*') ? 'active' : '' }}"
                                        href="{{ url('client/teacher') }}">Teacher/Counselor</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('instance*') ? 'text-primary' : '' }}"
                            data-bs-toggle="collapse" href="#instance" aria-expanded="false" aria-controls="ui-basic">
                            <i class="bi bi-building mx-2"></i>
                            <span class="menu-title">Instance</span>
                            <i class="menu-arrow bi bi-arrow-right"></i>
                        </a>
                        <div class="collapse {{ Request::is('instance*') ? 'show' : 'hide' }}" id="instance">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a
                                        class="nav-link {{ Request::is('instance/corporate*') ? 'active' : '' }}"
                                        href="{{ url('instance/corporate') }}">Partner</a>
                                </li>
                                <li class="nav-item"> <a
                                        class="nav-link {{ Request::is('instance/school*') ? 'active' : '' }}"
                                        href="{{ url('instance/school') }}">School</a>
                                </li>
                                <li class="nav-item"> <a
                                        class="nav-link {{ Request::is('instance/university*') ? 'active' : '' }}"
                                        href="{{ url('instance/university') }}">Universities</a>
                                </li>

                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('program*') ? 'text-primary' : '' }}" data-bs-toggle="collapse"
                            href="#program" aria-expanded="false" aria-controls="program">
                            <i class="bi bi-calendar2-event mx-2"></i>
                            <span class="menu-title">Program</span>
                            <i class="menu-arrow bi bi-arrow-right"></i>
                        </a>
                        <div class="collapse {{ Request::is('program*') ? 'show' : 'hide' }}" id="program">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a
                                        class="nav-link {{ Request::is('program/referral*') ? 'active' : '' }}"
                                        href="{{ url('program/referral') }}">Referral</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link  {{ Request::is('program/event*') ? 'active' : '' }}"
                                        href="{{ url('program/event') }}">Client
                                        Event</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::is('program/client*') ? 'active' : '' }}"
                                        href="{{ url('program/client') }}">Client
                                        Program</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::is('program/corporate*') ? 'active' : '' }}"
                                        href="{{ url('program/corporate') }}">Partner
                                        Program</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::is('program/school*') ? 'active' : '' }}"
                                        href="{{ url('program/school') }}">School
                                        Program</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#invoice" aria-expanded="false"
                            aria-controls="invoice">
                            <i class="bi bi-receipt mx-2"></i>
                            <span class="menu-title">Invoice</span>
                            <i class="menu-arrow bi bi-arrow-right"></i>
                        </a>
                        <div class="collapse" id="invoice">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ url('invoice/client-program/status/needed') }}">Client
                                        Program</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ url('invoice/corporate-program/status/needed') }}">Partner
                                        Program
                                    </a>
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ url('invoice/school-program/status/needed') }}">School
                                        Program
                                    </a>
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ url('invoice/referral/status/needed') }}">
                                        Referral
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#receipt" aria-expanded="false"
                            aria-controls="receipt">
                            <i class="bi bi-receipt-cutoff mx-2"></i>
                            <span class="menu-title">Receipt</span>
                            <i class="menu-arrow bi bi-arrow-right"></i>
                        </a>
                        <div class="collapse" id="receipt">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ url('client/program/receipt') }}">Client
                                        Program</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ url('corporate/program/receipt') }}">Partner
                                        Program
                                    </a>
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ url('school/program/receipt') }}">School
                                        Program
                                    </a>
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ url('referral/program/receipt') }}">Referral
                                        Program
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#users" aria-expanded="false"
                            aria-controls="users">
                            <i class="bi bi-person-workspace mx-2"></i>
                            <span class="menu-title">Users</span>
                            <i class="menu-arrow bi bi-arrow-right"></i>
                        </a>
                        <div class="collapse" id="users">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link" href="{{ url('employee') }}">Employee</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('mentor') }}">Mentor
                                    </a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('tutor') }}">Tutor
                                    </a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('editor') }}">Editor
                                    </a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('volunteer') }}">Volunteer
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#report" aria-expanded="false"
                            aria-controls="report">
                            <i class="bi bi-printer mx-2"></i>
                            <span class="menu-title">Report</span>
                            <i class="menu-arrow bi bi-arrow-right"></i>
                        </a>
                        <div class="collapse" id="report">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link" href="{{ url('report/sales') }}">Sales
                                        Tracking</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('report/pph') }}">PPH Final
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </nav>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-sm-12">
                            @yield('content')
                        </div>
                    </div>
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->

                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">ALL-in
                            Eduspace</span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Copyright © 2023. All
                            rights
                            reserved.</span>
                    </div>
                </footer>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
@endsection
