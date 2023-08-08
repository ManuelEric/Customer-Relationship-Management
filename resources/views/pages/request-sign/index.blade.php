@extends('layout.main')

@section('title', 'Request Sign')

@section('content')    
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Request Sign
            </h5>
        </div>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs flex-nowrap mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ Request::get('type') == 'invoice' ? 'active' : '' }}"
                        href="{{ url('request-sign?type=invoice') }}">Invoice <div class="badge bg-info p-1 px-2">
                            {{ $total_invoiceNeedToBeSigned }}</div></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::get('type') == 'receipt' ? 'active' : '' }}"
                        href="{{ url('request-sign?type=receipt') }}">Receipt <div class="badge bg-info p-1 px-2">
                            {{ $total_receiptNeedToBeSigned }}</div></a>
                </li>
            </ul>
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="requestSignTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">{{ Request::get('type') == 'receipt' ? 'Receipt ID' : 'Invoice ID' }}</th>
                        <th>Full Name</th>
                        <th>Program Name</th>
                        <th>Payment Method</th>
                        <th>Due Date</th>
                        <th>Total (USD, GBP, SGD)</th>
                        <th>Total IDR</th>
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

    <script>
        function previewAttachment(clientprog_id, currency, type)
        {
            var url;
            switch (type) {

                case "Invoice":
                    url = '{{ url("/") }}/invoice/client-program/'+clientprog_id+'/preview/'+currency;
                    break;

                case "Receipt":
                    url = '{{ url("/") }}/receipt/client-program/'+clientprog_id+'/preview/'+currency;
                    break;


            }
            window.open(url, '_blank');
        }

        $(document).ready(function() {
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            const paramType = urlParams.get('type');

            var table = $('#requestSignTable').DataTable({
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
                    left: window.matchMedia('(max-width: 767px)').matches ? 0 : 2,
                    right: 1
                },
                processing: true,
                serverSide: true,
                ajax: '',
                pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
                columns: [
                    {
                        data: 'clientprog_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'inv_id',
                        render: function(data, type, row, meta) {
                            
                            switch (paramType) {

                                case "invoice":
                                    return row.inv_id;
                                    break;

                                case "receipt":
                                    return row.receipt_id;
                                    break;

                            }

                        }

                    },
                    {
                        data: 'fullname',
                    },
                    {
                        data: 'program_name',
                    },
                    {
                        data: 'payment_method',
                    },
                    {
                        data: 'due_date',
                        render: function(data, type, row) {
                            return moment(data).format('MMMM Do YYYY')
                        }
                    },
                    {
                        data: 'total_price',
                        render: function(data, type, row) {
                            var format, currency;

                            var curr_category = row['currency_category'];
                            var currency = row['currency'];
                            if (curr_category != 'Other') 
                                return 0;

                            switch (currency) {

                                case 'usd':
                                    format = 'en-US';
                                    currency = 'USD';
                                    break;
                                case 'sgd':
                                    format = 'en-SG';
                                    currency = 'SGD';
                                    break;
                                case 'gbp':
                                    format = 'en-US';
                                    currency = 'GBP';
                                    break;

                            }   

                            return new Intl.NumberFormat(format, {
                                style: "currency",
                                currency: currency,
                                minimumFractionDigits: 0
                            }).format(data);
                            


                        }
                    },
                    {
                        data: 'total_price_idr',
                        render: function(data, type, row) {
                            return new Intl.NumberFormat("id-ID", {
                                style: "currency",
                                currency: "IDR",
                                minimumFractionDigits: 0
                            }).format(data);

                        }
                    },
                    {
                        data: 'clientprog_id',
                        className: 'text-center',
                        render: function(data, type, row) {

                            var clientprog_id = row.clientprog_id;
                            var receipt_id = row.id;
                            var currency = row.currency;

                            switch (paramType) {

                                case "invoice":
                                    link = '<button type="button" class="btn btn-sm btn-outline-warning" onclick="previewAttachment('+clientprog_id+', \''+currency+'\', \'Invoice\')"><i class="bi bi-pencil"></i></button>';
                                    break;

                                case "receipt":
                                    link = '<button type="button" class="btn btn-sm btn-outline-warning" onclick="previewAttachment('+receipt_id+', \''+currency+'\', \'Receipt\')"><i class="bi bi-pencil"></i></button>';
                                    break;

                            }

                            return link;
                        }
                    }
                ]
            });
        });
    </script>
@endsection