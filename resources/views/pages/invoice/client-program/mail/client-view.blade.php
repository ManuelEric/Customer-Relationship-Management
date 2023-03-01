@extends('layout.email')
@section('header', 'Invoice')
@section('content')
    <p style="margin:0;">Dear {Full Name},</p>
    <p>
        Please find attached the invoice of {Program Main - Sub Program} for your further action.
    </p>
    <p>
        Thank you <br>
        Regards
    </p>
@endsection
