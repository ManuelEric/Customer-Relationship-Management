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
                            <button type="submit" class="btn btn-sm btn-outline-primary">Submit</button>
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
                        <strong>Total</strong>
                        <div class="text-end">
                            Rp. {{ number_format($totalAmount) }}
                        </div>
                    </div>
                    {{-- <div class="d-flex justify-content-between">
                        <strong>Paid</strong>
                        <div class="text-end">
                                Rp. {{ number_format($totalPaid) }} {{ $totalDiff > 0 ? '( Rp. '. number_format($totalDiff) .')' : '' }}                       
                        </div>
                    </div> --}}
                    {{-- <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <strong>Remaining</strong>
                        <div class="text-end">
                             Rp. {{ number_format($remaining) }}
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>

        <div class="col-md-9 mt-3 mt-md-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Invoice & Receipt Report</h6>
                    <div class="">
                        <button class="btn btn-sm btn-outline-info" onclick="ExportToExcel()">
                            <i class="bi bi-file-earmark-excel me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body overflow-auto" style="max-height: 500px">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100 table2excel" id="tbl_unpaid_payment">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice ID</th>
                                    <th>Type</th>
                                    <th>Client/Partner/School Name</th>
                                    <th>Program Name</th>
                                    <th>Invoice Duedate</th>
                                    <th>Installment</th>
                                    <th>Status</th>
                                    <th>Amount IDR</th>
                                    <th>Amount USD</th>
                                    <th>Amount SGD</th>
                                    <th>Amount GBP</th>
                                    {{-- <th>Receipt ID</th>
                                    <th>Paid Date</th>
                                    <th>Amount</th>
                                </tr> --}}
                            </thead>
                            <tbody>
                         
                                @forelse ($invoices as $invoice)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ isset($invoice->inv_id) ? $invoice->inv_id : $invoice->invb2b_id}}</td>
                                        <td>{{ isset($invoice->inv_id) ? 'B2C' : 'B2B'}}</td>
                                        
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

                                        {{-- Invoice duedate --}}
                                        <td class="text-center">
                                            {{ isset($invoice->invdtl_installment) ? date('M d, Y', strtotime($invoice->installment_duedate)) : date('M d, Y', strtotime($invoice->invoice_duedate)) }}
                                        </td>

                                        {{-- Installment --}}
                                        <td class="text-center">
                                            {{ isset($invoice->invdtl_installment) ? $invoice->invdtl_installment : '-' }}
                                        </td>

                                        {{-- Status --}}
                                        <td>
                                            {{ isset($invoice->receipt_id) ? 'Paid' : 'Not yet' }}
                                        </td>

                                        {{-- Amount IDR --}}
                                        <td>
                                            Rp. {{ number_format($invoice->total_price_inv_idr) }}
                                        </td>

                                        {{-- Amount USD --}}
                                        <td>
                                            {{ $invoice->currency == 'usd' ? '$. ' . number_format($invoice->total_price_inv_other) : '-' }}
                                        </td>
                                        
                                        {{-- Amount SGD --}}
                                        <td>
                                            {{ $invoice->currency == 'sgd' ? 'S$. ' . number_format($invoice->total_price_inv_other) : '-' }}
                                        </td>
                                        {{-- Amount GBP --}}
                                        <td>
                                            {{ $invoice->currency == 'gbp' ? '£. ' . number_format($invoice->total_price_inv_other) : '-' }}
                                        </td>

                                        {{-- Receipt ID --}}
                                        {{-- @if(isset($invoice->receipt_id))
                                            <td>{{ $invoice->receipt_id }}</td>
                                        @else
                                            <td class="text-center">-</td>
                                        @endif
                                        --}}
                                        {{-- Paid Date --}}
                                        {{-- @if(isset($invoice->receipt_id))
                                            <td>{{ date('M d, Y', strtotime($invoice->paid_date)) }}</td>
                                        @else
                                            <td class="text-center">-</td>
                                        @endif --}}
                                        
                                        {{-- Amount --}}
                                         {{-- @if(isset($invoice->receipt_id))
                                            <td>Rp. {{ number_format($invoice->receipt_amount_idr) }} {{ $invoice->receipt_amount_idr > $invoice->total_price_inv ? '( Rp.'. number_format($invoice->receipt_amount_idr - $invoice->total_price_inv) .')' : '' }}</td>
                                        @else
                                            <td class="text-center">-</td>
                                        @endif --}}
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Not yet invoice</td>
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
            $privilage = $menus['Report']->where('submenu_name', 'Unpaid Payment')->first();
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

            var workbook = XLSX.utils.book_new();
            var ws = XLSX.utils.table_to_sheet(document.getElementById("tbl_unpaid_payment"));
            XLSX.utils.book_append_sheet(workbook, ws, "Unpaid Payment");


            var full_ref = workbook.Sheets['Unpaid Payment']['!fullref'];
            var last_ref = full_ref.slice(full_ref.indexOf(':') + 1);
            var last_col = parseInt(last_ref.slice(last_ref.indexOf('L') + 1)) - 1;

            var col = ['I', 'J', 'K', 'L']; //  I = Amount IDR, J = USD, K = SGD, L = GBP
                
                col.forEach(function (d, i){
                    for(var i = 2; i <= last_col; i++) {
                        var index = d + i;

                        var format_cell;
                        var remove_cursymbol;
                        switch (d) {
                            case 'I':
                                remove_cursymbol = 'Rp.';
                                format_cell = 'Rp#,##0;(Rp#,##0)';
                                break;
                            case 'J':
                                remove_cursymbol = '$.';
                                format_cell = '$#,##0;($#,##0)';
                                break;
                            case 'K':
                                remove_cursymbol = 'S$.';
                                format_cell = '$#,##0;($#,##0)';
                                break;
                            case 'L':
                                remove_cursymbol = '£.';
                                format_cell = '£#,##0;(£#,##0)';
                                break;
                        }
                        if(workbook.Sheets['Unpaid Payment'][index].v != '-')
                            workbook.Sheets['Unpaid Payment'][index].v =  parseInt(workbook.Sheets['Unpaid Payment'][index].v.replace(remove_cursymbol, "").replaceAll(",", ""));

                        workbook.Sheets['Unpaid Payment'][index].t = 'n';
                        workbook.Sheets['Unpaid Payment'][index].z = format_cell;
                    }
                    workbook.Sheets['Unpaid Payment'][d + i] = { t:'n', z:format_cell, f: `SUM(${d+2}:` + index +")", F:d + i + ":" + d + i }
                })

            XLSX.writeFile(workbook, "report-unpaid-payment.xlsx");
            
        }
    </script>
@endsection
