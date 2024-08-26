@extends('layout.main')

@section('title', 'Invoice of Referral')

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
                Invoice of Referral
            </h5>
        </div>
    </div>
</div>

    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs flex-nowrap w-100 overflow-auto mb-3" style="overflow-y: hidden !important;">
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ $status =='needed' ? 'active' : '' }}" aria-current="page" href="{{ url('invoice/referral/status/needed') }}">Invoice
                        Needed</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ $status =='list' ? 'active' : '' }}" href="{{ url('invoice/referral/status/list') }}">Invoice List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ $status =='reminder' ? 'active' : '' }}" href="{{ url('invoice/referral/status/reminder') }}">Due Date Reminder</a>
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
