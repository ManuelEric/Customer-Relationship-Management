@extends('app')
@section('title', 'Registration Form - STEM + Wonderlab')
@section('css')
    <link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/jquery.jssocials/1.4.0/jssocials.css" />

    <link type="text/css" rel="stylesheet"
        href="https://cdn.jsdelivr.net/jquery.jssocials/1.4.0/jssocials-theme-classic.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>
@endpush
@section('body')
    <section>
        <div class="container-fluid">
            <div class="row justify-content-center align-items-center" style="height: 100vh">
                <div class="col-md-4 px-5">
                    <div class="card" style="background: #233469;">
                        <div class="card-body">
                            <textarea name="" id="bar" class="form-control" rows="15">"Hi! I'm inviting you & your family to *STEM+ Wonderlab*, Indonesia's FIRST Student Makerspace Expo where our children can dive into *advanced tools, connect* with fellow innovators, collaborate on cool projects, and make a real impact on global issues!

📍{{ strip_tags($event->event_location) }}
🗓 {{ date('l, d M Y', strtotime($event->event_startdate)) }} | {{ date('g A', strtotime($event->event_startdate)) }} onwards

As my invited guest, enjoy VIP privileges, such as
 - Priority access via the dedicated VIP lane and fast-track entry for your child to explore event booths.
 - Exclusive merchandise courtesy of ALL-in.
 - Exclusive access to a range of special promotions and premium products.

Come with me to the event by clicking this link: {{$link}} See you there!
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
                    {{-- <div class="text-center d-flex align-items-center mb-3 justify-content-between">
                        <input type="url" name="" id="url" value="https://all-inedu.com"
                            class="form-control">
                        <div id="share" class="w-50 text-end"></div>
                    </div> --}}

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

                    {{-- Instruction  --}}
                    <div class="card shadow">
                        <div class="card-header" style="background: #233469;">
                            <h6 class="p-0 m-0 text-white  d-flex justify-content-between">
                                Benefit
                                <i class="bi bi-info-circle me-2"></i>
                            </h6>
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
    </section>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery.jssocials/1.4.0/jssocials.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#share").jsSocials({
                url: $('#url').val(),
                showLabel: false,
                showCount: false,
                shares: ["whatsapp", "facebook", "linkedin"]
            });

            new ClipboardJS('.btn');
            tinymce.remove('#bar');
        });
    </script>
@endsection
