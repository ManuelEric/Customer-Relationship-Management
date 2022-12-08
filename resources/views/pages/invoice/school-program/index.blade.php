@extends('layout.main')

@section('title', 'Invoice - School Program - Bigdata Platform')

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
                    <a class="nav-link" aria-current="page" href="{{ url('invoice/school-program/status/needed') }}">Invoice
                        Needed</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('invoice/school-program/status/list') }}">Invoice List</a>
                </li>
            </ul>
            @if ($status == 'needed')
                @include('pages.invoice.school-program.detail.invoice-needed')
            @else
                @include('pages.invoice.school-program.detail.invoice-list')
            @endif

        </div>
    </div>
@endsection
