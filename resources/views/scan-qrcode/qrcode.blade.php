@extends('app')
@section('title', 'Your QR-Code')
@section('style')
@endsection
@section('script')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
@endsection
@section('body')
    <section>
        <div class="container-fluid mt-3">
            <div class="row align-items-center justify-content-center" style="height:95vh;">
                
                <div class="col-md-3">
                    <div class="card shadow mb-3" style="background: #233469;">
                        <div class="card-body">
                            <div class="card bg-light">
                                <div class="card-body text-center position-relative">
                                    <div class="w-100 h-100 position-absolute top-0 start-0 d-flex align-items-center justify-content-center">
                                        <div class="bg-white p-2 rounded-3" style="width: 40px;">
                                            <img src="{{asset('img/favicon.png')}}" alt="" class="w-100" >
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
                    <div class="card shadow">
                        <div class="card-header" style="background: #233469;">
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
    </section>
@endsection
