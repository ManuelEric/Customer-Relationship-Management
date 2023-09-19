@extends('app')
@section('css')
    <link rel="stylesheet" href="{{ asset('library/dashboard/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/step.css') }}">
@endsection
@push('styles')
    <style>
        @media (min-width: 768px) {
            .position-md-static {
                position: static !important;
            }

            .position-md-relative {
                position: relative !important;
            }

            .position-md-absolute {
                position: absolute !important;
            }

            .position-md-fixed {
                position: fixed !important;
            }

            .position-md-sticky {
                position: sticky !important;
            }
        }
    </style>
@endpush
@push('scripts')
    <script src="{{ asset('library/dashboard/js/off-canvas.js') }}"></script>
    <script src="{{ asset('library/dashboard/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('library/dashboard/js/template.js') }}"></script>
    <script src="{{ asset('library/dashboard/js/settings.js') }}"></script>
    <script src="{{ asset('library/dashboard/js/jquery.cookie.js') }}" type="text/javascript"></script>

    {{-- <script src="{{ asset('assets/dist/pspdfkit.js')}}"></script> --}}

    {{-- Sheet Js --}}
    <script lang="javascript" src="https://cdn.sheetjs.com/xlsx-0.19.2/package/dist/xlsx.full.min.js"></script>)
@endpush

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
                        <h1 class="welcome-text">Welcome Back, <span
                                class="text-black fw-bold">{{ Auth::user()->first_name }}
                                {{ Auth::user()->last_name }}</span></h1>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    
                    @if ($isAdmin || $isSales || $isDigital)
                        <li class="nav-item dropdown d-none d-lg-block user-dropdown me-lg-3 me-0">
                            <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell"></i>
                                @if (isset($countAlarm))
                                    @php
                                        if($isSales){
                                            $count = $countAlarm['sales'];
                                        }elseif($isAdmin){
                                            $count = $countAlarm['general'];
                                        }elseif($isDigital) {
                                            $count = $countAlarm['digital'];
                                        }
                                    @endphp
                                    @if($count > 0)
                                        <span
                                            class="position-absolute ms-1 top-1 start-100 translate-middle badge rounded-pill bg-danger"
                                            style="font-size: 11px">
                                            <small>
                                                {{ $count }}
                                            </small>
                                        </span>
                                    @endif
                                @endif
                            </a>

                            @if (isset($notification))
                                <ul class="dropdown-menu dropdown-menu-right navbar-dropdown py-2 px-4" style="width: 400px;">
                                    @php
                                        if($isSales){
                                            $notification = $notification['sales'];
                                        }elseif($isDigital){
                                            $notification = $notification['digital'];
                                        }elseif($isAdmin){
                                            $notification = $notification['general'];
                                        }
                                    @endphp
                                    @foreach ($notification as $notif)
                                        @if(isset($notif))
                                            <li class="d-flex align-items-center border-bottom py-2">
                                                <i class="bi bi-exclamation-circle me-2"></i>
                                                <span class="lh-sm">
                                                    {!! $notif !!}
                                                </span>
                                            </li>
                                        @endif
                                    @endforeach

                                </ul>
                            @endif
                        </li>
                        
                    @endif
                    <li class="nav-item dropdown d-none d-lg-block user-dropdown">
                        <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <img class="img-xs rounded-circle" src="{{ asset('img/user.png') }}" alt="Profile image"> </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                            <div class="dropdown-header text-center">
                                <img class="img-md rounded-circle w-25" src="{{ asset('img/user.png') }}"
                                    alt="Profile image">
                                <p class="mb-1 mt-3 font-weight-semibold">{{ Auth::user()->first_name }}
                                    {{ Auth::user()->last_name }}</p>
                                <p class="fw-light text-muted mb-0">{{ Auth::user()->email }}</p>
                            </div>
                            <a class="dropdown-item text-center" href="{{ route('logout') }}">
                                <i class="bi bi-box-arrow-down-left text-primary me-2"></i>
                                Sign Out
                            </a>
                        </div>
                    </li>
                </ul>
                <div class="dropdown d-block d-lg-none me-3">
                    <a class="" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell" style="font-size: 2em;"></i>
                        <span class="position-absolute ms-1 top-1 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 11px">
                            <small>
                                5
                            </small>
                        </span>
                    </a>

                    <ul class="dropdown-menu py-2 px-4" style="width: 300px; left:-250px;">
                        @for ($i = 0; $i < 3; $i++)
                            <li class="d-flex align-items-center border-bottom py-2">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <span class="lh-sm">
                                    The number of hot leads is less than the target
                                </span>
                            </li>
                        @endfor
                    </ul>
                </div>
                <div class="dropdown d-block d-lg-none user-dropdown me-0">
                    <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <img class="img-xs rounded-circle" src="{{ asset('img/user.png') }}" alt="Profile image"> </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown"
                        style="left:-150px;">
                        <div class="dropdown-header text-center">
                            <img class="img-md rounded-circle w-25" src="{{ asset('img/user.png') }}" alt="Profile image">
                            <p class="mb-1 mt-3 font-weight-semibold">{{ Auth::user()->first_name }}
                                {{ Auth::user()->last_name }}</p>
                            <p class="fw-light text-muted mb-0">{{ Auth::user()->email }}</p>
                        </div>
                        <a class="dropdown-item text-center" href="{{ route('logout') }}">
                            <i class="bi bi-box-arrow-down-left text-primary me-2"></i>
                            Sign Out
                        </a>
                    </div>
                </div>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                    data-bs-toggle="offcanvas">
                    <span class="bi bi-list"></span>
                </button>
            </div>
        </nav>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.html -->
            <nav class="sidebar sidebar-offcanvas position-md-fixed h-75 overflow-auto pt-3 pe-1" id="sidebar">
                <ul class="nav">
                    <li class="nav-item">
                        <a @class([
                            'nav-link',
                            'bg-secondary text-white' => Request::is('dashboard'),
                        ]) href="{{ url('dashboard') }}">
                            <i class="bi bi-speedometer2 mx-2"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item nav-category">Pages</li>

                    @foreach ($menus as $key => $menu)
                        <li class="nav-item">
                            @php
                                $key = $key == 'Users' ? 'User' : $key;
                            @endphp
                            <a class="nav-link  {{ Request::is(strtolower($key) . '*') ? 'text-primary' : '' }}"
                                data-bs-toggle="collapse" href="#{{ strtolower($key) }}" aria-expanded="false"
                                aria-controls="{{ strtolower($key) }}">
                                <i class="{{ $menu[0]['icon'] }} mx-2"></i>
                                <span class="menu-title">{{ $key }}</span>
                                <i class="menu-arrow bi bi-arrow-right"></i>
                            </a>
                            <div class="collapse {{ Request::is(strtolower($key) . '*') ? 'show' : 'hide' }}"
                                id="{{ strtolower($key) }}">
                                <ul class="nav flex-column sub-menu bg-secondary p-0" style="list-style-type: none;">
                                    @foreach ($menu as $key2 => $submenu)
                                        @php
                                            $submenu_link = $submenu['submenu_link'];
                                            $explode = explode('/', $submenu_link);
                                            $length = count($explode);
                                            if ($length > 2) {
                                                $submenu_link_array = array_slice($explode, 0, 2);
                                                $submenu_link = implode('/', $submenu_link_array);
                                            }
                                        @endphp
                                        @if ($position = strpos($submenu['submenu_link'], '?'))
                                            @php
                                                $submenu_link = substr($submenu['submenu_link'], 0, $position);
                                            @endphp
                                        @endif
                                        <li class="p-0">
                                            <a class="nav-link py-1 m-0 ps-5 border-bottom rounded-0 {{ Request::is($submenu_link . '*') ? 'active bg-info text-white' : 'text-white' }}"
                                                href="{{ url($submenu['submenu_link']) }}">
                                                <i class="bi bi-dash me-2"></i>
                                                {{ $submenu['submenu_name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @endforeach

                    @if ($isAdmin)
                        <li class="nav-item">
                            <a href="{{ url('request-sign?type=invoice') }}"
                                class="nav-link {{ Request::is('request-sign') ? 'bg-secondary text-white' : '' }}">
                                <i class="bi bi-pencil mx-2"></i>
                                <span class="menu-title">Request Sign</span>
                            </a>
                        </li>
                        <li class="nav-item nav-category">Settings</li>
                        <li class="nav-item">
                            <a href="{{ url('menus') }}"
                                class="nav-link {{ Request::is('menus') ? 'bg-secondary text-white' : '' }}">
                                <i class="bi bi-list mx-2"></i>
                                <span class="menu-title">Menus</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>
            <!-- partial -->
            <div class="main-panel position-md-absolute end-0">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-sm-12">
                            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                                <ol class="breadcrumb bg-light border-0 rounded px-2 mb-1 justify-content-end">
                                    @yield('breadcrumb')
                                </ol>
                            </nav>
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
