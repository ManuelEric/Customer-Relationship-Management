@extends('layout.main')

@section('title', 'Receipt - Client Program - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Receipt
        </a>
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
                <thead class="bg-dark text-white">
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

    {{-- Need Changing --}}
    <script>
        var widthView = $(window).width();
        $(document).ready(function() {
            var table = $('#programTable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
                ],
                buttons: [
                    'pageLength', {
                        extend: 'excel',
                        text: 'Export to Excel',
                    }
                ],
                scrollX: true,
                fixedColumns: {
                    left: (widthView < 768) ? 1 : 2,
                    right: 1
                },
                processing: true,
                serverSide: true,
                ajax: '',
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
                        name: 'program.program_name'
                    },
                    {
                        data: 'receipt_id',
                    },
                    {
                        data: 'inv_id',
                    },
                    {
                        data: 'receipt_method',
                    },
                    {
                        data: 'created_at',
                        render: function(data, type, row) {
                            let receipt_date = row.created_at ? moment(row
                                .created_at).format("MMMM Do YYYY HH:mm:ss") : '-'
                            return receipt_date
                        }
                    },
                    {
                        data: 'receipt_amount_idr'
                    },
                    {
                        data: 'id',
                        className: 'text-center',
                        render: function(data, type, row) {
                            
                            return '<a href="{{ url('receipt/client-program/') }}/' + data +
                                '" class="btn btn-sm btn-outline-warning"><i class="bi bi-eye"></i></a>'
                        }
                    }
                ]
            })

            @php            
                $privilage = $menus['Receipt']->where('submenu_name', 'Client Program')->first();
            @endphp

            @if($privilage['copy'] == 0)
                document.oncontextmenu = new Function("return false"); 
                
                $('body').bind('cut copy paste', function(event) {
                    event.preventDefault();
                });
            @endif

            @if ($privilage['export'] == 0)
                table.button(1).disable();
            @endif

        });
    </script>
@endsection
