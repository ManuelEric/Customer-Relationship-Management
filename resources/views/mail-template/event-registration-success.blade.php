@extends('layout.email')
@section('header', 'Contract Expiration Notification - '.$title)
@section('content')
    <p style="margin:0;">Dear {{ $client['name'] }},</p>
    <p>
        Thanks for joining our Event.
    </p>
    <div>
        {!! QrCode::size(300)->generate($url) !!}
    </div>
@endsection
