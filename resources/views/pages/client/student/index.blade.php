@extends('layout.main')

@section('title', 'List of Student')

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
                    {{-- <div class="col-md-3 col-6">
                        <a href="{{ url('api/download/excel-template/student') }}"
                            class="btn btn-sm btn-light text-info btn-download w-100"><i class="bi bi-download"></i> <span
                                class="ms-1">Template</span></a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="javascript:void(0)" class="btn btn-sm btn-light text-info btn-import w-100"
                            data-bs-toggle="modal" data-bs-target="#importData"><i class="bi bi-cloud-upload"></i> <span
                                class="ms-1">Import</span></a>
                    </div> --}}
                    <div class="col-md-3 offset-6">
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
                                            @foreach ($advanced_filter['schools'] as $school)
                                                <option value="{{ $school->sch_name }}"
                                                    {{ Request::get('sch') == $school->sch_name && Request::get('sch') != '' && Request::get('sch') != null ? 'selected' : null }}>
                                                    {{ $school->sch_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <label for="">Graduation Year</label>
                                        <select name="graduation_year[]" class="select form-select form-select-sm w-100"
                                            multiple id="graduation-year">
                                            @for ($i = $advanced_filter['max_graduation_year']; $i >= 2016; $i--)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <label for="">Lead Source</label>
                                        <select name="lead_source[]" class="select form-select form-select-sm w-100"
                                            multiple id="lead-sources">
                                            @foreach ($advanced_filter['leads'] as $lead)
                                                <option value="{{ $lead['main_lead'] }}">{{ $lead['main_lead'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <label for="">Program Suggestion</label>
                                        <select name="program_name[]" class="select form-select form-select-sm w-100"
                                            multiple id="program-name">
                                            @foreach ($advanced_filter['initial_programs'] as $init_program)
                                                <option value="{{ $init_program->name }}">{{ $init_program->name }}
                                                </option>
                                            @endforeach
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

                                    {{-- <div class="col-md-12 mb-2">
                                        <label for="">Active Status</label>
                                        <select name="active_status[]" class="select form-select form-select-sm w-100"
                                            multiple id="active-status">
                                            <option value="1">Active</option>
                                            <option value="0">Non-active</option>
                                        </select>
                                    </div> --}}

                                    @if (Session::get('user_role') != 'Employee')
                                        <div class="col-md-12 mb-2">
                                            <label for="">PIC</label>
                                            <select name="pic[]" class="select form-select form-select-sm w-100" multiple
                                                id="pic">
                                                @foreach ($advanced_filter['pics'] as $pic)
                                                    <option value="{{ $pic->id }}">{{ $pic->full_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="col-md-12 mb-2">
                                        <div class="row g-2">
                                            <label>Joined Date</label>
                                            <div class="col-md-6 mb-2">
                                                <input type="date" name="start_joined_date" id="start_joined_date"
                                                    class="form-control form-control-sm rounded">
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <input type="date" name="end_joined_date" id="end_joined_date"
                                                    class="form-control form-control-sm rounded">
                                            </div>
                                        </div>
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
            <x-client.student.nav />

            @push('styles')
                <style>
                    #clientTable tr td.danger {
                        background: rgb(255, 151, 151)
                    }
                </style>
            @endpush
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="clientTable">
                <thead class="bg-secondary text-white">
                    <tr class="text-center" role="row">
                        <th class="d-none">Score</th>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Name</th>
                        <th>Interested Program</th>
                        <th>Program Suggest</th>
                        <th>Status Lead</th>
                        <th>PIC</th>
                        <th>Mail</th>
                        <th>Phone</th>
                        <th>Parents Name</th>
                        <th>Parents Mail</th>
                        <th>Parents Phone</th>
                        <th>School</th>
                        <th>Graduation Year</th>
                        <th>Grade</th>
                        <th>Instagram</th>
                        <th>State/Region</th>
                        <th>City</th>
                        <th>Location</th>
                        <th>Lead</th>
                        <th>Referral From</th>
                        <th>Level of Interest</th>
                        <th>Joined Event</th>
                        {{-- <th>Success Program</th>
                        <th>Mentor/Tutor</th> --}}
                        <th>Year of Study Abroad</th>
                        <th>Country of Study Abroad</th>
                        <th>University Destination</th>
                        <th>Interest Major</th>
                        <th>Joined Date</th>
                        <th>Scholarship Eligible</th>
                        <th>Joined Date</th>
                        <th>Last Update</th>
                        <th>Is Active</th>
                        <th class="bg-info text-white"># Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="21"></td>
                    </tr>
                </tfoot>
            </table>
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

    {{-- Convert to Low Status --}}
    <div class="modal fade" id="hotLeadModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <span>
                        Reason
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100 text-start">
                    @csrf
                    <div class="form-group">
                        <div id="reason">
                            <div class="classReason">
                                <input type="hidden" name="clientId" id="clientId">
                                <input type="hidden" name="initProg" id="initProg">
                                <input type="hidden" name="leadStatus" id="leadStatus">
                                <input type="hidden" name="leadStatusOld" id="leadStatusOld">
                                <input type="hidden" name="groupId" id="groupId">
                                <select name="reason_id" class="w-100" id="selectReason"
                                    onchange="otherOption($(this).val())">
                                    <option data-placeholder="true"></option>
                                    @foreach ($reasons as $reason)
                                        <option value="{{ $reason->reason_id }}"
                                            {{ old('reason_id') == $reason->reason_id ? 'selected' : '' }}>
                                            {{ $reason->reason_name }}
                                        </option>
                                    @endforeach
                                    <option value="other">
                                        Other option
                                    </option>
                                </select>
                                <div id="error-message">

                                </div>
                            </div>

                            <div class="d-flex align-items-center d-none" id="inputReason">
                                <input type="text" name="other_reason" class="form-control form-control-sm rounded"
                                    id="other_reason">
                                <div class="float-end cursor-pointer" onclick="resetOption()">
                                    <b>
                                        <i class="bi bi-x text-danger"></i>
                                    </b>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" href="#" class="btn btn-outline-danger btn-sm"
                            onclick="closeUpdateLead()">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</button>
                        <button type="button" onclick="updateHotLead()" class="btn btn-primary btn-sm">
                            <i class="bi bi-save2 me-1"></i>
                            Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="assignForm" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <span>
                        Assign
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100">
                    <form action="" method="POST" id="formAssign">
                        @csrf
                        <div class="put"></div>
                        <div class="row g-2">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        PIC <sup class="text-danger">*</sup>
                                    </label>
                                    <select name="pic_id" id="pic-id" class="modal-select w-100">
                                        <option data-placeholder="true"></option>

                                    </select>
                                    @error('pic_id')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="#" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                                <i class="bi bi-x-square me-1"></i>
                                Cancel</a>
                            <button type="button" id="btnSubmit" class="btn btn-primary btn-sm">
                                <i class="bi bi-save2 me-1"></i>
                                Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#selectReason').select2({
                dropdownParent: $('#hotLeadModal'),
                placeholder: "Select value",
                allowClear: true
            });
        });

        function otherOption(value) {
            if (value == 'other') {
                $('.classReason').addClass('d-none')
                $('#inputReason').removeClass('d-none')
                $('#inputReason input').focus()
            } else {
                $('#inputReason').addClass('d-none')
                $('.classReason').removeClass('d-none')
            }
        }

        function resetOption() {
            $('.classReason').removeClass('d-none')
            $('#selectReason').val(null).trigger('change')
            $('#inputReason').addClass('d-none')
            $('#inputReason input').val(null)
        }
    </script>

    <script>
        var widthView = $(window).width();
        $(document).ready(function() {
            $('.modal-select').select2({
                dropdownParent: $('#assignForm .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });

            var get_st = "{{ isset($_GET['st']) ? $_GET['st'] : '' }}"
            var button = [
                'pageLength', {
                    extend: 'excel',
                    text: 'Export to Excel',
                },
            ];

            // button for DataTable 
            if (get_st == 'new-leads' || get_st == 'potential') {
                button = [
                    'pageLength', {
                        extend: 'excel',
                        text: 'Export to Excel',
                    },
                    {
                        text: '<i class="bi bi-check-square me-1"></i> Select All',
                        action: function(e, dt, node, config) {
                            selectAll();
                        }
                    },
                    {
                        text: '<i class="bi bi-trash-fill me-1"></i> Delete',
                        action: function(e, dt, node, config) {
                            multipleDelete();
                        }
                    },
                ];
            }

            if (get_st == 'new-leads' && ('{{ $isSalesAdmin }}' || '{{ $isSuperAdmin }}') &&
                '{{ auth()->user()->email }}' != 'ericko.siswanto@all-inedu.com') {
                button = [
                    'pageLength', {
                        extend: 'excel',
                        text: 'Export to Excel',
                    },
                    {
                        text: '<i class="bi bi-check-square me-1"></i> Select All',
                        action: function(e, dt, node, config) {
                            selectAll();
                        }
                    },
                    {
                        text: '<i class="bi bi-trash-fill me-1"></i> Delete',
                        action: function(e, dt, node, config) {
                            multipleDelete();
                        }
                    },
                    {
                        text: '<i class="bi bi-person-fill me-1"></i> Assign',
                        action: function(e, dt, node, config) {
                            multipleAssign();
                        }
                    },
                ];
            }

            // unused
            var ex_button = {
                        extend: 'excelHtml5',
                        text: 'Export to Excel',
                        exportOptions: {
                            modifier: {
                                search: 'applied',
                                page: 'all'
                            },
                            columns: ':visible',
                            format: {
                                body: function ( data, row, column, node) {
                                    if (column == 0) 
                                        return no++

                                    return data
                                }
                            }
                        }
                    };

            var no = 1;
            var table = $('#clientTable').DataTable({
                order: [],
                dom: 'Bfrtip',
                buttons: [button],
                lengthMenu: [
                    [10, 50, 100, -1],
                    ['10 row', '50 row', '100 row', 'Show all']
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
                    data: function(params) {
                        params.school_name = $("#school-name").val()
                        params.graduation_year = $("#graduation-year").val()
                        params.lead_source = $("#lead-sources").val()
                        params.program_suggest = $("#program-name").val()
                        params.status_lead = $("#lead-source").val()
                        params.active_status = $("#active-status").val()
                        params.pic = $("#pic").val()
                        params.start_joined_date = $("#start_joined_date").val()
                        params.end_joined_date = $("#end_joined_date").val()
                    }
                },
                columns: [{
                        data: 'status_lead_score',
                        visible: false,
                    },
                    {
                        data: 'id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            if (get_st == 'new-leads' || get_st == 'potential')
                                return '<input type="checkbox" class="editor-active cursor-pointer" data-id="' +
                                    data + '">'
                            else
                                return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'full_name',
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return data
                        }
                    },
                    {
                        data: 'interest_prog',
                        searchable: false,
                        className: 'text-start',
                        defaultContent: '-',
                        render: function(data, type, row, meta) {
                            if (data == undefined && data == null) {
                                return '-'
                            } else {
                                var arrayInterest = data.split(',');
                                var arrayLength = arrayInterest.length > 1 ? (arrayInterest.length -
                                    1) + ' More' : ''

                                var interestProgram = ""

                                for (i = 0; i < arrayInterest.length; i++) {
                                    if (i != 0) {
                                        interestProgram += arrayInterest[i] + '</br>'
                                    }
                                }

                                var descProgram = arrayInterest.length > 1 ?
                                    '<div class="badge badge-primary py-1 px-2" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="right" data-bs-title="' +
                                    interestProgram + '">' + arrayLength +
                                    '</div>' : ''


                                return arrayInterest[0] + " " + descProgram
                            }
                        }
                    },
                    {
                        data: 'program_suggest',
                        defaultContent: '-'
                    },
                    {
                        data: 'status_lead',
                        searchable: false,
                        className: 'text-center',
                        defaultContent: '-',
                        render: function(data, type, row, meta) {
                            var warm = '';
                            var hot = '';
                            var cold = '';
                            switch (data) {
                                case 'Hot':
                                    hot = 'selected';
                                    break;

                                case 'Warm':
                                    warm = 'selected';
                                    break;

                                case 'Cold':
                                    cold = 'selected';
                                    break;
                            }
                            return data != null ?
                                '<select name="status_lead" style="color:#212b3d" class="select w-100 leads' +
                                row.id + '" id="status_lead"><option value="hot" ' +
                                hot + '>Hot</option><option value="warm" ' + warm +
                                '>Warm</option><option value="cold" ' + cold +
                                '>Cold</option></select>' : '-';
                        }

                    },
                    {
                        data: 'pic_name',
                        defaultContent: '-'
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
                        name: 'parent_name',
                        defaultContent: '-',
                        orderable: true,
                    },
                    {
                        data: 'parent_mail',
                        name: 'parent_mail',
                        defaultContent: '-'
                    },
                    {
                        data: 'parent_phone',
                        name: 'parent_phone',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'school_name',
                        name: 'school_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'graduation_year_real',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'grade_now',
                        defaultContent: '-',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return data > 12 ? 'Not high school' : data;
                        }
                    },
                    {
                        data: 'insta',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'state',
                        defaultContent: '-'
                    },
                    {
                        data: 'city',
                        defaultContent: '-'
                    },
                    {
                        data: 'address',
                        defaultContent: '-'
                    },
                    {
                        data: 'lead_source',
                        className: 'text-center',
                        defaultContent: '-',
                    },
                    {
                        data: 'referral_name',
                        name: 'referral_name',
                        className: 'text-center',
                        defaultContent: '-',
                        render: function(data, type, row, meta) {
                            if (row.lead_source == "Referral") {
                                return data;
                            } else {
                                return '-';
                            }
                        }
                    },
                    {
                        data: 'st_levelinterest',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'joined_event',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            if (data == undefined && data == null) {
                                return '-'
                            } else {
                                var arrayInterest = data.split(',');
                                var arrayLength = arrayInterest.length > 1 ? (arrayInterest.length -
                                    1) + ' More' : ''

                                var interestProgram = ""

                                for (i = 0; i < arrayInterest.length; i++) {
                                    if (i != 0) {
                                        interestProgram += arrayInterest[i] + '</br>'
                                    }
                                }

                                var descProgram = arrayInterest.length > 1 ?
                                    '<div class="badge badge-primary py-1 px-2" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="right" data-bs-title="' +
                                    interestProgram + '">' + arrayLength +
                                    '</div>' : ''


                                return arrayInterest[0] + " " + descProgram
                            }
                        }
                    },
                    {
                        data: 'st_abryear',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'abr_country',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            if (data == undefined && data == null) {
                                return '-'
                            } else {
                                var arrayInterest = data.split(',');
                                var arrayLength = arrayInterest.length > 1 ? (arrayInterest.length -
                                    1) + ' More' : ''

                                var interestProgram = ""

                                for (i = 0; i < arrayInterest.length; i++) {
                                    if (i != 0) {
                                        interestProgram += arrayInterest[i] + '</br>'
                                    }
                                }

                                var descProgram = arrayInterest.length > 1 ?
                                    '<div class="badge badge-primary py-1 px-2" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="right" data-bs-title="' +
                                    interestProgram + '">' + arrayLength +
                                    '</div>' : ''


                                return arrayInterest[0] + " " + descProgram
                            }
                        }
                    },
                    {
                        data: 'dream_uni',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'dream_major',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'dream_major',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'scholarship',
                        className: 'text-center',
                        searchable: false,
                        render: function(data, type, row, meta) {
                            if (data == "Y")
                                return "Yes"
                            else
                                return "No"
                        }
                    },
                    {
                        data: 'created_at',
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return moment(data).format('MMMM Do YYYY')
                        }
                    },
                    {
                        data: 'updated_at',
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return moment(data).format('MMMM Do YYYY')
                        }
                    },
                    {
                        data: 'st_statusact',
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            const status = data == 1 ? "checked" : "";
                            const content = '<div class="form-check form-switch m-0 p-0">' +
                                '<input class="form-check-input status" style="margin-left:2em" type="checkbox" role="switch" id="status-' +
                                row.id + '" ' + status + '>' +
                                '</div>'
                            return content;
                        }
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '',
                        render: function(data, type, row, meta) {
                            let content = '<div class="d-flex gap-1 justify-content-center">' +
                                '<small class="btn btn-sm btn-outline-warning cursor-pointer editClient" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="More Detail"><i class="bi bi-eye"></i></small>'
                            '</div>';

                            if (get_st == 'new-leads' || get_st == 'potential') {
                                content = '<div class="d-flex gap-1 justify-content-center">' +
                                    '<small data-bs-toggle="tooltip" data-bs-placement="top" ' +
                                    'data-bs-custom-class="custom-tooltip" ' +
                                    'data-bs-title="Delete" class="btn btn-sm btn-outline-danger cursor-pointer deleteClient">' +
                                    '<i class="bi bi-trash"></i>' +
                                    '</small>' +
                                    '<small class="btn btn-sm btn-outline-warning cursor-pointer editClient" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="More Detail"><i class="bi bi-eye"></i></small>'
                                '</div>';
                            }
                            return content;
                        }
                    },
                ],
                createdRow: function(row, data, index) {
                    let currentDate = new Date().toJSON().slice(0, 10);
                    if (moment(data['created_at']).format('YYYY-MM-DD') == currentDate) {
                        $('td', row).addClass('table-success');
                    }
                }
            });

            @php
                $privilage = $menus['Client']->where('submenu_name', 'Students')->first();
            @endphp


            @if ($privilage['copy'] == 0)
                document.oncontextmenu = new Function("return false");

                $('body').bind('cut copy paste', function(event) {
                    event.preventDefault();
                });
            @endif

            @if ($privilage['export'] == 0)
                table.button(1).disable();
            @endif

            // Tooltip 
            $('#clientTable tbody').on('mouseover', 'tr', function() {
                $('[data-bs-toggle="tooltip"]').tooltip({
                    trigger: 'hover',
                    html: true
                });
            });

            // Hold Student 
            $('#clientTable tbody').on('click', '.holdClient ', function() {
                var data = table.row($(this).parents('tr')).data();
            });

            // Delete Student 
            $('#clientTable tbody').on('click', '.deleteClient ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('client/student', data.id)
            });

            // View More 
            $('#clientTable tbody').on('click', '.editClient ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.open("{{ url('client/student') }}/" + data.id, "_blank")
            });

            // Change Active Status 
            $('#clientTable tbody').on('change', '.status ', function() {
                const data = table.row($(this).parents('tr')).data();
                const val = data.st_statusact == 1 ? 0 : 1;
                const link = "{{ url('/') }}/client/student/" + data.id + "/status/" + val

                axios.get(link)
                    .then(function(response) {
                        Swal.close()
                        notification("success", response.data.message)
                    })
                    .catch(function(error) {
                        Swal.close()
                        notification("error", error.response.data.message)
                    })
                table.ajax.reload(null, false)
            });

            // Change Lead Status 
            $('#clientTable tbody').on('change', '#status_lead', function() {
                var data = table.row($(this).parents('tr')).data();
                var lead_status = $(this).val();
                if (data.status_lead == 'Hot' || (data.status_lead == "Warm" && lead_status == "cold")) {
                    $('#hotLeadModal').modal('show');
                    $('#groupId').val(data.group_id);
                    $('#clientId').val(data.id);
                    $('#initProg').val(data.program_suggest);
                    $('#leadStatusOld').val(data.status_lead);
                    $('#leadStatus').val(lead_status);
                    $('#hotLeadForm').attr('action', '{{ url('client/student') }}/' + data.id +
                        '/lead_status/');
                } else {
                    confirmUpdateLeadStatus("{{ url('client/student') }}/" + data.id + "/lead_status", data
                        .id, data.program_suggest, data.group_id, data.status_lead, lead_status)
                }
            });

            /* for advanced filter */
            $("#school-name").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#graduation-year").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#lead-sources").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#program-name").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#lead-source").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#active-status").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#pic").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#start_joined_date").on('change', function (e) {
                var value = $(e.currentTarget).val();
                table.draw();
            })

            $("#end_joined_date").on('change', function (e) {
                var value = $(e.currentTarget).val();
                table.draw();
            })

            function selectAll() {
                const check_number = $('input.editor-active').length;
                const checked_number = $('input.editor-active:checked').length;
                const uncheck_number = check_number - checked_number;

                $('input.editor-active').each(function() {
                    if (uncheck_number == check_number) {
                        $(this).prop('checked', true)
                        table.button(2).text('<i class="bi bi-x me-1"></i> Unselect All')
                    } else if (checked_number == check_number) {
                        $(this).prop('checked', false)
                        table.button(2).text('<i class="bi bi-check-square me-1"></i> Select All')
                    } else {
                        $(this).prop('checked', true)
                        table.button(2).text('<i class="bi bi-x me-1"></i> Unselect All')
                    }
                });
            }

            function multipleDelete() {
                var selected = [];
                $('input.editor-active').each(function() {
                    if ($(this).prop('checked')) {
                        selected.push($(this).data('id'));
                    }
                });

                console.log(selected);

                if (selected.length > 0) {
                    Swal.fire({
                        title: "Confirmation!",
                        text: 'Are you sure to delete the students data?',
                        showCancelButton: true,
                        confirmButtonText: "Yes",
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            showLoading();
                            var link = '{{ route('client.raw.bulk.destroy') }}';
                            axios.post(link, {
                                    choosen: selected
                                })
                                .then(function(response) {
                                    swal.close();
                                    notification('success', response.data.message);
                                    table.ajax.reload(null, false)
                                })
                                .catch(function(error) {
                                    swal.close();
                                    notification('error', error.message);
                                })
                        }
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Please select the students data first!",
                    });
                }
            }

            function multipleAssign() {
                var selected = [];
                $('input.editor-active').each(function() {
                    if ($(this).prop('checked')) {
                        selected.push($(this).data('id'));
                    }
                });


                axios.get("{{ url('api/user/sales-team') }}")
                    .then(function(response) {
                        const data = response.data.data
                        $('#pic-id').html('')
                        $('#pic-id').append('<option value=""></option>')
                        data.forEach(element => {
                            const last_name = element.last_name == null ? '' : ' ' + element.last_name
                            const fullname = element.first_name + last_name
                            $('#pic-id').append(
                                '<option' +
                                ' value="' + element.id + '">' + fullname +
                                '</option>'
                            )
                        });
                    })
                    .catch(function(error) {
                        swal.close()
                        console.log(error);
                    })

                if (selected.length > 0) {
                    $('#assignForm').modal('show');

                }

            };

            $("#btnSubmit").click(function() {
                showLoading();
                var selected = [];
                $('input.editor-active').each(function() {
                    if ($(this).prop('checked')) {
                        selected.push($(this).data('id'));
                    }
                });
                var pic_id = $('#pic-id').val();

                var link = '{{ route('client.bulk.assign') }}';
                axios.post(link, {
                        choosen: selected,
                        pic_id: pic_id
                    })
                    .then(function(response) {
                        swal.close();
                        notification('success', response.data.message);
                        table.ajax.reload(null, false)
                        $('#assignForm').modal('hide');
                    })
                    .catch(function(error) {
                        swal.close();
                        notification('error', error.response.data.message);
                        $('#assignForm').modal('hide');
                    })
            });
        });

        function closeUpdateLead() {
            const id = $('#clientId').val();
            const old_status = $('#leadStatusOld').val().toLowerCase();
            $('.leads' + id).val(old_status)
            $('#hotLeadModal').modal('hide');
            $('#selectReason').val('').trigger('change');
            $('#other_reason').val('');
        }

        function updateHotLead() {
            var link = '{{ url('client/student') }}/' + $('#clientId').val() + '/lead_status';
            $('#hotLeadModal').modal('hide');
            Swal.showLoading()
            axios.post(link, {
                    groupId: $('#groupId').val(),
                    clientId: $('#clientId').val(),
                    initProg: $('#initProg').val(),
                    leadStatus: $('#leadStatus').val(),
                    reason_id: $('#selectReason').val(),
                    other_reason: $('#other_reason').val(),
                })
                .then(function(response) {
                    swal.close();

                    let obj = response.data;

                    $('#clientTable').DataTable().ajax.reload(null, false);

                    switch (obj.code) {
                        case 200:
                            notification('success', obj.message)

                            break;
                        case 400:
                            $('#hotLeadModal').modal('show');
                            if (obj.message['reason_id'] != undefined) {
                                $('#error-message').html('<small class="text-danger fw-light">' + obj.message[
                                    'reason_id'] + '</small>')
                            } else if (obj.message['leadStatus'] != undefined) {
                                $('#error-message').html('<small class="text-danger fw-light">' + obj.message[
                                    'leadStatus'] + '</small>')
                            }
                            break;

                        case 500:
                            notification('error', 'Something went wrong while update lead status')
                            break;
                    }
                })
                .catch(function(error) {
                    swal.close();
                    notification('error', error)
                })
        }
    </script>
@endpush
