{{-- send to internal PIC  --}}
{{-- cc to kak dev + emil  --}}

@extends('layout.email')
@section('header', 'Invoice')
@section('content')
    <p style="margin:0;">Dear {{ ucwords($param['fullname']) }},</p>
    <p>
        Please find attached the invoice of {{ $param['program_name'] }} for your further action.
    </p>
    <p>
        Thank you <br>
        Regards
    </p>
@endsection