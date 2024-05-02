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
            <div class="row align-items-stretch">
                <div class="col-md-9 px-5 position-relative overflow-hidden bg-eduall" style="height: 100vh;">
                    <img src="{{ asset('img/makerspace/asset-1.webp') }}" alt=""
                        class="position-absolute w-25 animate__animated animate__pulse animate__infinite"
                        style="top:-2vh; left:-20vh; --animate-duration:10s">
                    <img src="{{ asset('img/makerspace/asset-2.webp') }}" alt=""
                        class="position-absolute w-25 animate__animated animate__pulse animate__infinite"
                        style="bottom:-7vh; left:-20vh; --animate-duration:10s">
                    <img src="{{ asset('img/makerspace/asset-3.webp') }}" alt=""
                        class="position-absolute w-25 animate__animated animate__pulse animate__infinite"
                        style="top:-7vh; right:-20vh; --animate-duration:10s">
                    <img src="{{ asset('img/makerspace/asset-4.webp') }}" alt=""
                        class="position-absolute w-25 animate__animated animate__pulse animate__infinite"
                        style="bottom:-7vh; right:-20vh; --animate-duration:10s">
                    <img src="{{ asset('img/makerspace/Segitiga.png') }}" alt="" class="position-absolute"
                        width="100px" style="top:40vh; right:-5vh;">

                    <div class="d-flex align-items-center h-100">
                        <div class="row justify-content-center">
                            <div class="col-md-9">
                                <img src="{{ asset('img/makerspace/stem-logo-white.webp') }}" alt="" class="w-100">
                                <h5 class="text-center text-white mt-4" style="font-size: 1.8em;">SCIENCE, TECHNOLOGY,
                                    ENGINEERING, MATHEMATICS AND ART</h5>
                                <button class="btn btn-lg btn-regist btn-block w-100 rounded-pill py-1 shadow-lg mt-3">
                                    Indonesia's FIRST Student Makerspace Expo
                                </button>

                                <div class="row mt-3">
                                    <div class="col-md-6">
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
                                    <div class="col-md-6">
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
                <div class="col-md-3 px-5 py-5">
                    <div class="d-flex align-items-center h-100">
                        <div class="w-100">
                            <a href="{{ url('onsite') }}" class="text-decoration-none text-muted">
                                <div class="card border-0 shadow mb-3">
                                    <div class="card-body text-center">
                                        <div class="d-flex justify-content-center mb-3">
                                            <img src="{{ asset('img/makerspace/ots.png') }}" alt="" class="w-25">
                                        </div>
                                        <h5>Onsite Registration</h5>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ url('scan') }}" class="text-decoration-none text-muted">
                                <div class="card border-0 shadow">
                                    <div class="card-body text-center">
                                        <div class="d-flex justify-content-center mb-3">
                                            <img src="{{ asset('img/makerspace/scanner.png') }}" alt=""
                                                class="w-25">
                                        </div>
                                        <h5>QR Scanner</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
