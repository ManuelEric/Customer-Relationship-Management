@extends('layout.main')

@section('title', 'Receipt of Referral')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="row align-items-center justify-content-between">
            <div class="col-md-6">
                <h5 class="text-white m-0">
                    <i class="bi bi-tag me-1"></i>
                    Receipt of Referral
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
                {{-- <tbody>
                    @for ($i = 0; $i < 5; $i++)
                        <tr>
                            <td>#</td>
                            <td>Partner Name</td>
                            <td>Program Name</td>
                            <td>Receipt ID</td>
                            <td>Invoice ID</td>
                            <td>Payment Method</td>
                            <td>Receipt Date</td>
                            <td>Total Price</td>
                            <td class="text-center">
                                <a href="{{ url('receipt/referral/1') }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endfor
                </tbody> --}}
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
                        name: 'tbl_referral.additional_prog_name'
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
                                .created_at).format("MMMM Do YYYY") : '-'
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
            };

            var table = initializeDataTable('#receiptTable', options, 'rt_receipt');

            @php
                $privilage = $menus['Receipt']->where('submenu_name', 'Referral Program')->first();
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

            // realtimeData(table)

            $('#receiptTable tbody').on('click', '.showReceipt ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('receipt/referral/') }}/" + data.increment_receipt;
            });

        });
    </script>
@endsection
