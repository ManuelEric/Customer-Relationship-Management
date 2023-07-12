{{-- send to internal PIC  --}}
{{-- cc to kak dev + emil  --}}

@extends('layout.email')
@section('header', 'Receipt')
@section('content')
    <p style="margin:0;">Dear Mr./Mrs. {{ ucwords($param['fullname']) }},</p>
    <p>
        Please find attached the payment receipt of <u>{{ $param['program_name'] }}</u> for your reference.
    </p>
    <p>
        Thank you <br>
        Regards
    </p>
@endsection
