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
                        <a href="#referral-in"
                            class="card-body d-flex justify-content-between align-items-center text-decoration-none text-white">

                            <strong class="">
                                Total Referral In
                            </strong>
                            <h5 class="text-end m-0 badge bg-white text-dark">
                                {{ count($referrals_in) }}
                            </h5>
                        </a>
                    </div>
                    <div class="card mb-1 bg-danger text-white">
                        <a href="#referral-out"
                            class="card-body d-flex justify-content-between align-items-center text-decoration-none text-white">

                            <strong class="">
                                Total Referral Out
                            </strong>
                            <h5 class="text-end m-0 badge bg-white text-dark">
                                {{ count($referrals_out) }}
                            </h5>
                        </a>
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
            <button class="btn btn-sm btn-outline-info" onclick="ExportToExcel()"
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
                                    <td>{{ $schoolProgram->program->program_name }}</td>
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
                                    <td>{{ $partnerProgram->program->program_name }}</td>
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

            <div class="card mb-3" id="referral-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Referral In</h6>
                </div>
                <div class="card-body overflow-auto" style="max-height: 500px">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="tbl_ref_in">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Partner Name</th>
                                    <th>Program Name</th>
                                    <th>Participants</th>
                                    <th>Referral Fee IDR</th>
                                    <th>Referral Fee USD</th>
                                    <th>Referral Fee SGD</th>
                                    <th>Referral Fee GBP</th>
                                    <th>PIC</th>
                                    <th>Created at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($referrals_in as $referral)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $referral->partner->corp_name }}</td>
                                        <td>{{ $referral->program->program_name }}</td>
                                        <td>{{ $referral->number_of_student }}</td>
                                        <td>Rp. {{ number_format($referral->revenue) }}</td>
                                        <td>{{ $referral->currency == "USD" ? '$. ' . number_format($referral->revenue_other) : '-' }}</td>
                                        <td>{{ $referral->currency == "SGD" ? 'S$. ' . number_format($referral->revenue_other) : '-' }}</td>
                                        <td>{{ $referral->currency == "GBP" ? '£. ' . number_format($referral->revenue_other) : '-' }}</td>
                                        <td>{{ $referral->user->first_name }} {{ $referral->user->last_name }}</td>
                                        <td>{{ $referral->created_at }}</td>
                                    </tr>
                                @empty
                                    <td colspan="10" class="text-center">Not new referral yet</td>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4">Total Amount</th>
                                    <th>Rp. {{ number_format($referrals_in->sum('revenue')) }}</th>
                                    <th>$. {{ number_format($referrals_in->where('currency', 'USD')->sum('revenue_other')) }}</th>
                                    <th>S$. {{ number_format($referrals_in->where('currency', 'SGD')->sum('revenue_other')) }}</th>
                                    <th>£. {{ number_format($referrals_in->where('currency', 'GBP')->sum('revenue_other')) }}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>

            <div class="card mb-3" id="referral-out">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Referral Out</h6>
                </div>
                <div class="card-body overflow-auto" style="max-height: 500px">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="tbl_ref_out">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Partner Name</th>
                                    <th>Program Name</th>
                                    <th>Participants</th>
                                    <th>Referral Fee IDR</th>
                                    <th>Referral Fee USD</th>
                                    <th>Referral Fee SGD</th>
                                    <th>Referral Fee GBP</th>
                                    <th>PIC</th>
                                    <th>Created at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($referrals_out as $referral)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $referral->partner->corp_name }}</td>
                                        <td>{{ $referral->additional_prog_name }}</td>
                                        <td>{{ $referral->number_of_student }}</td>
                                        <td>Rp. {{ number_format($referral->revenue) }}</td>
                                        <td>{{ $referral->currency == "USD" ? '$. ' . number_format($referral->revenue_other) : '-' }}</td>
                                        <td>{{ $referral->currency == "SGD" ? 'S$. ' . number_format($referral->revenue_other) : '-' }}</td>
                                        <td>{{ $referral->currency == "GBP" ? '£. ' . number_format($referral->revenue_other) : '-' }}</td>
                                        <td>{{ $referral->user->first_name }} {{ $referral->user->last_name }}</td>
                                        <td>{{ $referral->created_at }}</td>
                                    </tr>
                                @empty
                                    <td colspan="10" class="text-center">Not new referral yet</td>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4">Total Amount</th>
                                    <th>Rp. {{ number_format($referrals_out->sum('revenue')) }}</th>
                                    <th>$. {{ number_format($referrals_out->where('currency', 'USD')->sum('revenue_other')) }}</th>
                                    <th>S$. {{ number_format($referrals_out->where('currency', 'SGD')->sum('revenue_other')) }}</th>
                                    <th>£. {{ number_format($referrals_out->where('currency', 'GBP')->sum('revenue_other')) }}</th>
                                    <th></th>
                                    <th></th>
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
                <div class="card-body overflow-auto" style="max-height: 500px">
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
                <div class="card-body overflow-auto" style="max-height: 500px">
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
                <div class="card-body overflow-auto" style="max-height: 500px">
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
        @php            
            $privilage = $menus['Report']->where('submenu_name', 'Partnership')->first();
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

            var sheetName = ['School Programs', 'Partner Programs', 'Referral In', 'Referral Out', 'School Visits', 'New School', 'New Partner', 'New University'];

            var tableName = ['tblsch_prog', 'tblpartner_prog', 'tbl_ref_in', 'tbl_ref_out', 'tbl_schvisit', 'tbl_newsch', 'tbl_newpartner', 'tbl_newuniv'];

            var ws = new Array();

            var workbook = XLSX.utils.book_new();
            tableName.forEach(function (d, i) {
                ws[i] = XLSX.utils.table_to_sheet(document.getElementById(tableName[i]));
                XLSX.utils.book_append_sheet(workbook, ws[i], sheetName[i]);
            })

            var sheetNamePrograms = ['School Programs', 'Partner Programs', 'Referral In', 'Referral Out'];
            
            
            sheetNamePrograms.forEach(function (d, i){

                var sheet = d;
                var full_ref = workbook.Sheets[sheet]['!fullref'];
                var last_ref = full_ref.slice(full_ref.indexOf(':') + 1);
                
                
                if(sheet == 'School Programs' || sheet == 'Partner Programs'){
                    var last_col = parseInt(last_ref.slice(last_ref.indexOf('G') + 1)) - 1;

                    if(last_col > 2){
                        for (var j = 2; j<=last_col; j++){
        
                            var index = 'F' + j;
                            var format_cell = 'Rp#,##0;(Rp#,##0)';
                
                            workbook.Sheets[sheet][index].v =  parseInt(workbook.Sheets[sheet][index].v.replace('Rp.', "").replaceAll(",", ""));
                            workbook.Sheets[sheet][index].t = 'n';
                            workbook.Sheets[sheet][index].z = format_cell;
                        }
        
                        workbook.Sheets[sheet]['F' + j] = { t:'n', z:format_cell, f: `SUM(${'F'+2}:` + index +")", F:'F' + j + ":" + 'F' + j }
                    }
                }else{
                    var col = ['E', 'F', 'G', 'H'];
                    var last_col = parseInt(last_ref.slice(last_ref.indexOf('J') + 1)) - 1;
                    
                    col.forEach(function (d, i){
                        for(var i = 2; i <= last_col; i++) {
                            var index = d + i;
    
                            var format_cell;
                            var remove_cursymbol;
                            switch (d) {
                                case 'E':
                                    remove_cursymbol = 'Rp.';
                                    format_cell = 'Rp#,##0;(Rp#,##0)';
                                    break;
                                case 'F':
                                    remove_cursymbol = '$.';
                                    format_cell = '$#,##0;($#,##0)';
                                    break;
                                case 'G':
                                    remove_cursymbol = 'S$.';
                                    format_cell = '$#,##0;($#,##0)';
                                    break;
                                case 'H':
                                    remove_cursymbol = '£.';
                                    format_cell = '£#,##0;(£#,##0)';
                                    break;
                            }
                            if(workbook.Sheets[sheet][index].v != '-')
                                workbook.Sheets[sheet][index].v =  parseInt(workbook.Sheets[sheet][index].v.replace(remove_cursymbol, "").replaceAll(",", ""));
    
                            workbook.Sheets[sheet][index].t = 'n';
                            workbook.Sheets[sheet][index].z = format_cell;
                        }
                        workbook.Sheets[sheet][d + i] = { t:'n', z:format_cell, f: `SUM(${d+2}:` + index +")", F:d + i + ":" + d + i }
                    })
                }

            })
            console.log(workbook);
            
            XLSX.writeFile(workbook, "report-partnership.xlsx");
            
        }
    </script>
@endsection
