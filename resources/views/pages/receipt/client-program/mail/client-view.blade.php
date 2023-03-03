@extends('layout.email')
@section('header', 'Receipt')
@section('content')
    <p style="margin:0;">Dear {{ ucwords($recipient) }},</p>
    <p>
        Please find attached the payment receipt of <u>{{ $program_name }}</u> for your reference.
    </p>
    <p>
        Thank you <br>
        Regards
    </p>
@endsection
