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

            var sheetName = ['School Programs', 'Partner Programs', 'School Visits', 'New School', 'New Partner', 'New University'];

            var tableName = ['tblsch_prog', 'tblpartner_prog', 'tbl_schvisit', 'tbl_newsch', 'tbl_newpartner', 'tbl_newuniv'];

            var ws = new Array();

            var workbook = XLSX.utils.book_new();
            tableName.forEach(function (d, i) {
                ws[i] = XLSX.utils.table_to_sheet(document.getElementById(tableName[i]));
                XLSX.utils.book_append_sheet(workbook, ws[i], sheetName[i]);
            })
            
            XLSX.writeFile(workbook, "report-partnership.xlsx");
            
        }
    </script>
@endsection
