@extends('layout.main')

@section('title', 'Refund List')

@section('css')
    <link rel="stylesheet" href="{{ asset('library/dashboard/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">
@endsection

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="row align-items-center justify-content-between">
            <div class="col-md-6">
                <h5 class="text-white m-0">
                    <i class="bi bi-tag me-1"></i>
                    Refund List
                </h5>
            </div>
        </div>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs flex-nowrap w-100 overflow-auto mb-3">
                <li class="nav-item">
                    <a @class(['nav-link text-nowrap', 'active' => Request::route('status') == 'needed']) aria-current="page"
                        href="{{ url('invoice/refund/status/needed') }}">Refund
                        Needed</a>
                </li>
                <li class="nav-item">
                    <a @class(['nav-link text-nowrap', 'active' => Request::route('status') == 'list']) href="{{ url('invoice/refund/status/list') }}">Refund List</a>
                </li>
            </ul>
            @if ($status == 'needed')
                @include('pages.invoice.refund.detail.refund-needed')
            @else
                @include('pages.invoice.refund.detail.refund-list')
            @endif

        </div>
    </div>
@endsection
