@extends('app')
@section('title', 'STEM + Wonderlab - Referral Page')
@section('css')
    <link type="text/css" rel="stylesheet" href="https://fastly.jsdelivr.net/jquery.jssocials/1.4.0/jssocials.css" />

    <link type="text/css" rel="stylesheet"
        href="https://fastly.jsdelivr.net/jquery.jssocials/1.4.0/jssocials-theme-classic.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection
@push('styles')
    <style>
        .bg-eduall {
            background: #0C0F38 !important;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>
@endpush
@section('body')
    <section>
        <div class="container-fluid">
            <div class="row justify-content-center align-items-center" style="height: 100vh">
                <div class="col-md-4 mb-4">
                    <div class="d-flex justify-content-center py-3">
                        <img loading="lazy"  src="{{asset('img/makerspace/stem-logo-allin-color.webp')}}" alt="STEM+ Wonderlab" class="w-50" >
                    </div>
                    <div class="card bg-eduall shadow">
                        <div class="card-body p-1">
                            <textarea name="" id="bar" class="form-control" rows="15">"Hi! I'm inviting you & your family to *STEM+ Wonderlab*, Indonesia's FIRST Student Makerspace Expo where our children can dive into *advanced tools, connect* with fellow innovators, collaborate on cool projects, and make a real impact on global issues!

📍{{ strip_tags($event->event_location) }}
🗓 {{ date('l, d M Y', strtotime($event->event_startdate)) }} | {{ date('g A', strtotime($event->event_startdate)) }} onwards

As my invited guest, enjoy {{ $notes }} privileges, such as
 - Priority access via the dedicated {{ $notes }} lane and fast-track entry for your child to explore event booths.
 - Exclusive merchandise courtesy of ALL-in.
 - Exclusive access to a range of special promotions and premium products.

Come with me to the event by clicking this link: {{ $link }} See you there!
                            </textarea>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <button class="btn btn-sm text-white" data-clipboard-action="copy" data-clipboard-target="#bar"
                            style="background: #233469;" onclick="alert('Copied')">
                            <i class="bi bi-clipboard-check"></i>
                            Copy & Share
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                     {{-- Instruction  --}}
                    <div class="card shadow mb-3">
                        <div class="card-header" style="background: #233469;">
                            <h6 class="p-0 m-0 text-white  d-flex justify-content-between">
                                Instructions
                                <i class="bi bi-info-circle me-2"></i>
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    Copy & paste this message to your friends and their families through WhatsApp, Facebook,
                                    and other platforms
                                </li>
                                <li class="list-group-item">
                                    Advise your invitees to present their QR code at the event day for seamless and
                                    exclusive privileges at STEM+ Wonderlab
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Benefit  --}}
                    <div class="card shadow">
                        <div class="card-header" style="background: #233469;">
                            <h6 class="p-0 m-0 text-white  d-flex justify-content-between">
                                Your Benefits
                                <i class="bi bi-info-circle me-2"></i>
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    Share the invitation to your friends and their families, and gain what your child needs for a
                                    profile boost: <br><br>
                                    - Personal Branding Website <br>
                                    - Exclusive Professional Photoshoot
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function() {
            new ClipboardJS('.btn');

            tinymce.remove('#bar');
        });
    </script>
@endsection
