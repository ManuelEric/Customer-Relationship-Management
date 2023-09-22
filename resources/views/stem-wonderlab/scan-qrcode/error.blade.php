@extends('app')
@section('title', 'Confirmation')
@push('styles')
    <style>
        input {
            border: 0 !important;
            border-bottom: 1px solid #16236a !important;
            border-radius: 0 !important;
            outline: none !important;
            box-shadow: none !important;
            padding: 3px 0 !important;
        }

        .iti {
            display: block !important;
        }

        #phoneUser1, #phoneUser2 {
            margin-left: 20%;
            width: 80%;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
@endpush
@section('body')
    <section>
        <div class="container-fluid my-3" style="height: 90vh">
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-md-8">
                    <div class="min-h-screen d-flex align-items-center bg-gray-200">
                        <div class="max-w-screen-md w-full mx-auto p-4 text-center">
                            <h2 class="text-3xl mb-4 font-bold">
                                {!! $message !!}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
