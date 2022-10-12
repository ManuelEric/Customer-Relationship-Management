<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Star Admin2 </title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <!-- endinject -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('dashboard/css/vertical-layout-light/style.css') }}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('dashboard/images/favicon.png') }}" />
</head>

<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
                <div class="me-3">
                    <button class="navbar-toggler navbar-toggler align-self-center" type="button"
                        data-bs-toggle="minimize">
                        <span class="bi bi-list"></span>
                    </button>
                </div>
                <div>
                    <a class="navbar-brand brand-logo" href="index.html">
                        <img src="{{ asset('dashboard/images/logo.svg') }}" alt="logo" />
                    </a>
                    <a class="navbar-brand brand-logo-mini" href="index.html">
                        <img src="{{ asset('dashboard/images/logo-mini.svg') }}" alt="logo" />
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
                            <img class="img-xs rounded-circle" src="{{ asset('dashboard/images/faces/face8.jpg') }}"
                                alt="Profile image"> </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                            <div class="dropdown-header text-center">
                                <img class="img-md rounded-circle" src="{{ asset('dashboard/images/faces/face8.jpg') }}"
                                    alt="Profile image">
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
                        <a class="nav-link" href="index.html">
                            <i class="bi bi-speedometer2 mx-2"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item nav-category">Pages</li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#master" aria-expanded="false"
                            aria-controls="master">
                            <i class="bi bi-bookmark mx-2"></i>
                            <span class="menu-title">Master</span>
                            <i class="menu-arrow bi bi-arrow-right"></i>
                        </a>
                        <div class="collapse" id="master">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/employee') }}">Program</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/employee') }}">Lead
                                        Source</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ url('/client') }}">Universities</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">Vendors</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">Assets</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ url('/program') }}">Department</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">Purchase
                                        Request</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#client" aria-expanded="false"
                            aria-controls="client">
                            <i class="bi bi-people-fill mx-2"></i>
                            <span class="menu-title">Client</span>
                            <i class="menu-arrow bi bi-arrow-right"></i>
                        </a>
                        <div class="collapse" id="client">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/client') }}">Mentees</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/employee') }}">Parents</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ url('/program') }}">Teacher/Counselor</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#instance" aria-expanded="false"
                            aria-controls="ui-basic">
                            <i class="bi bi-building mx-2"></i>
                            <span class="menu-title">Instance</span>
                            <i class="menu-arrow bi bi-arrow-right"></i>
                        </a>
                        <div class="collapse" id="instance">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/client') }}">School</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ url('/employee') }}">Corporate</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">Referral</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">Edufair</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#program" aria-expanded="false"
                            aria-controls="program">
                            <i class="bi bi-calendar2-event mx-2"></i>
                            <span class="menu-title">Program</span>
                            <i class="menu-arrow bi bi-arrow-right"></i>
                        </a>
                        <div class="collapse" id="program">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/employee') }}">Client
                                        Program</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">Corporate
                                        Program</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">School
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
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/client') }}">Client
                                        Program</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">Corporate
                                        Program
                                    </a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">School
                                        Program
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
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/client') }}">Client
                                        Program</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">Corporate
                                        Program
                                    </a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">School
                                        Program
                                    </a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">Referral
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
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/client') }}">Employee</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">Mentor
                                    </a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">Tutor
                                    </a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">Editor
                                    </a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">Volunteer
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
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/client') }}">Sales
                                        Tracking</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ url('/program') }}">PPH Final
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
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="{{ asset('dashboard/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- inject:js -->
    <script src="{{ asset('dashboard/js/off-canvas.js') }}"></script>
    <script src="{{ asset('dashboard/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('dashboard/js/template.js') }}"></script>
    <script src="{{ asset('dashboard/js/settings.js') }}"></script>
    <!-- endinject -->
</body>

</html>
