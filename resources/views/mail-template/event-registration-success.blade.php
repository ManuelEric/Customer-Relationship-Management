@extends('layout.email')
@section('header', 'Welcome to the Event')
@section('content')
    <p style="margin:0;">Dear {{ $client['name'] }},</p>
    <p>
        Thanks for joining our Event.
    </p>
    <div>
        {!! QrCode::size(300)->generate($url) !!}
    </div>
@endsection
