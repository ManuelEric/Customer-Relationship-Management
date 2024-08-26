@extends('layout.main')

@section('title', 'Finance Report - Bigdata Platform')

@section('content')
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Period</h6>
                </div>
                <div class="card-body">
                    <form action="">
                        <div class="mb-3">
                            <label>Start Date</label>
                            <input type="date" name="start_date" id="" value="{{ Request::get('start_date') }}" class="form-control form-control-sm rounded">
                        </div>
                        <div class="mb-3">
                            <label>End Date</label>
                            <input type="date" name="end_date" id="" value="{{ Request::get('end_date') }}" class="form-control form-control-sm rounded">
                        </div>
                        <div class="text-center">
                            <button class="btn btn-sm btn-outline-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="p-0 m-0">Detail</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <strong>Total Invoice ({{count($invoices)}})</strong>
                        <div class="text-end">
                           Rp. {{ number_format($totalInvoice) }}
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <strong>Total Receipt ({{count($receipts)}})</strong>
                        <div class="text-end">
                                Rp. {{ number_format($totalReceipt) }}
                           
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <strong>Total Refund ({{ $countRefund }})</strong>
                        <div class="text-end">
                                Rp. {{ number_format($totalRefund) }}
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="text-end">
                <button class="btn btn-sm btn-outline-info" onclick="ExportToExcel()"
                    style="margin-bottom: 15px">
                    <i class="bi bi-file-earmark-excel me-1"></i> Print
                </button>
            </div>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Invoice List</h6>
                </div>
                <div class="card-body overflow-auto" style="max-height: 500px">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="tbl_inv">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice ID</th>
                                    <th>Client/Partner/School Name</th>
                                    <th>Type</th>
                                    <th>Program Name</th>
                                    <th>Method</th>
                                    <th>Installment</th>
                                    <th>Due Date</th>
                                    <th>Amount IDR</th>
                                    <th>Amount USD</th>
                                    <th>Amount SGD</th>
                                    <th>Amount GBP</th>
                                    <th>Amount Refund</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoices as $invoice)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if(isset($invoice->inv_id))
                                                {{ $invoice->inv_id }}
                                                <div class="badge badge-{{ $invoice->inv_status == 2 ? 'danger' : 'success' }} py-1 px-2 ms-2">
                                                    {{ $invoice->inv_status == 2 ? 'Refund' : 'Success' }}
                                                </div>
                                            @else
                                                {{ $invoice->invb2b_id }}
                                                <div class="badge badge-{{ $invoice->invb2b_status == 2 ? 'danger' : 'success' }} py-1 px-2 ms-2">
                                                    {{ $invoice->invb2b_status == 2 ? 'Refund' : 'Success' }}
                                                </div>
                                            @endif
                                        </td>
                                        
                                        {{-- Client Name --}}
                                        @if(isset($invoice->clientprog_id))
                                            <td>{{ $invoice->clientprog->client->first_name }} {{ $invoice->clientprog->client->last_name }}</td> 
                                        @elseif(isset($invoice->schprog_id))
                                            <td>{{ $invoice->sch_prog->school->sch_name }}</td>
                                        @elseif(isset($invoice->partnerprog_id))
                                            <td>{{ $invoice->partner_prog->corp->corp_name }}</td>
                                        @elseif(isset($invoice->ref_id))
                                            <td>{{ $invoice->referral->partner->corp_name }}</td>
                                        @endif

                                        {{-- Type --}}
                                        <td>{{ isset($invoice->inv_id) ? 'B2C' : 'B2B' }}</td>

                                        {{-- Program Name --}}
                                        @if(isset($invoice->clientprog_id))
                                            <td>{{ $invoice->clientprog->program->program_name }}</td>
                                        @elseif(isset($invoice->schprog_id))
                                            <td>{{ $invoice->sch_prog->program->program_name }}</td>
                                        @elseif(isset($invoice->partnerprog_id))
                                            <td>{{ $invoice->partner_prog->program->program_name }}</td>
                                        @elseif(isset($invoice->ref_id))
                                            <td>{{ $invoice->referral->additional_prog_name }}</td>
                                        @endif 

                                        {{-- Method --}}
                                        <td>{{ isset($invoice->inv_id) ? $invoice->inv_paymentmethod : $invoice->invb2b_pm }}</td>
                                        
                                        {{-- Installment --}}
                                        <td class="text-center">
                                            @if(isset($invoice->inv_id))
                                                @if(count($invoice->invoiceDetail) > 0)
                                                    {{ count($invoice->invoiceDetail) }}
                                                @else
                                                    -
                                                @endif
                                            @elseif(isset($invoice->invb2b_id))
                                                @if(count($invoice->inv_detail) > 0)
                                                    {{ count($invoice->inv_detail) }}
                                                @else
                                                    -
                                                @endif
                                            @endif
                                        </td>
                                        
                                        {{-- Due date --}}
                                        <td>{{ isset($invoice->inv_id) ? date('M d, Y', strtotime($invoice->inv_duedate)) : date('M d, Y', strtotime($invoice->invb2b_duedate)) }}</td>

                                        {{-- Amount IDR --}}
                                        <td>{{ $invoice->invoiceTotalpriceIdr }}</td>
                                        
                                        {{-- Amount USD --}}
                                        <td>{{ $invoice->currency == 'usd' ? $invoice->invoiceTotalprice : '-' }}</td>
                                        
                                        {{-- Amount SGD --}}
                                        <td>{{ $invoice->currency == 'sgd' ? $invoice->invoiceTotalprice : '-' }}</td>
                                        
                                        {{-- Amount GBP --}}
                                        <td>{{ $invoice->currency == 'gbp' ? $invoice->invoiceTotalprice : '-' }}</td>

                                        {{-- Amount Refund --}}
                                        <td>{{ isset($invoice->refund) ? $invoice->refund->totalRefundedStr : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Not invoice yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-light text-white">
                                <tr>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Receipt List</h6>
                </div>
                <div class="card-body overflow-auto" style="max-height: 500px">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="tbl_receipt">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Receipt ID</th>
                                    <th>Client/Partner/School Name</th>
                                    <th>Type</th>
                                    <th>Program Name</th>
                                    <th>Method</th>
                                    <th>Installment</th>
                                    <th>Paid Date</th>
                                    <th>Amount IDR</th>
                                    <th>Amount USD</th>
                                    <th>Amount SGD</th>
                                    <th>Amount GBP</th>
                                    <th>Amount Refund</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($receipts as $receipt)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $receipt->receipt_id }}
                                            @if(isset($receipt->inv_id))
                                                <div class="badge badge-{{ $receipt->invoiceProgram->inv_status == 2 ? 'danger' : 'success' }} py-1 px-2 ms-2">
                                                    {{ $receipt->invoiceProgram->inv_status == 2 ? 'Refund' : 'Success' }}
                                                </div>
                                            @elseif(isset($receipt->invb2b_id))
                                                <div class="badge badge-{{ $receipt->invoiceB2b->invb2b_status == 2 ? 'danger' : 'success' }} py-1 px-2 ms-2">
                                                    {{ $receipt->invoiceB2b->invb2b_status == 2 ? 'Refund' : 'Success' }}
                                                </div>                                            
                                            @endif
                                        </td>
                                        
                                        {{-- Client Name --}}
                                        @if(isset($receipt->inv_id))
                                            <td>{{ $receipt->invoiceProgram->clientprog->client->first_name }} {{ $receipt->invoiceProgram->clientprog->client->last_name }}</td>
                                        @elseif(isset($receipt->invb2b_id))
                                            @if(isset($receipt->invoiceB2b->schprog_id))
                                                <td>{{ $receipt->invoiceB2b->sch_prog->school->sch_name }}</td>
                                            @elseif(isset($receipt->invoiceB2b->partnerprog_id))
                                                <td>{{ $receipt->invoiceB2b->partner_prog->corp->corp_name }}</td>
                                            @elseif(isset($receipt->invoiceB2b->ref_id))
                                                <td>{{ $receipt->invoiceB2b->referral->partner->corp_name }}</td>
                                            @endif
                                        @endif

                                        {{-- Type --}}
                                        <td>{{ $receipt->inv_id ? 'B2C' : 'B2B'}}</td>

                                        {{-- Program Name --}}
                                        @if(isset($receipt->inv_id))
                                            <td>{{ $receipt->invoiceProgram->clientprog->program->program_name }}</td>
                                        @elseif(isset($receipt->invb2b_id))
                                            @if(isset($receipt->invoiceB2b->schprog_id))
                                                <td>{{ $receipt->invoiceB2b->sch_prog->program->program_name }}</td>
                                            @elseif((isset($receipt->invoiceB2b->partnerprog_id)))
                                                <td>{{ $receipt->invoiceB2b->partner_prog->program->program_name }}</td>
                                            @elseif((isset($receipt->invoiceB2b->ref_id)))
                                                <td>{{ $receipt->invoiceB2b->referral->additional_prog_name }}</td>
                                            @endif
                                        @endif 

                                        {{-- Method --}}
                                        <td>{{ $receipt->receipt_method }}</td>
                                        
                                        {{-- Installment --}}
                                        <td class="text-center">
                                            {{ isset($receipt->invoiceInstallment) ?  $receipt->invoiceInstallment->invdtl_installment : '-' }}
                                        </td>
                                        
                                        {{-- Paid date --}}
                                        <td>{{ date('M d, Y', strtotime($receipt->created_at)) }}</td>

                                        {{-- Amount IDR--}}
                                        <td>{{ $receipt->receipt_amount_idr }}</td>

                                        {{-- Amount USD--}}
                                        @if(isset($receipt->inv_id))
                                            <td>{{ $receipt->invoiceProgram->currency == 'usd' ? $receipt->receipt_amount : '-' }}</td>
                                        @elseif(isset($receipt->invb2b_id))
                                            <td>{{ $receipt->invoiceB2b->currency == 'usd' ? $receipt->receipt_amount : '-' }}</td>
                                        @endif

                                        {{-- Amount SGD--}}
                                        @if(isset($receipt->inv_id))
                                            <td>{{ $receipt->invoiceProgram->currency == 'sgd' ? $receipt->receipt_amount : '-' }}</td>
                                        @elseif(isset($receipt->invb2b_id))
                                            <td>{{ $receipt->invoiceB2b->currency == 'sgd' ? $receipt->receipt_amount : '-' }}</td>
                                        @endif

                                        {{-- Amount GBP--}}
                                        @if(isset($receipt->inv_id))
                                            <td>{{ $receipt->invoiceProgram->currency == 'gbp' ? $receipt->receipt_amount : '-' }}</td>
                                        @elseif(isset($receipt->invb2b_id))
                                            <td>{{ $receipt->invoiceB2b->currency == 'gbp' ? $receipt->receipt_amount : '-' }}</td>
                                        @endif

                                        {{-- Amount Refund--}}
                                        @if(isset($receipt->inv_id))
                                            <td>{{ isset($receipt->invoiceProgram->refund) ? $receipt->invoiceProgram->refund->totalRefundedStr : '-' }}</td>
                                        @elseif(isset($receipt->invb2b_id))
                                            <td>{{ isset($receipt->invoiceB2b->refund) ? $receipt->invoiceB2b->refund->totalRefundedStr : '-' }}</td>
                                        @endif 
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Not receipt yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-light text-white">
                                <tr>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        @php            
            $privilage = $menus['Report']->where('submenu_name', 'Invoice & Receipt')->first();
        @endphp
        $(document).ready(function() {
            @if($privilage['copy'] == 0)
                document.oncontextmenu = new Function("return false"); 
                    
                $('body').bind('cut copy paste', function(event) {
                    event.preventDefault();
                });
            @endif
        });

        function ExportToExcel() {

            var sheetName = ['Invoices', 'Receipts'];

            var tableName = ['tbl_inv', 'tbl_receipt'];

            var ws = new Array();

            var workbook = XLSX.utils.book_new();

            tableName.forEach(function (d, i) {
                ws[i] = XLSX.utils.table_to_sheet(document.getElementById(tableName[i]));
                XLSX.utils.book_append_sheet(workbook, ws[i], sheetName[i]);
            })

            sheetName.forEach(function (d, i){

                var sheet = d;
                var full_ref = workbook.Sheets[sheet]['!fullref'];
                var last_ref = full_ref.slice(full_ref.indexOf(':') + 1);
                var last_col = parseInt(last_ref.slice(last_ref.indexOf('M') + 1)) - 1;

                var col = ['I', 'J', 'K', 'L', 'M']; //  I = Amount IDR, J = USD, K = SGD, L = GBP, M = Refund(IDR)
                
                col.forEach(function (d, i){
                    // console.log(last_col);
                    for(var i = 2; i <= last_col; i++) {
                        var index = d + i;

                        var format_cell;
                        var remove_cursymbol;
                        switch (d) {
                            case 'I':
                            case 'M':
                                remove_cursymbol = 'Rp.';
                                format_cell = 'Rp#,##0;(Rp#,##0)';
                                break;
                            case 'J':
                                format_cell = '$#,##0;($#,##0)';
                                break;
                            case 'K':
                                remove_cursymbol = 'S$ ';
                                format_cell = '$#,##0;($#,##0)';
                                break;
                            case 'L':
                                remove_cursymbol = '£ ';
                                format_cell = '£#,##0;(£#,##0)';
                                break;
                        }
                        if(workbook.Sheets[sheet][index].v != '-' && d != 'J')
                            workbook.Sheets[sheet][index].v =  parseInt(workbook.Sheets[sheet][index].v.replace(remove_cursymbol, "").replaceAll(",", ""));

                        workbook.Sheets[sheet][index].t = 'n';
                        workbook.Sheets[sheet][index].z = format_cell;
                    }
                    workbook.Sheets[sheet][d + i] = { t:'n', z:format_cell, f: `SUM(${d+2}:` + index +")", F:d + i + ":" + d + i }
                    

                })

            })
           
            XLSX.writeFile(workbook, "report-invoice-receipt.xlsx");
            
        }
    </script>
@endsection
