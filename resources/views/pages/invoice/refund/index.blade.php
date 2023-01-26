@extends('layout.main')

@section('title', 'Refund - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Refund
        </a>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="{{ url('invoice/refund/status/needed') }}">Refund
                        Needed</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('invoice/refund/status/list') }}">Refund List</a>
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
