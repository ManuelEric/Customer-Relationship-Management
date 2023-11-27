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
    </style>
@endpush

@section('content')

    <div class="card bg-secondary mb-1 p-2">
        <div class="row align-items-center justify-content-between g-3">
            <div class="col-md-6">
                <h5 class="text-white m-0">
                    <i class="bi bi-tag me-1"></i>
                    Raw
                </h5>
            </div>
            {{-- <div class="col-md-6">
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
                                            @foreach ($advanced_filter['schools'] as $school)
                                                <option value="{{ $school->sch_name }}">{{ $school->sch_name }}</option>
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
            </div> --}}
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


            <style>
                #clientTable tr td.danger {
                    background: rgb(255, 151, 151)
                }
            </style>
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="clientTable">
                <thead class="bg-secondary text-white">
                    <tr class="text-center" role="row">
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">No</th>
                        <th class="bg-info text-white">Name</th>
                        <th>Suggestion</th>
                        <th>Mail</th>
                        <th>Phone</th>
                        <th>register as</th>
                        <th>relation</th>
                        <th>school uuid</th>
                        <th>interest_countries</th>
                        <th>lead id</th>
                        <th>graduation_year</th>
                        <th class="bg-info text-white"># Action</th>
                    </tr>
                </thead>
                {{-- <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="12"></td>
                    </tr>
                </tfoot> --}}
            </table>
        </div>
    </div>


@endsection
@push('scripts')
    <script>
        // $('#cancel').click(function() {
        //     $(this).parents('.dropdown').find('button.dropdown-toggle').dropdown('toggle')
        // });


        var widthView = $(window).width();
        $(document).ready(function() {

            function format(d) {

                var listSuggest = '<table class="table table-striped table-hover">'
                listSuggest += '<tr>';
                listSuggest += '<th>Name</th>';
                listSuggest += '<th>Email</th>';
                listSuggest += '<th>Phone</th>';
                d.suggestion.forEach(function(item, index) {
                    listSuggest += '<tr><td>' + item.first_name + ' ' + item.last_name + '</td>'
                    listSuggest += '<td>' + item.mail + '</td>'
                    listSuggest += '<td>' + item.phone + '</td></tr>'
                });

                listSuggest += '</table>';
                return listSuggest;
            }

            var table = $('#clientTable').DataTable({
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
                        data: 'first_name',
                        render: function(data, type, row, meta) {
                            return data
                        }
                    },
                    {
                        data: 'suggestion',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return '<span class="badge badge-info">' +
                                data.length + '</span>';
                        }
                        // defaultContent: '-'
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
                        data: 'register_as',
                        defaultContent: '-',
                    },
                    {
                        data: 'relation',
                        defaultContent: '-'
                    },
                    {
                        data: 'school_uuid',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'interest_countries',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'lead_id',
                        defaultContent: '-',
                        className: 'text-center',
                    },
                    {
                        data: 'graduation_year',
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

            // Add event listener for opening and closing details
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




        });
    </script>
@endpush
