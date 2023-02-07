@extends('layout.main')

@section('title', 'Sales Tracking - Bigdata Platform')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Client Event</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('report.client.event') }}" method="GET">
                        {{-- @csrf --}}
                        <div class="mb-3">
                            <label>Event Name</label>
                            <select name="event_id" id="" class="select w-100">
                                <option data-placeholder="true"></option>
                                @foreach ($events as $event)
                                    <option value="{{ $event->event_id }}">{{ $event->event_title }}</option>
                                @endforeach

                            </select>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-search me-1"></i>
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Client  --}}
            <div class="card mb-3">
                <div class="card-header  d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Client</h6>
                    <span class="badge bg-primary">{{ count($clientEvents) }}</span>
                </div>
                <div class="card-body p-2 overflow-auto" style="max-height: 150px ">
                    <ul class="list-group">
                        @forelse ($clients as $client)
                            <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                                <div class="">{{ $client->role_name }}</div>
                                <span class="badge badge-info">{{ $client->count_role }}</span>
                            </li>
                        @empty
                            <li class="text-center">Not Client yet</li> 
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Conversion Lead  --}}
            <div class="card mb-3">
                <div class="card-header  d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Conversion Lead</h6>
                </div>
                <div class="card-body p-2 overflow-auto" style="max-height: 150px ">
                    <ul class="list-group">
                        @forelse ($conversionLeads as $conversionLead)
                            <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                                <div class="">{{ $conversionLead->conversion_lead }}</div>
                                <span class="badge badge-warning">{{ $conversionLead->count_conversionLead }}</span>
                            </li>
                        @empty
                            <li class="text-center">Not conversion lead yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Client Event</h6>
                    <div class="">
                        <button onclick="tablesToExcel(['tbl_event'], ['Client Event'], 'report-event-tracking.xls', 'Excel')" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark-excel me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body overflow-auto" style="height: 500px">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100 table2excel" id="tbl_event">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>School Name</th>
                                    <th>Grade</th>
                                    <th>Conversion Lead</th>
                                    <th>Joined At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clientEvents as $clientEvent)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $clientEvent->client_name }}</td>
                                        <td>{{ $clientEvent->mail }}</td>
                                        <td>{{ $clientEvent->phone }}</td>
                                        <td>{{ isset($clientEvent->sch_name) ? $clientEvent->sch_name : '-' }}</td>
                                        <td>{{ isset($clientEvent->st_grade) ? $clientEvent->st_grade : '-' }}</td>
                                        <td> {{ $clientEvent->conversion_lead }}
                                        </td>
                                        <td>{{ isset($clientEvent->joined_date) ? $clientEvent->joined_date : '-' }}</td>
                                @empty
                                        <td colspan="8" class="text-center">
                                            Not yet event
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
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
