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
                            <input type="date" name="start_date" id="" class="form-control form-control-sm rounded">
                        </div>
                        <div class="mb-3">
                            <label>End Date</label>
                            <input type="date" name="end_date" id="" class="form-control form-control-sm rounded">
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
                           Rp. {{ number_format($invoices->sum('inv_totalprice_idr')+$invoices->sum('invb2b_totpriceidr'), '2', ',', '.') }}
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
            <button class="btn btn-sm btn-outline-info" onclick="tablesToExcel(['tbl_inv','tbl_receipt'], ['Invoice','Receipt'], 'report-invoice-receipt.xls', 'Excel')"
                style="margin-bottom: 15px">
                <i class="bi bi-file-earmark-excel me-1"></i> Print
            </button>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Invoice List</h6>
                </div>
                <div class="card-body">
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
                                        @endif

                                        {{-- Type --}}
                                        <td>{{ isset($invoice->inv_id) ? 'B2C' : 'B2B' }}</td>

                                        {{-- Program Name --}}
                                        @if(isset($invoice->clientprog_id))
                                            <td>{{ $invoice->clientprog->program->sub_prog ? $invoice->clientprog->program->sub_prog->sub_prog_name.' - ':''}}{{ $invoice->clientprog->program->prog_program }}</td>
                                        @elseif(isset($invoice->schprog_id))
                                            <td>{{ $invoice->sch_prog->program->sub_prog ? $invoice->sch_prog->program->sub_prog->sub_prog_name.' - ':''}}{{ $invoice->sch_prog->program->prog_program }}</td>
                                        @elseif(isset($invoice->partnerprog_id))
                                            <td>{{ $invoice->partner_prog->program->sub_prog ? $invoice->partner_prog->program->sub_prog->sub_prog_name.' - ':''}}{{ $invoice->partner_prog->program->prog_program }}</td>
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
                <div class="card-body">
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
                                            @endif
                                        @endif

                                        {{-- Type --}}
                                        <td>{{ $receipt->inv_id ? 'B2C' : 'B2B'}}</td>

                                        {{-- Program Name --}}
                                        @if(isset($receipt->inv_id))
                                            <td>{{ $receipt->invoiceProgram->clientprog->program->sub_prog ? $receipt->invoiceProgram->clientprog->program->sub_prog->sub_prog_name.' - ':''}}{{ $receipt->invoiceProgram->clientprog->program->prog_program }}</td>
                                        @elseif(isset($receipt->invb2b_id))
                                            @if(isset($receipt->invoiceB2b->schprog_id))
                                                <td>{{ $receipt->invoiceB2b->sch_prog->program->sub_prog ? $receipt->invoiceB2b->sch_prog->program->sub_prog->sub_prog_name.' - ':''}}{{ $receipt->invoiceB2b->sch_prog->program->prog_program }}</td>
                                            @elseif((isset($receipt->invoiceB2b->partnerprog_id)))
                                                <td>{{ $receipt->invoiceB2b->partner_prog->program->sub_prog ? $receipt->invoiceB2b->partner_prog->program->sub_prog->sub_prog_name.' - ':''}}{{ $receipt->invoiceB2b->partner_prog->program->prog_program }}</td>
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
            var tablesToExcel = (function() {
            var uri = 'data:application/vnd.ms-excel;base64,'
            , tmplWorkbookXML = '<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'
            + '<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office"><Author>Axel Richter</Author><Created>{created}</Created></DocumentProperties>'
            + '<Styles>'
            + '<Style ss:ID="Currency"><NumberFormat ss:Format="Currency"></NumberFormat></Style>'
            + '<Style ss:ID="Date"><NumberFormat ss:Format="Medium Date"></NumberFormat></Style>'
            + '</Styles>' 
            + '{worksheets}</Workbook>'
            , tmplWorksheetXML = '<Worksheet ss:Name="{nameWS}"><Table>{rows}</Table></Worksheet>'
            , tmplCellXML = '<Cell{attributeStyleID}{attributeFormula}><Data ss:Type="{nameType}">{data}</Data></Cell>'
            , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
            , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
            return function(tables, wsnames, wbname, appname) {
            var ctx = "";
            var workbookXML = "";
            var worksheetsXML = "";
            var rowsXML = "";

            for (var i = 0; i < tables.length; i++) {
                if (!tables[i].nodeType) tables[i] = document.getElementById(tables[i]);
                for (var j = 0; j < tables[i].rows.length; j++) {
                rowsXML += '<Row>'
                for (var k = 0; k < tables[i].rows[j].cells.length; k++) {
                    var dataType = tables[i].rows[j].cells[k].getAttribute("data-type");
                    var dataStyle = tables[i].rows[j].cells[k].getAttribute("data-style");
                    var dataValue = tables[i].rows[j].cells[k].getAttribute("data-value");
                    dataValue = (dataValue)?dataValue:tables[i].rows[j].cells[k].innerHTML;
                    var dataFormula = tables[i].rows[j].cells[k].getAttribute("data-formula");
                    dataFormula = (dataFormula)?dataFormula:(appname=='Calc' && dataType=='DateTime')?dataValue:null;
                    ctx = {  attributeStyleID: (dataStyle=='Currency' || dataStyle=='Date')?' ss:StyleID="'+dataStyle+'"':''
                        , nameType: (dataType=='Number' || dataType=='DateTime' || dataType=='Boolean' || dataType=='Error')?dataType:'String'
                        , data: (dataFormula)?'':dataValue
                        , attributeFormula: (dataFormula)?' ss:Formula="'+dataFormula+'"':''
                        };
                    rowsXML += format(tmplCellXML, ctx);
                }
                rowsXML += '</Row>'
                }
                ctx = {rows: rowsXML, nameWS: wsnames[i] || 'Sheet' + i};
                worksheetsXML += format(tmplWorksheetXML, ctx);
                rowsXML = "";
            }

            ctx = {created: (new Date()).getTime(), worksheets: worksheetsXML};
            workbookXML = format(tmplWorkbookXML, ctx);



            var link = document.createElement("A");
            link.href = uri + base64(workbookXML);
            link.download = wbname || 'Workbook.xls';
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            }
        })();
    </script>
@endsection
