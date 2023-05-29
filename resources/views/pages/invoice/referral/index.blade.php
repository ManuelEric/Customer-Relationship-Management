@extends('layout.main')

@section('title', 'Referral Invoice - Bigdata Platform')

@section('css')
    <link rel="stylesheet" href="{{ asset('library/dashboard/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">
@endsection

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Invoice
        </a>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs flex-nowrap mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ $status =='needed' ? 'active' : '' }}" aria-current="page" href="{{ url('invoice/referral/status/needed') }}">Invoice
                        Needed</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status =='list' ? 'active' : '' }}" href="{{ url('invoice/referral/status/list') }}">Invoice List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status =='reminder' ? 'active' : '' }}" href="{{ url('invoice/referral/status/reminder') }}">Due Date Reminder</a>
                </li>
            </ul>
            @if ($status == 'needed')
                @include('pages.invoice.referral.detail.invoice-needed')
            @elseif ($status == 'list')
                @include('pages.invoice.referral.detail.invoice-list')
            @elseif ($status == 'reminder')
                @include('pages.invoice.referral.detail.invoice-reminder')
            @endif

        </div>
    </div>
@endsection
