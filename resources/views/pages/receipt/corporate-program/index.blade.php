@extends('layout.main')

@section('title', 'Receipt of Partner Program')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="row align-items-center justify-content-between">
            <div class="col-md-6">
                <h5 class="text-white m-0">
                    <i class="bi bi-tag me-1"></i>
                    Receipt of Partner Program
                </h5>
            </div>
        </div>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="receiptTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Partner Name</th>
                        <th>Program Name</th>
                        <th>Receipt ID</th>
                        <th>Invoice ID</th>
                        <th>Payment Method</th>
                        <th>Receipt Date</th>
                        <th>Total Price Other</th>
                        <th>Total Price IDR</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="9"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Need Changing --}}
    <script>
        var widthView = $(window).width();
        $(document).ready(function() {
            var table = $('#receiptTable').DataTable({
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
                order: [[6, 'desc']],
                scrollX: true,
                fixedColumns: {
                    left: (widthView < 768) ? 1 : 2,
                    right: 1
                },
                search: {
                    return: true
                },
                processing: true,
                serverSide: true,
                ajax: '',
                columns: [{
                        data: 'increment_receipt',
                        name: 'tbl_receipt.id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'corp_name',
                        name: 'tbl_corp.corp_name'

                    },
                    {
                        data: 'program_name',
                        name: 'program.program_name'
                    },
                    {
                        data: 'receipt_id',
                        name: 'tbl_receipt.receipt_id',
                        className: 'text-center',
                    },
                    {
                        data: 'invb2b_id',
                        className: 'text-center',
                    },
                    {
                        data: 'receipt_method',
                        name: 'tbl_receipt.receipt_method',
                        className: 'text-center',
                    },
                    {
                        data: 'created_at',
                        className: 'text-center',
                        render: function(data, type, row) {
                            let receipt_date = row.created_at ? moment(row
                                .created_at).format("MMMM Do YYYY HH:mm:ss") : '-'
                            return receipt_date
                        }
                    },
                    {
                        data: 'total_price_other',
                        name: 'tbl_receipt.receipt_amount',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            var currency;
                            var totprice = new Intl.NumberFormat().format(row.total_price_other);
                            switch (row.currency) {
                                case 'usd':
                                    currency = '$. ';
                                    break;
                                case 'sgd':
                                    currency = 'S$. ';
                                    break;
                                case 'gbp':
                                    currency = '£. ';
                                    break;
                                default:
                                    currency = '';
                                    totprice = '-'
                                    break;
                            }
                            return currency + totprice;
                        }

                    },
                    {
                        data: 'total_price_idr',
                        name: 'tbl_receipt.receipt_amount_idr',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            var totprice = new Intl.NumberFormat().format(row.total_price_idr);
                            return 'Rp. ' + totprice;
                        }

                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning showReceipt"><i class="bi bi-eye"></i></button>'
                    }
                ]
            });

            @php
                $privilage = $menus['Receipt']->where('submenu_name', 'Partner Program')->first();
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

            realtimeData(table)

            $('#receiptTable tbody').on('click', '.showReceipt ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('receipt/corporate-program/') }}/" + data.increment_receipt;
            });

        });
    </script>
@endsection
