@extends('layout.main')

@section('title', 'Program Tracker Report - Bigdata Platform')

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
                            <label>Start Month</label>
                            <input type="month" name="start_month" id="" value="{{ Request::get('start_month') ?? date('Y-m') }}" class="form-control form-control-sm rounded">
                        </div>
                        <div class="mb-3">
                            <label>End Month</label>
                            <input type="month" name="end_month" id="" value="{{ Request::get('end_month') ?? date('Y-m') }}" class="form-control form-control-sm rounded">
                        </div>
                        <div class="text-center">
                            <button class="btn btn-sm btn-outline-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <div class="col-md-9">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Program List</h6>
                    @if($isFinance || $isSuperAdmin)
                        <div class="">
                            <button class="btn btn-sm btn-outline-info" onclick="ExportToExcel()">
                                <i class="bi bi-file-earmark-excel me-1"></i> Print
                            </button>
                        </div>
                    @endif
                </div>
                <div class="card-body overflow-auto" style="max-height: 500px">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="tbl_program_tracking">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice ID</th>
                                    <th>Client Name</th>
                                    <th>City</th>
                                    <th>School Name</th>
                                    <th>Program Name</th>
                                    <th>Price USD</th>
                                    <th>Price IDR</th>
                                    <th>Method</th>
                                    <th>Installment</th>
                                    <th>Destination Country</th>
                                    <th>Graduation Year</th>
                                    <th>Joined Date</th>
                                    <th>Lead Source</th>
                                    <th>Coversion Lead</th>
                                    <th>Category</th>
                                    <th>Status Program</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($program_tracking as $pt)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $pt->inv_id }}
                                        </td>
                                        
                                        {{-- Client Name --}}
                                        <td>{{ $pt->clientprog->client->full_name }}</td>

                                        {{-- City --}}
                                        <td>{{ $pt->clientprog->client->city ?? '-' }}</td> 

                                        {{-- School Name --}}
                                        <td>{{ isset($pt->clientprog->client->school) ? $pt->clientprog->client->school->sch_name : '-' }}</td>

                                        {{-- Program Name --}}
                                        <td>{{ $pt->clientprog->program->program_name }}</td>

                                        {{-- Amount USD --}}
                                        <td>{{ $pt->currency == 'usd' ? $pt->invoiceTotalprice : '-' }}</td>
                                        
                                        {{-- Price IDR --}}
                                        <td>{{ $pt->invoiceTotalpriceIdr }}</td>

                                        {{-- Method --}}
                                        <td>{{ $pt->inv_paymentmethod }}</td>
                                        
                                        {{-- Installment --}}
                                        <td class="text-center">
                                            @if(count($pt->invoiceDetail) > 0)
                                                {{ count($pt->invoiceDetail) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        
                                        {{-- Destination Country --}}
                                        <td>{{ count($pt->clientprog->client->destinationCountries) > 0 ? implode(', ', json_decode($pt->clientprog->client->destinationCountries->pluck('name')->toJson())) : '-' }}</td>
                                        
                                        {{-- Graduation Year --}}
                                        <td>{{ $pt->clientprog->client->graduationYearReal }}</td>
                                        
                                        {{-- Joined Date --}}
                                        <td>{{ date('F, dS Y', strtotime($pt->clientprog->client->created_at)) }}</td>

                                        {{-- Lead Source --}}
                                        <td>{{ $pt->clientprog->client->lead_source }}</td>
                                        
                                        {{-- Conversion Lead --}}
                                        <td>{{ $pt->clientprog->conversionLead }}</td>
                                        
                                        {{-- Conversion Lead --}}
                                        <td>{{ ucfirst($pt->clientprog->client->category) }}</td>

                                        {{-- Status Program --}}
                                        <td>
                                            @switch($pt->clientprog->status)
                                            @case(1)
                                                Success
                                                @break
                                            @case(3)
                                                Refund
                                                @break
                                            @case(4)
                                                Hold
                                                @break
                                            @endswitch
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="17" class="text-center">Not data yet</td>
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
            $privilage = $menus['Report']->where('submenu_name', 'Program Tracking')->first();
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

            var sheetName = ['Program Tracking'];

            var tableName = ['tbl_program_tracking'];

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
                var last_col = parseInt(last_ref.slice(last_ref.indexOf('Q') + 1)) - 1;

                var col = ['G', 'H']; //  I = Amount IDR, J = USD, K = SGD, L = GBP, M = Refund(IDR)
                col.forEach(function (d, i){
                    
                    for(var i = 2; i <= last_col; i++) {
                        var index = d + i;

                        var format_cell;
                        var remove_cursymbol;
                        switch (d) {
                            case 'H':
                                remove_cursymbol = 'Rp.';
                                format_cell = 'Rp#,##0;(Rp#,##0)';
                                break;
                            case 'G':
                                format_cell = '$#,##0;($#,##0)';
                                break;
                        }
                        if(workbook.Sheets[sheet][index].v != '-' && d != 'G')
                            workbook.Sheets[sheet][index].v =  parseInt(workbook.Sheets[sheet][index].v.replace(remove_cursymbol, "").replaceAll(",", ""));

                        workbook.Sheets[sheet][index].t = 'n';
                        workbook.Sheets[sheet][index].z = format_cell;
                    }
                    
                    workbook.Sheets[sheet][d + i] = { t:'n', z:format_cell, f: `SUM(${d+2}:` + index +")", F:d + i + ":" + d + i }
                    

                })

            })
           
            XLSX.writeFile(workbook, "report-program-tracking.xlsx");
            
        }
    </script>
@endsection
