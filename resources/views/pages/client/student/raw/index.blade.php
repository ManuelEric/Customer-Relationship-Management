@extends('layout.main')

@section('title', 'Student')

@push('styles')
    <link rel="stylesheet" href="{{ asset('library/dashboard/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">
    <style>
        .btn-download span,
        .btn-import span {
            display: none;
        }

        .btn-download:hover>span,
        .btn-import:hover>span {
            display: inline-block;
        }

        td.dt-control {
            background: url('http://www.datatables.net/examples/resources/details_open.png') no-repeat center center;
            cursor: pointer;
        }

        tr.shown td.dt-control {
            background: url('http://www.datatables.net/examples/resources/details_close.png') no-repeat center center;
        }
    </style>
@endpush

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="row align-items-center justify-content-between g-3">
            <div class="col-md-6">
                <h5 class="text-white m-0">
                    <i class="bi bi-tag me-1"></i>
                    Students
                </h5>
            </div>
            <div class="col-md-6">
                <div class="row g-2">
                    <div class="col-md-3 col-6">
                        <a href="{{ url('api/download/excel-template/student') }}"
                            class="btn btn-sm btn-light text-info btn-download w-100"><i class="bi bi-download"></i> <span
                                class="ms-1">Template</span></a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="javascript:void(0)" class="btn btn-sm btn-light text-info btn-import w-100"
                            data-bs-toggle="modal" data-bs-target="#importData"><i class="bi bi-cloud-upload"></i> <span
                                class="ms-1">Import</span></a>
                    </div>
                    <div class="col-md-3">
                        <div class="dropdown">
                            <button href="#" class="btn btn-sm btn-light text-dark dropdown-toggle w-100"
                                data-bs-toggle="dropdown" data-bs-auto-close="false" id="filter">
                                <i class="bi bi-funnel me-2"></i> Filter
                            </button>
                            <form action="" class="dropdown-menu dropdown-menu-end pt-0 advance-filter shadow"
                                style="width: 400px;" id="advanced-filter">
                                <div class="dropdown-header bg-info text-dark py-2 d-flex justify-content-between">
                                    Advanced Filter
                                    <i class="bi bi-search"></i>
                                </div>
                                <div class="row p-3">
                                    <div class="col-md-12 mb-2">
                                        <label for="">School Name</label>
                                        <select name="school_name[]" class="select form-select form-select-sm w-100"
                                            multiple id="school-name">
                                            {{-- @foreach ($advanced_filter['schools'] as $school)
                                                <option value="{{ $school->sch_name }}">{{ $school->sch_name }}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <label for="">Graduation Year</label>
                                        <select name="graduation_year[]" class="select form-select form-select-sm w-100"
                                            multiple id="graduation-year">
                                            {{-- @for ($i = $advanced_filter['max_graduation_year']; $i >= 2016; $i--)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor --}}
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <label for="">Lead Source</label>
                                        <select name="lead_source[]" class="select form-select form-select-sm w-100"
                                            multiple id="lead-sources">
                                            {{-- @foreach ($advanced_filter['leads'] as $lead)
                                                <option value="{{ $lead['main_lead'] }}">{{ $lead['main_lead'] }}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <label for="">Program Suggestion</label>
                                        <select name="program_name[]" class="select form-select form-select-sm w-100"
                                            multiple id="program-name">
                                            {{-- @foreach ($advanced_filter['initial_programs'] as $init_program)
                                                <option value="{{ $init_program->name }}">{{ $init_program->name }}
                                                </option>
                                            @endforeach --}}
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <label for="">Lead Status</label>
                                        <select name="lead_status[]" class="select form-select form-select-sm w-100"
                                            multiple id="lead-source">
                                            <option value="Hot">Hot</option>
                                            <option value="Warm">Warm</option>
                                            <option value="Cold">Cold</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <label for="">Active Status</label>
                                        <select name="active_status[]" class="select form-select form-select-sm w-100"
                                            multiple id="active-status">
                                            <option value="1">Active</option>
                                            <option value="0">Non-active</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12 mt-3 d-none">
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                id="cancel">Cancel</button>
                                            <button type="button" id="submit"
                                                class="btn btn-sm btn-outline-success">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ url('client/student/create') }}" class="btn btn-sm btn-info w-100"><i
                                class="bi bi-plus-square me-1"></i> Add Student</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs flex-nowrap overflow-auto w-100 mb-3" style="overflow-y: hidden !important;">
                <li class="nav-item">
                    <a class="nav-link text-nowrap active" aria-current="page"
                        href="{{ url('client/student/raw-data') }}">Raw Dataa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ Request::get('st') == 'new-leads' ? 'active' : '' }}"
                        aria-current="page" href="{{ url('client/student?st=new-leads') }}">New Leads</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ Request::get('st') == 'potential' ? 'active' : '' }}"
                        href="{{ url('client/student?st=potential') }}">Potential</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ Request::get('st') == 'mentee' ? 'active' : '' }}"
                        href="{{ url('client/student?st=mentee') }}">Mentee</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ Request::get('st') == 'non-mentee' ? 'active' : '' }}"
                        href="{{ url('client/student?st=non-mentee') }}">Non-Mentee</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap" href="{{ url('client/student') }}">All</a>
                </li>
            </ul>


            <style>
                #clientTable tr td.danger {
                    background: rgb(255, 151, 151)
                }
            </style>
            <div class="table-responsive">
                <table class="table table-bordered table-hover nowrap align-middle w-100" id="rawTable">
                    <thead class="bg-secondary text-white">
                        <tr class="text-center" role="row">
                            <th class="bg-info text-white">#</th></th>
                            <th class="bg-info text-white">No</th>
                            <th class="bg-info text-white">Name</th>
                            <th class="bg-info text-white">Suggestion</th>
                            <th>Mail</th>
                            <th>Phone</th>
                            <th>Parents Name</th>
                            <th>Parents Mail</th>
                            <th>Parents Phone</th>
                            <th>School</th>
                            <th>Graduation Year</th>
                            {{-- <th>Grade</th>
                            <th>Instagram</th>
                            <th>Location</th> --}}
                            <th>Lead</th>
                            {{-- <th>Level of Interest</th>
                            <th>Interested Program</th>
                            <th>Year of Study Abroad</th> --}}
                            <th>Country of Study Abroad</th>
                            {{-- <th>University Destination</th>
                            <th>Interest Major</th> --}}
                            <th>Joined Date</th>
                            <th>Last Update</th>
                            {{-- <th>Status</th> --}}
                            <th class="bg-info text-white"># Action</th>
                        </tr>
                    </thead>
                    {{-- <tbody>
                        @for ($i = 1; $i <= 20; $i++)
                            <tr class="text-center" role="row">
                                <td class="dt-control"></td>
                                <td>Name
                                    <div class="badge badge-warning py-1 px-2 ms-2">4 Similar Names</div>
                                </td>
                                <td>Mail</td>
                                <td>Phone</td>
                                <td>Parents Name</td>
                                <td>Parents Mail</td>
                                <td>Parents Phone</td>
                                <td>School
                                    <div class="badge badge-danger py-1 px-2 ms-2">Not Verified</div>
                                </td>
                                <td>Graduation Year
                                </td>
                                <td>Grade</td>
                                <td>Instagram</td>
                                <td>Location</td>
                                <td>Lead</td>
                                <td>Level of Interest</td>
                                <td>Interested Program</td>
                                <td>Year of Study Abroad</td>
                                <td>Country of Study Abroad</td>
                                <td>University Destination</td>
                                <td>Interest Major</td>
                                <td>Joined Date</td>
                                <td>Last Update</td>
                                <td>Status</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 10px"
                                        href="{{ url('client/student/raw-data/' . $i) }}">
                                        Convert
                                    </a>
                                </td>
                            </tr>
                        @endfor
                    </tbody> --}}
                    {{-- <tfoot class="bg-light text-white">
                        <tr>
                            <td colspan="16"></td>
                        </tr>
                    </tfoot> --}}
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importData" tabindex="-1" aria-labelledby="importDataLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('student.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="importDataLabel">Import CSV Data</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="">CSV File</label>
                                <input type="file" name="file" id=""
                                    class="form-control form-control-sm">
                            </div>
                            <small class="text-warning mt-3">
                                * Please clean the file first, before importing the csv file. <br>
                                You can download the csv template <a
                                    href="{{ url('api/download/excel-template/student') }}">here</a>
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">
                            <i class="bi bi-x"></i>
                            Close</button>
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-upload"></i>
                            Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        var widthView = $(window).width();
        $(document).ready(function() {

            // Formatting function for row details - modify as you need
            function format(d) {
                var similar = '<table class="table w-auto table-borderless">' +
                    '<tr>' +
                    '<th colspan=5>Comparison with Similar Names:</th>' +
                    '</tr>'

                for (let i = 0; i < 2; i++) {
                    similar += '<tr>' +
                        '<td><input type="radio" name="similar" class="form-check-input" onclick="comparison(' + i +
                        ',5)" /></td>' +
                        '<td>Full Name</td>' +
                        '<td>Email</td>' +
                        '<td>Phone Number</td>' +
                        '<td>School Name</td>' +
                        '<td>Graduation Year</td>' +
                        '</tr>'
                }
                similar +=
                    '<tr>' +
                    '<th colspan=5>Convert without Comparison</th>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><input type="radio" name="similar" class="form-check-input" onclick="newLeads(1)" /></td>' +
                    '<td colspan=5>New Lead</td>' +
                    '</tr>' +
                    '</table>'
                // `d` is the original data object for the row
                return (similar);
            }

            var table = $('#rawTable').DataTable({
                order: [
                    // [20, 'desc'],
                    [1, 'asc']
                ],
                dom: 'Bfrtip',
                buttons: [
                    'pageLength', {
                        extend: 'excel',
                        text: 'Export to Excel',
                    }
                ],
                scrollX: true,
                fixedColumns: {
                    left: (widthView < 768) ? 1 : 2,
                    right: 1
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: '',
                },
                columns: [{
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: ''
                    },
                    {
                        data: 'id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'fullname',
                        render: function(data, type, row, meta) {
                            return data
                        }
                    },
                    {
                        data: 'suggestion',
                        render: function(data, type, row, meta) {
                            return '<div class="badge badge-warning py-1 px-2 ms-2">'+data.length+' Similar Names</div>'
                        }
                    },
                    {
                        data: 'mail',
                        defaultContent: '-'
                    },
                    {
                        data: 'phone',
                        defaultContent: '-'
                    },
                    {
                        data: 'parent_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'parent_mail',
                        defaultContent: '-'
                    },
                    {
                        data: 'parent_phone',
                        defaultContent: '-'
                    },
                    {
                        data: 'school',
                        defaultContent: '-',
                    },
                    {
                        data: 'graduation_year',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'lead_source',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'interest_countries',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'created_at',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'updated_at',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning editClient"><i class="bi bi-eye"></i></button>'
                    }
                ],
            });


            // Add a click event listener to each row in the parent DataTable
            table.on('click', 'td.dt-control', function(e) {
                let tr = e.target.closest('tr');
                let row = table.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                }
            });

            function comparison(id, id2) {
                window.open("{{ url('client/student/raw/') }}" + '/' + id + '/comparison/' + id2, "_blank");
            }

            function newLeads(id) {
                window.open("{{ url('client/student/raw/') }}" + '/' + id, "_blank");
            }


        });
    </script>
@endpush
