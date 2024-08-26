@extends('layout.main')

@section('title', 'Invoice of Client Program')
@section('css')
    <link rel="stylesheet" href="{{ asset('library/dashboard/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">
@endsection
@section('style')
    <style>
        .bg-warning-soft {
            background-color: #FFEAAD !important;
        }

        .bg-danger-soft {
            background-color: #DBA4A9 !important;
        }

        .bg-primary-soft {
            background-color: #ADCCFC !important;
        }
    </style>
@endsection

@section('content')

    <div class="card bg-secondary mb-1 p-2">
        <div class="row align-items-center justify-content-between">
            <div class="col-md-6">
                <h5 class="text-white m-0">
                    <i class="bi bi-tag me-1"></i>
                    Invoice of Client Program
                </h5>
            </div>
        </div>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <x-invoice.program.nav :activeMenu="$status" />

            @includeWhen($status == 'needed' && !$isBundle, 'pages.invoice.client-program.detail.invoice-needed')
            @includeWhen($status == 'needed' && $isBundle, 'pages.invoice.client-program.detail.invoice-bundle-needed')

            @includeWhen($status == 'list' && !$isBundle, 'pages.invoice.client-program.detail.invoice-list')
            @includeWhen($status == 'list' && $isBundle, 'pages.invoice.client-program.detail.invoice-bundle-list')

            @includeWhen($status == 'reminder' && !$isBundle, 'pages.invoice.client-program.detail.invoice-reminder')
            @includeWhen($status == 'reminder' && $isBundle, 'pages.invoice.client-program.detail.invoice-bundle-reminder')

            

        </div>
    </div>
@endsection
