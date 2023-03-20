@extends('app')
@section('css')
    <link rel="stylesheet" href="{{ asset('library/dashboard/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/step.css') }}">
@endsection
@section('script')
    <script src="{{ asset('library/dashboard/js/off-canvas.js') }}"></script>
    <script src="{{ asset('library/dashboard/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('library/dashboard/js/template.js') }}"></script>
    <script src="{{ asset('library/dashboard/js/settings.js') }}"></script>
    <script src="{{ asset('library/dashboard/js/jquery.cookie.js') }}" type="text/javascript"></script>

    {{-- <script src="{{ asset('assets/dist/pspdfkit.js')}}"></script> --}}

    {{-- Sheet Js --}}
    <script lang="javascript" src="https://cdn.sheetjs.com/xlsx-0.19.2/package/dist/xlsx.full.min.js"></script>
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
                        <img src="{{ asset('img/logo.png') }}" alt="logo" class="h-auto" />
                    </a>
                    <a class="navbar-brand brand-logo-mini" href="{{ url('dashboard') }}">
                        <img src="{{ asset('library/dashboard/images/logo-mini.svg') }}" alt="logo" />
                    </a>
                </div>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-top border-bottom">
                <ul class="navbar-nav">
                    <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
                        <h1 class="welcome-text">Welcome Back, <span class="text-black fw-bold">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span></h1>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown d-none d-lg-block user-dropdown">
                        <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <img class="img-xs rounded-circle" src="{{ asset('library/dashboard/images/faces/face8.jpg') }}"
                                alt="Profile image"> </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                            <div class="dropdown-header text-center">
                                <img class="img-md rounded-circle" src="{{ asset('library/dashboard/images/faces/face8.jpg') }}"
                                    alt="Profile image">
                                <p class="mb-1 mt-3 font-weight-semibold">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                                <p class="fw-light text-muted mb-0">{{ Auth::user()->email }}</p>
                            </div>
                            <a class="dropdown-item">
                                <i class="bi bi-person text-primary me-2"></i>
                                My
                                Profile
                                <a class="dropdown-item" href="{{route('logout')}}">
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
                        <a @class(['nav-link', 'text-primary' => Request::is('dashboard')]) href="{{ url('dashboard') }}">
                            <i class="bi bi-speedometer2 mx-2"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item nav-category">Pages</li>
    
                    @foreach ($menus as $key => $menu)
                    <li class="nav-item">
                        <a class="nav-link  {{ Request::is(strtolower($key).'*') ? 'text-primary' : '' }}" data-bs-toggle="collapse"
                            href="#{{strtolower($key)}}" aria-expanded="false" aria-controls="{{strtolower($key)}}">
                            <i class="{{$menu[0]['icon']}} mx-2"></i>
                            <span class="menu-title">{{$key}}</span>
                            <i class="menu-arrow bi bi-arrow-right"></i>
                        </a>
                        <div class="collapse {{ Request::is(strtolower($key).'*') ? 'show' : 'hide' }}" id="{{strtolower($key)}}">
                            <ul class="nav flex-column sub-menu">
                                @foreach ($menu as $key => $submenu)
                                    <li class="nav-item"> <a
                                        class="nav-link {{ Request::is($submenu['submenu_link'] . '*') ? 'active' : '' }}"
                                        href="{{ url($submenu['submenu_link']) }}">{{$submenu['submenu_name']}}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    @endforeach

                    @if ($isAdmin)
                    <li class="nav-item nav-category">Settings</li>
                    <li class="nav-item">
                        <a href="{{url('menus')}}" class="nav-link {{ Request::is('menus') ? 'text-primary' : '' }}">
                            <i class="bi bi-list mx-2"></i>
                            <span class="menu-title">Menus</span>
                            <i class="menu-arrow bi bi-arrow-right"></i>
                        </a>
                    </li>
                    @endif
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
