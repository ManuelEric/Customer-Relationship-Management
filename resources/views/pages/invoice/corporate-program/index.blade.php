@extends('layout.main')

@section('title', 'Invoice of Partner Program')

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
                Invoice of Partner Program
            </h5>
        </div>
    </div>
</div>

    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs flex-nowrap w-100 overflow-auto mb-3" style="overflow-y: hidden !important;">
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ $status =='needed' ? 'active' : '' }}" aria-current="page" href="{{ url('invoice/corporate-program/status/needed') }}">Invoice
                        Needed</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ $status =='list' ? 'active' : '' }}" href="{{ url('invoice/corporate-program/status/list') }}">Invoice List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ $status =='reminder' ? 'active' : '' }}" href="{{ url('invoice/corporate-program/status/reminder') }}">Due Date Reminder</a>
                </li>
            </ul>
            @if ($status == 'needed')
                @include('pages.invoice.corporate-program.detail.invoice-needed')
            @elseif ($status == 'list')
                @include('pages.invoice.corporate-program.detail.invoice-list')
            @elseif ($status == 'reminder')
                @include('pages.invoice.corporate-program.detail.invoice-reminder')
            @endif

        </div>
    </div>
@endsection
