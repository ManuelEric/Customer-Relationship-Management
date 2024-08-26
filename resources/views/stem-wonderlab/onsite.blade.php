@extends('app')
@section('title', 'STEM+ WONDERLAB REGISTRATION')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/registration.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
@endsection
@section('body')
    <section>
        <div class="container-fluid">
            <a href="{{ url('registration') }}" class="btn btn-sm btn-secondary position-absolute"
                style="z-index: 999; top:10px; right:30px;">
                <i class="bi bi-house me-1"></i> Home
            </a>
            <div class="row align-items-stretch">
                <div class="col-md-5 px-5 position-relative overflow-hidden bg-eduall" style="height: 100vh;">
                    <img loading="lazy"  src="{{ asset('img/makerspace/asset-1.webp') }}" alt=""
                        class="position-absolute w-25 animate__animated animate__pulse animate__infinite"
                        style="top:-2vh; left:-10vh; --animate-duration:10s">
                    <img loading="lazy"  src="{{ asset('img/makerspace/asset-2.webp') }}" alt=""
                        class="position-absolute w-25 animate__animated animate__pulse animate__infinite"
                        style="bottom:-7vh; left:-10vh; --animate-duration:10s">
                    <img loading="lazy"  src="{{ asset('img/makerspace/asset-3.webp') }}" alt=""
                        class="position-absolute w-25 animate__animated animate__pulse animate__infinite"
                        style="top:-7vh; right:-10vh; --animate-duration:10s">
                    <img loading="lazy"  src="{{ asset('img/makerspace/asset-4.webp') }}" alt=""
                        class="position-absolute w-25 animate__animated animate__pulse animate__infinite"
                        style="bottom:-7vh; right:-10vh; --animate-duration:10s">
                    <img loading="lazy"  src="{{ asset('img/makerspace/Segitiga.png') }}" alt="" class="position-absolute"
                        width="100px" style="top:40vh; right:-5vh;">

                    <div class="d-flex align-items-center h-100">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <img loading="lazy"  src="{{ asset('img/makerspace/stem-logo-white.webp') }}" alt="" class="w-100">
                                <h5 class="text-center text-white mt-4" style="font-size: 1.2em;">SCIENCE, TECHNOLOGY,
                                    ENGINEERING, MATHEMATICS AND ART</h5>
                                <button class="btn btn-lg btn-regist btn-block w-100 rounded-pill py-1 shadow-lg mt-3"
                                    style="font-size: 1.2em;">
                                    Indonesia's FIRST Student Makerspace Expo
                                </button>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div
                                            class="btn text-white border-orange btn-block w-100 rounded-pill py-1 shadow-lg mt-3">
                                            Passion Project Expo
                                        </div>
                                        <div
                                            class="btn text-white border-blue btn-block w-100 rounded-pill py-1 shadow-lg mt-3">
                                            STEM+ Learning Lab Workshop
                                        </div>
                                        <div
                                            class="btn text-white border-green btn-block w-100 rounded-pill py-1 shadow-lg mt-3">
                                            Hands-On STEM+ TechXperience Demo
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div
                                            class="btn text-white border-pink btn-block w-100 rounded-pill py-1 shadow-lg mt-3">
                                            Collaborative Real-World Challenge
                                        </div>
                                        <div
                                            class="btn text-white border-yellow btn-block w-100 rounded-pill py-1 shadow-lg mt-3">
                                            Parenting Talks & Discussions
                                        </div>
                                        <div
                                            class="btn text-white border-orange btn-block w-100 rounded-pill py-1 shadow-lg mt-3">
                                            University Expo
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7 px-5">
                    <div class="d-flex align-items-center h-100">
                        <div class="w-100">
                            <iframe
                                src="{{ url('form/event?event_name=STEM%20Wonderlab%20Registration%20Form&attend_status=attend&status=ots') }}"
                                frameborder="0" class="w-100 form-embed" width="100%" style="height: 90vh;"
                                id="frameEmbed"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
