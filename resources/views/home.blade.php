@extends('app')

@section('title', 'Bigdata Platform')

@section('body')
    <style>
        #main {
            height: 90vh;
        }
    </style>

    <nav class="navbar navbar-expand-lg" style="background: #62A8DC">
        <div class="container">
            <a class="navbar-brand text-white" href="#">
                <div class="d-flex align-items-center">
                    <i class="bi bi-clipboard-data me-1" style="font-size:1.2em"></i>
                    <div class="fw-bold lh-1" style="font-size: 0.6em">
                        BIG<br>DATA
                    </div>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="navbar-nav me-auto mb-2 mb-lg-0">

                </div>
                <div class="d-flex">
                    <a href="{{ url('login') }}" class="btn btn-sm shadow btn-light rounded-pill">
                        <i class="bi bi-box-arrow-in-right me-1"></i>
                        Log In
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section id="main">
        <div class="container h-100">
            <div class="row h-100 align-items-center row-cols-md-2 g-3">
                <div class="col pe-md-5">
                    <div class="text-center mb-3">
                        <img loading="lazy"  src="{{ asset('img/logo.webp') }}" alt="" class="w-50">
                    </div>
                    <div class="card border-0 shadow mb-3">
                        <div class="d-flex align-items-center">
                            <div class="px-2" style="width: 30%">
                                <img loading="lazy"  src="{{ asset('img/vision.webp') }}" alt="ALL-in Vision" class="w-100">
                            </div>
                            <div class="card-body" style="width:70%">
                                <h6 class="fw-light text-start">
                                    <ul>
                                        <li>
                                            To empower students to reach their aspirations by opening access to
                                            top-quality
                                            learning/education opportunities ABROAD such that they can lead an impactful
                                            life in
                                            their role as a member of their community
                                        </li>
                                        <li>
                                            Be a leading student-focused education provider in Indonesia
                                        </li>
                                    </ul>
                                </h6>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow">
                        <div class="d-flex align-items-center">
                            <div class="px-2" style="width: 30%">
                                <img loading="lazy"  src="{{ asset('img/mission.webp') }}" alt="ALL-in Vision" class="w-100">
                            </div>
                            <div class="card-body"style="width: 70%">
                                <h6 class=" mb-3 ps-3">
                                    We achieve our vision through our 4 pillars:
                                </h6>
                                <h6 class="fw-light text-start">
                                    <ul>
                                        <li>
                                            Academic Performance
                                        </li>
                                        <li>
                                            Exploration
                                        </li>
                                        <li>
                                            Personal Branding
                                        </li>
                                        <li>
                                            Communication/ Writing
                                        </li>
                                    </ul>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <h3 class="mb-4 text-center">Core Values</h3>
                    <section class="splide">
                        <div class="splide__track px-5">
                            <ul class="splide__list">
                                <li class="splide__slide">
                                    <img loading="lazy"  src="{{ asset('img/core-values/1-01.webp') }}" class="w-100">
                                </li>
                                <li class="splide__slide">
                                    <img loading="lazy"  src="{{ asset('img/core-values/2-01.webp') }}" class="w-100">
                                </li>
                                <li class="splide__slide">
                                    <img loading="lazy"  src="{{ asset('img/core-values/3-01.webp') }}" class="w-100">
                                </li>
                                <li class="splide__slide">
                                    <img loading="lazy"  src="{{ asset('img/core-values/4-01.webp') }}" class="w-100">
                                </li>
                                <li class="splide__slide">
                                    <img loading="lazy"  src="{{ asset('img/core-values/5-01.webp') }}" class="w-100">
                                </li>
                            </ul>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>

    <script>
        new Splide('.splide', {
            type: 'loop',
            gap: 30,
            autoplay: true,
            interval: 5000,
        }).mount();
    </script>

@endsection
