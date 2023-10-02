@extends('app')
@section('title', 'STEM+ WONDERLAB REGISTRATION')
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
@endsection
@push('styles')
    <style>
        @font-face {
            font-family: 'nulshock';
            src: url('/img/makerspace/font/nulshock-bd.otf');
            font-display: swap;
        }

        h2 {
            font-family: 'nulshock' !important;
        }

        a .card {
            transition: all .3s ease-in-out;
        }

        a .card:hover,
        a .card:active {
            background: #0e124a;
            color:#FFFFFF;
        }

        .bg-eduall {
            background: #0C0F38 !important;
        }

        .btn-eduall {
            background: #0C0F38 !important;
            color: #FFFFFF
        }

        .btn-eduall:hover {
            background: #0e124a !important;
            color: #FFFFFF
        }

        .btn-eduall:active {
            background: #ff6708 !important;
            color: #FFFFFF !important;
        }
    </style>
@endpush
@section('body')
    <section>
        <div class="container-fluid">
            <a href="{{url('registration')}}" class="btn btn-sm btn-secondary position-absolute" style="z-index: 999; top:10px; right:30px;">
                <i class="bi bi-house me-1"></i> Home
            </a>
            <div class="row align-items-stretch">
                <div class="col-5 px-5 position-relative overflow-hidden bg-eduall" style="height: 100vh;">
                    <img src="{{ asset('img/makerspace/asset-1.webp') }}" alt=""
                        class="position-absolute animate__animated animate__pulse animate__infinite"
                        style="width:40%; top:-2vh; left:-10vh; --animate-duration:10s">
                    <img src="{{ asset('img/makerspace/asset-2.webp') }}" alt=""
                        class="position-absolute animate__animated animate__pulse animate__infinite"
                        style="width:40%; bottom:-7vh; left:-10vh; --animate-duration:10s">
                    <img src="{{ asset('img/makerspace/asset-3.webp') }}" alt=""
                        class="position-absolute animate__animated animate__pulse animate__infinite"
                        style="width:40%; top:-7vh; right:-10vh; --animate-duration:10s">
                    <img src="{{ asset('img/makerspace/asset-4.webp') }}" alt=""
                        class="position-absolute animate__animated animate__pulse animate__infinite"
                        style="width:40%; bottom:-7vh; right:-10vh; --animate-duration:10s">

                    <div class="d-flex align-items-center h-100">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <img src="{{ asset('img/makerspace/stem-logo-white.webp') }}" alt="" class="w-100">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-7 px-5">
                    <div class="d-flex align-items-center h-100">
                        <div class="w-100">
                            <iframe
                            src="https://crm-allinedu.com/form/event?event_name=STEM%20Wonderlab%20Registration%20Form&attend_status=attend&status=ots"
                            frameborder="0" class="w-100 form-embed" width="100%" style="height: 90vh;"
                            id="frameEmbed"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
