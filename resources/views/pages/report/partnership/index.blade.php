@extends('layout.main')

@section('title', 'Partnership Report - Bigdata Platform')

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

            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Partnership Detail</h6>
                </div>
                <div class="card-body">
                    <div class="card mb-1 bg-danger text-white">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <strong class="">
                                Total School Visit
                            </strong>
                            <h5 class="text-end m-0 badge bg-white text-dark">
                                {{ count($schoolVisits) }}
                            </h5>
                        </div>
                    </div>
                    <div class="card mb-1 bg-danger text-white">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <strong class="">
                                Total School Program
                            </strong>
                            <h5 class="text-end m-0 badge bg-white text-dark">
                                {{ count($schoolPrograms) }}
                            </h5>
                        </div>
                    </div>
                    <div class="card mb-1 bg-danger text-white">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <strong class="">
                                Total Partner Program
                            </strong>
                            <h5 class="text-end m-0 badge bg-white text-dark">
                                {{ count($partnerPrograms) }}
                            </h5>
                        </div>
                    </div>
                    <div class="card mb-1 bg-danger text-white">
                        <a href="#school"
                            class="card-body d-flex justify-content-between align-items-center text-decoration-none text-white">
                            <strong class="">
                                Total New School
                            </strong>
                            <h5 class="text-end m-0 badge bg-white text-dark">
                                {{ count($schools) }}
                            </h5>
                        </a>
                    </div>
                    <div class="card mb-1 bg-danger text-white">
                        <a href="#partner"
                            class="card-body d-flex justify-content-between align-items-center text-decoration-none text-white">

                            <strong class="">
                                Total New Partner
                            </strong>
                            <h5 class="text-end m-0 badge bg-white text-dark">
                                {{ count($partners) }}
                            </h5>
                        </a>
                    </div>
                    <div class="card mb-1 bg-danger text-white">
                        <a href="#university"
                            class="card-body d-flex justify-content-between align-items-center text-decoration-none text-white">

                            <strong class="">
                                Total New University
                            </strong>
                            <h5 class="text-end m-0 badge bg-white text-dark">
                                {{ count($universities) }}
                            </h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-md-9">
            <button class="btn btn-sm btn-outline-info" onclick="tablesToExcel(['tblsch_prog','tblpartner_prog','tbl_schvisit','tbl_newsch','tbl_newpartner','tbl_newuniv'], ['School Program','Partner Program','School Visit','New School','New Partner','New University'], 'report-partnership.xls', 'Excel')"
                style="margin-bottom: 15px">
                <i class="bi bi-file-earmark-excel me-1"></i> Print
            </button>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">School Program</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100 table2excel" id="tblsch_prog">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>School Name</th>
                                    <th>Program Name</th>
                                    <th>Program Date</th>
                                    <th>Participants</th>
                                    <th>Amount</th>
                                    <th>PIC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($schoolPrograms as $schoolProgram)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $schoolProgram->school->sch_name }}</td>
                                    <td>{{ $schoolProgram->program->sub_prog ? $schoolProgram->program->sub_prog->sub_prog_name.' - ':'' }}{{ $schoolProgram->program->prog_program }}</td>
                                    <td>{{ $schoolProgram->success_date }}</td>
                                    <td>{{ $schoolProgram->participants }}</td>
                                    <td>Rp. {{ number_format($schoolProgram->total_fee) }}</td>
                                    <td>{{ $schoolProgram->user->first_name }} {{ $schoolProgram->user->last_name }}</td>
                                </tr>
                                @empty
                                    <td colspan="7" class="text-center">Not school program yet</td>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5">Total Amount</th>
                                    <th colspan="2" class="text-center">Rp. {{ number_format($schoolPrograms->sum('total_fee')) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Partner Program</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100 table2excel" id="tblpartner_prog">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Partner Name</th>
                                    <th>Program Name</th>
                                    <th>Program Date</th>
                                    <th>Participants</th>
                                    <th>Amount</th>
                                    <th>PIC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($partnerPrograms as $partnerProgram)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $partnerProgram->corp->corp_name }}</td>
                                    <td>{{ $partnerProgram->program->sub_prog ? $partnerProgram->program->sub_prog->sub_prog_name.' - ':'' }}{{ $partnerProgram->program->prog_program }}</td>
                                    <td>{{ $partnerProgram->success_date }}</td>
                                    <td>{{ $partnerProgram->participants }}</td>
                                    <td>Rp. {{ number_format($partnerProgram->total_fee) }}</td>
                                    <td>{{ $partnerProgram->user->first_name }} {{ $partnerProgram->user->last_name }}</td>
                                </tr>
                                @empty
                                    <td colspan="7" class="text-center">Not partner program yet</td>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5">Total Amount</th>
                                    <th colspan="2" class="text-center">Rp. {{ number_format($partnerPrograms->sum('total_fee')) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3" id="school_visit">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">School Visit</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="tbl_schvisit">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>School Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Address</th>
                                    <th>Internal PIC</th>
                                    <th>School PIC</th>
                                    <th>Visit Date</th>
                                    <th>Created at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($schoolVisits as $schoolVisit)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $schoolVisit->school->sch_name }}</td>
                                        <td>{{ $schoolVisit->school->sch_mail }}</td>
                                        <td>{{ $schoolVisit->school->sch_phone }}</td>
                                        <td>{!! $schoolVisit->school->sch_location !!}</td>
                                        <td>{{ $schoolVisit->pic_from_allin->first_name }} {{ $schoolVisit->pic_from_allin->last_name }}</td>
                                        <td>{{ $schoolVisit->pic_from_school->schdetail_fullname }}</td>
                                        <td>{{ $schoolVisit->visit_date }}</td>
                                        <td>{{ $schoolVisit->created_at }}</td>
                                    </tr>
                                @empty
                                    <td colspan="9" class="text-center">Not school visit yet</td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <div class="card mb-3" id="school">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">New School</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="tbl_newsch">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>School Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Address</th>
                                    <th>Created at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($schools as $school)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $school->sch_name }}</td>
                                        <td>{{ $school->sch_mail }}</td>
                                        <td>{{ $school->sch_phone }}</td>
                                        <td>{!! $school->sch_location !!}</td>
                                        <td>{{ $school->created_at }}</td>
                                    </tr>
                                @empty
                                    <td colspan="6" class="text-center">Not new school yet</td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <div class="card mb-3" id="partner">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">New Partner</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="tbl_newpartner">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Partner Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Address</th>
                                    <th>Created at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($partners as $partner)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $partner->corp_name }}</td>
                                        <td>{{ $partner->corp_mail }}</td>
                                        <td>{{ $partner->corp_phone }}</td>
                                        <td>{{ $partner->corp_address }}</td>
                                        <td>{{ $partner->created_at }}</td>
                                    </tr>
                                @empty
                                    <td colspan="6" class="text-center">Not new partner yet</td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <div class="card mb-3" id="university">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">New University</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="tbl_newuniv">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>University Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Address</th>
                                    <th>Created at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($universities as $university)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $university->univ_name }}</td>
                                        <td>{{ $university->univ_mail }}</td>
                                        <td>{{ $university->univ_phone }}</td>
                                        <td>{{ $university->univ_address }}</td>
                                        <td>{{ $university->created_at }}</td>
                                    </tr>
                                @empty
                                    <td colspan="6" class="text-center">Not new university yet</td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function showHideDetail(d) {
            if ($('#' + d).hasClass('d-none')) {
                $('#' + d).removeClass('d-none')
            } else {
                $('#' + d).addClass('d-none')
            }
        }
    </script>

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
