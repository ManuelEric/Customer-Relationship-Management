@extends('layout.email')
@section('header', 'Invoice - Signed')
@section('content')
    <p style="margin:0;">Dear Emil,</p>
    <p>
        Invoice no: {{ $invoice_id }} has been signed. <br>
        Please find attached the invoice.
    </p>
    <p>
        Thank you <br>
        Regards
    </p>
@endsection
