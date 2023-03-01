{{-- send to internal PIC  --}}
{{-- cc to kak dev + emil  --}}

@extends('layout.email')
@section('header', 'Receipt')
@section('content')
    <p style="margin:0;">Dear {{ ucwords($param['fullname']) }},</p>
    <p>
        Please find attached the payment receipt {{ $param['program_name'] }} for your reference.
    </p>
    <p>
        Thank you <br>
        Regards
    </p>
@endsection
