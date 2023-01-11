@extends('layout.main')

@section('title', 'Dashboard - Bigdata Platform')

@section('content')

    @include('pages.dashboard.sales.client-status')
    @include('pages.dashboard.sales.client-program')
    @include('pages.dashboard.sales.program-lead')

@endsection
