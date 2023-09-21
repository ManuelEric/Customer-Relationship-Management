@extends('app')
@section('title', 'Your QR-Code')
@section('script')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
@endsection
@push('styles')
    <style>
        @font-face {
            font-family: 'nulshock';
            src: url('/img/makerspace/font/nulshock-bd.otf');
            font-display: swap;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'nulshock' !important;
        }

        .qrcode {
            height: 100vh;
        }

        @media only screen and (max-width: 600px) {
            .qrcode {
                padding: 30px;
                height: auto;
            }
        }
    </style>
@endpush
@section('body')
    <section>
        <div class="container-fluid position-relative overflow-hidden">
            <img src="{{ asset('img/makerspace/asset-1.webp') }}" alt=""
                class="position-absolute animate__animated animate__pulse animate__infinite"
                style="top:-2vh; left:-10vh; --animate-duration:10s; width:18%;">
            <img src="{{ asset('img/makerspace/asset-2.webp') }}" alt=""
                class="position-absolute animate__animated animate__pulse animate__infinite"
                style="bottom:-7vh; left:-10vh; --animate-duration:10s; width:18%;">
            <img src="{{ asset('img/makerspace/asset-3.webp') }}" alt=""
                class="position-absolute animate__animated animate__pulse animate__infinite"
                style="top:-7vh; right:-10vh; --animate-duration:10s; width:18%;">
            <img src="{{ asset('img/makerspace/asset-4.webp') }}" alt=""
                class="position-absolute animate__animated animate__pulse animate__infinite"
                style="bottom:-7vh; right:-10vh; --animate-duration:10s; width:18%;">


            <div class="w-100 container d-flex align-items-center qrcode">
                <div class="row align-items-center">
                    <div class="col-md-8 mb-4">
                        <div class="text-center">
                            <div class="d-flex justify-content-center mb-4">
                                <img src="{{ asset('img/makerspace/stem-logo-allin-color.webp') }}" alt=""
                                    class="w-75">
                            </div>
                            <h5>Thank you for joining the STEM+ Wonderlab</h5>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="m-0 p-0">Here your QR Code</p>
                            <button id="dl-qr" class="btn btn-sm text-white rounded-3" style="background: #233469">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>
                        <div id="my-qr">
                            <div class="card shadow mb-3 mt-2" style="background: #233469;">
                                <div class="card-body">
                                    <div class="card bg-light">
                                        <div class="card-body text-center position-relative">
                                            <div
                                                class="w-100 h-100 position-absolute top-0 start-0 d-flex align-items-center justify-content-center">
                                                <div class="bg-white p-2 rounded-3" style="width: 40px;">
                                                    <img src="{{ asset('img/favicon.png') }}" alt="" class="w-100">
                                                </div>
                                            </div>
                                            {!! QrCode::size(200)->generate($url) !!}
                                        </div>
                                    </div>
                                    <div class="text-center mt-3">
                                        <h3 class="m-0 p-0 text-white">SCAN ME</h3>
                                    </div>
                                </div>
                            </div>

                            {{-- Instruction  --}}
                            <div class="mb-2">

                                <div class="card shadow">
                                    <div class="card-header mb-2" style="background: #233469;">
                                        <h5 class="p-0 m-0 text-white  d-flex justify-content-between">
                                            Instructions
                                            <i class="bi bi-info-circle me-2"></i>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">
                                                On the event day, present the QR code to the registration
                                                personnel at the venue to expedite the entry process.
                                            </li>
                                            <li class="list-group-item">
                                                Enjoy our event and take the opportunity to connect with fellow
                                                peers.
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                $("#dl-qr").click(function() {
                    const screenshotTarget = document.getElementById('my-qr');

                    html2canvas(screenshotTarget).then((canvas) => {
                        const base64image = canvas.toDataURL("image/png");
                        var anchor = document.createElement('a');
                        anchor.setAttribute("href", base64image);
                        anchor.setAttribute("download", "my-qr.png");
                        anchor.click();
                        anchor.remove();
                    });
                });
            </script>
    </section>
@endsection
