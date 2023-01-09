@extends('layout.main')

@section('title', 'Invoice - Client Program - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Invoice
        </a>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ isset($status) && $status == "needed" ? "active" : null }}" aria-current="page" href="{{ url('invoice/client-program?s=needed') }}">Invoice
                        Needed</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($status) && $status == "list" ? "active" : null }}" href="{{ url('invoice/client-program?s=list') }}">Invoice List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($status) && $status == "reminder" ? "active" : null }}" href="{{ url('invoice/client-program?s=reminder') }}">Due Date Reminder</a>
                </li>
            </ul>
            @if ($status == 'needed')
                @include('pages.invoice.client-program.detail.invoice-needed')
            @else
                @include('pages.invoice.client-program.detail.invoice-list')
            @endif

        </div>
    </div>
@endsection
