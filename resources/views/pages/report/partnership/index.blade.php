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
                    <div class="mb-3">
                        <label>Start Date</label>
                        <input type="date" name="" id="" class="form-control form-control-sm rounded">
                    </div>
                    <div class="mb-3">
                        <label>End Date</label>
                        <input type="date" name="" id="" class="form-control form-control-sm rounded">
                    </div>
                    <div class="text-center">
                        <button class="btn btn-sm btn-outline-primary">Submit</button>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Partnership Detail</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="">
                            Total School Visit
                        </div>
                        <div class="text-end">
                            134
                        </div>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between">
                        <div class="">
                            Total Partner Program
                        </div>
                        <div class="text-end">
                            134
                        </div>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between" onclick="showHideDetail('school')" style="cursor: pointer">
                        <div class="">
                            Total New School
                        </div>
                        <div class="text-end">
                            20
                        </div>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between" onclick="showHideDetail('partner')" style="cursor: pointer">
                        <div class="">
                            Total New Partner
                        </div>
                        <div class="text-end">
                            13
                        </div>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between" onclick="showHideDetail('univ')" style="cursor: pointer">
                        <div class="">
                            Total New University
                        </div>
                        <div class="text-end">
                            13
                        </div>
                    </div>
                </div>
            </div>

            {{-- School  --}}
            <div class="card mb-3 new-detail d-none" id="school">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">New School</h6>
                    <i class="bi bi-x text-danger" style="cursor: pointer" onclick="showHideDetail('school')"></i>
                </div>
                <div class="card-body overflow-auto" style="height: 150px">
                    <ul class="list-group p-0">
                        @for ($i = 0; $i < 30; $i++)
                            <li class="list-group-item p-1">
                                School Name
                            </li>
                        @endfor
                    </ul>
                </div>
            </div>

            {{-- Partner  --}}
            <div class="card mb-3 new-detail d-none" id="partner">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">New Partner</h6>
                    <i class="bi bi-x text-danger" style="cursor: pointer" onclick="showHideDetail('partner')"></i>
                </div>
                <div class="card-body overflow-auto" style="height: 150px">
                    <ul class="list-group p-0">
                        @for ($i = 0; $i < 30; $i++)
                            <li class="list-group-item p-1">
                                Partner Name
                            </li>
                        @endfor
                    </ul>
                </div>
            </div>

            {{-- University  --}}
            <div class="card mb-3 new-detail d-none" id="univ">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">New University</h6>
                    <i class="bi bi-x text-danger" style="cursor: pointer" onclick="showHideDetail('univ')"></i>
                </div>
                <div class="card-body overflow-auto" style="height: 150px">
                    <ul class="list-group p-0">
                        @for ($i = 0; $i < 30; $i++)
                            <li class="list-group-item p-1">
                                University Name
                            </li>
                        @endfor
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">School Program</h6>
                    <div class="">
                        <button class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark-excel me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="volunteerTable">
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
                                <td class="text-center">1</td>
                                <td>School Name</td>
                                <td>Program Name</td>
                                <td>Program Date</td>
                                <td>Participants</td>
                                <td>Amount</td>
                                <td>PIC</td>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5">Total Amount</th>
                                    <th colspan="2" class="text-center">Rp. 230.000.000</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Partner Program</h6>
                    <div class="">
                        <button class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark-excel me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="volunteerTable">
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
                                    <td>{{ $partnerProgram->start_date }}</td>
                                    <td>{{ $partnerProgram->participants }}</td>
                                    <td>{{ $partnerProgram->total_fee }}</td>
                                    <td>{{ $partnerProgram->user->first_name }} {{ $partnerProgram->user->last_name }}</td>
                                </tr>
                                @empty
                                    <td colspan="7">Not partner program yet</td>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5">Total Amount</th>
                                    <th colspan="2" class="text-center">Rp. 230.000.000</th>
                                </tr>
                            </tfoot>
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
@endsection
