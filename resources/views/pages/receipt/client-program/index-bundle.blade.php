@extends('layout.main')

@section('title', 'Receipt of Client Program')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="text-white m-0">
                    <i class="bi bi-tag me-1"></i>
                        Receipt of Client Program
                </h5>
            </div>
            <div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="bundleChecked" @checked(Request::get('b') == true)>
                    <label class="form-check-label text-white me-1" for="bundleChecked">Bundle</label>
                  </div>
            </div>
        </div>
    </div>

    <div class="card rounded">
        {{-- <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link {{ Request::get('s') == 'list' ? 'active' : null }}" aria-current="page"
                    href="{{ route('receipt.client-program') }}?s=list">Receipt List</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::get('s') == 'refund-request' ? 'active' : null }}"
                    href="{{ route('receipt.client-program') }}?s=refund-request">Refund Request</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::get('s') == 'refund-list' ? 'active' : null }}"
                    href="{{ route('receipt.client-program') }}?s=refund-list">Refund List</a>
            </li>
        </ul> --}}
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Client Name</th>
                        <th>Program Name</th>
                        <th>Receipt ID</th>
                        <th>Invoice ID</th>
                        <th>Payment Method</th>
                        <th>Receipt Date</th>
                        <th>Total Price</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="7"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
{{-- Need Changing --}}
<script>
    var widthView = $(window).width();
    $(document).ready(function() {

        var options = {
            buttons: [
                'pageLength', {
                    extend: 'excel',
                    text: 'Export to Excel',
                }
            ],
            order: [[6, 'desc']],
            fixedColumns: {
                left: (widthView < 768) ? 1 : 2,
                right: 1
            },
            ajax: '',
            pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
            columns: [{
                    data: 'id',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'client_fullname',
                },
                {
                    data: 'program_name',
                },
                {
                    data: 'receipt_id',
                    className: 'text-center',
                },
                {
                    data: 'inv_id',
                    className: 'text-center',
                },
                {
                    data: 'receipt_method',
                    className: 'text-center',
                },
                {
                    data: 'created_at',
                    className: 'text-center',
                    render: function(data, type, row) {
                        let receipt_date = row.created_at ? moment(row.created_at).format("MMMM Do YYYY") : '-'
                        return receipt_date
                    }
                },
                {
                    data: 'receipt_amount_idr',
                    className: 'text-center',
                },
                {
                    data: 'id',
                    className: 'text-center',
                    render: function(data, type, row) {

                        return '<a href="{{ url('receipt/client-program/') }}/' + data +
                            '?b=true" class="btn btn-sm btn-outline-warning"><i class="bi bi-eye"></i></a>'
                    }
                }
            ]
        };

        var table = initializeDataTable('#programTable', options, 'rt_receipt');

        @php
            $privilage = $menus['Receipt']->where('submenu_name', 'Client Program')->first();
        @endphp

        @if ($privilage['copy'] == 0)
            document.oncontextmenu = new Function("return false");

            $('body').bind('cut copy paste', function(event) {
                event.preventDefault();
            });
        @endif

        @if ($privilage['export'] == 0)
            table.button(1).disable();
        @endif

    });

    $('#bundleChecked').click(function() {
        var url = '{{ Request::url() }}';

        var searchParams = new URLSearchParams(window.location.search);
        
        if($('#bundleChecked').is(':checked')){
            searchParams.set('b','true')
            var newParams = searchParams.toString()
        }else{
            searchParams.delete('b')
            var newParams = searchParams.toString()
            
        }

        window.location.href = url + '?' + newParams;
    });
</script>
@endpush
