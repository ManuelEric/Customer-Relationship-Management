@extends('layout.email')
@section('header', 'Receipt - Signed')
@section('content')
    <p style="margin:0;">Dear Emil,</p>
    <p>
        Receipt no: {inv_id} has been signed. <br>
        Please find attached the receipt.
    </p>
    <p>
        Thank you <br>
        Regards
    </p>
@endsection
