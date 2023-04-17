@extends('layout.main')

@section('title', 'Finance Report - Bigdata Platform')

@section('content')
    <div class="row">
        <div class="col-md-3">
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
                        <strong>Total Invoice ({{$countInvoice}})</strong>
                        <div class="text-end">
                           Rp. {{ number_format($totalInvoice, '2', ',', '.') }}
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <strong>Total Receipt ({{count($receipts)}})</strong>
                        <div class="text-end">
                                Rp. {{ number_format($totalReceipt, '2', ',', '.') }}
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <button class="btn btn-sm btn-outline-info" onclick="ExportToExcel()"
                style="margin-bottom: 15px">
                <i class="bi bi-file-earmark-excel me-1"></i> Print
            </button>
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
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoices as $invoice)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ isset($invoice->inv_id) ? $invoice->inv_id : $invoice->invb2b_id}}</td>
                                        
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
                                        <td>{{ isset($invoice->inv_id) ? $invoice->inv_duedate : $invoice->invb2b_duedate }}</td>

                                        {{-- Amount --}}
                                        <td>{{ $invoice->invoiceTotalpriceIdr }}</td>
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
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($receipts as $receipt)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $receipt->receipt_id }}</td>
                                        
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
                                        <td>{{ $receipt->created_at }}</td>

                                        {{-- Amount --}}
                                        <td>{{ $receipt->receipt_amount_idr }}</td>
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
            
            XLSX.writeFile(workbook, "report-invoice-receipt.xlsx");
            
        }
    </script>
@endsection
