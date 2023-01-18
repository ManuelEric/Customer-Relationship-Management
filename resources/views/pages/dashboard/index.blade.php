@extends('layout.main')

@section('title', 'Dashboard - Bigdata Platform')

@section('content')

    {{-- Sales --}}
    @include('pages.dashboard.sales.index')

@endsection
