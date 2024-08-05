@extends('layout.main')

@section('title', 'Client Event ')
@section('style')
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
@endsection

@section('content')

    <div class="card bg-secondary mb-1 p-2">
        <div class="row align-items-center justify-content-between">
            <div class="col-md-7 mb-3">
                <h5 class="text-white m-0">
                    <i class="bi bi-tag me-1"></i>
                    Client Event
                </h5>
            </div>
            <div class="col-md-5">
                <div class="row align-items-center g-2">
                    {{-- @if (Session::get('user_role') != 'Employee')
                    <div class="col-md-3 col-6">
                        <a href="{{ url('api/download/excel-template/client-event') }}"
                            class="btn btn-sm btn-light text-info btn-download text-nowrap w-100"><i
                                class="bi bi-download me-1"></i>
                            <span>
                                Template</span></a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="#" class="btn btn-sm btn-light text-info btn-import text-nowrap w-100"
                            data-bs-toggle="modal" data-bs-target="#importData"><i class="bi bi-cloud-upload me-1"></i>
                            <span>Import</span></a>
                    </div>
                    @endif --}}
                    {{-- <div @class([
                        'col-md-3',
                        'offset-6' => Session::get('user_role') == 'Employee',
                    ])> --}}
                    <div class="col-md-3 offset-6">
                        <div class="dropdown">
                            <button href="#" class="btn btn-sm btn-light text-dark dropdown-toggle w-100"
                                data-bs-toggle="dropdown" data-bs-auto-close="false" id="filter">
                                <i class="bi bi-funnel me-2"></i> Filter
                            </button>
                            <form action="" class="dropdown-menu dropdown-menu-end pt-0 shadow filter-clientprog"
                                style="width: 400px;" id="advanced-filter">
                                <div class="dropdown-header bg-info text-dark py-2 d-flex justify-content-between">
                                    Advanced Filter
                                    <i class="bi bi-search"></i>
                                </div>
                                <div class="row p-3">
                                    <div class="col-md-12 mb-2">
                                        <label>Event Name</label>
                                        <select class="select w-100" name="event_name" id="event-name">
                                            <option data-placeholder="true"></option>
                                            @foreach ($events as $event)
                                                <option value="{{ $event->event_title }}">{{ $event->event_title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label>Audience</label>
                                        <select class="select w-100" name="audience[]" id="audience" multiple>
                                            <option data-placeholder="true"></option>
                                            <option value="student">Student</option>
                                            <option value="parent">Parent</option>
                                            <option value="teacher/counsellor">Teacher / Counsellor</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label for="">School Name</label>
                                        <select name="school_name[]" class="select form-select form-select-sm w-100" multiple
                                            id="school-name">
                                            @foreach ($schools as $school)
                                                <option value="{{ $school->sch_name }}">
                                                    {{ $school->sch_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label>Graduation Year</label>
                                        <select class="select w-100" name="graduation_year[]" id="graduation-year" multiple>
                                            <option data-placeholder="true"></option>
                                            @for ($i = date('Y'); $i < date('Y') + 6; $i++)
                                                <option value="{{ $i }}">{{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label for="">Conversion Lead</label>
                                        <select name="conversion_lead[]" class="select form-select form-select-sm w-100" multiple
                                            id="conversion-lead">
                                            @foreach ($conversion_leads as $lead)
                                                <option value="{{ $lead->lead_id }}">
                                                    {{ $lead->conversion_lead }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label>Attendance</label>
                                        <select class="select w-100" name="attendance" id="attendance">
                                            <option data-placeholder="true"></option>
                                            <option value="1">Attend</option>
                                            <option value="0">Join</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label>Registration Type</label>
                                        <select class="select w-100" name="registration" id="registration">
                                            <option data-placeholder="true"></option>
                                            <option value="OTS">OTS</option>
                                            <option value="PR">Pre-Registration</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="row g-2">
                                            <div class="col-md-6 mb-2">
                                                <label>Start Date</label>
                                                <input type="date" name="start_date" id="start_date"
                                                    class="form-control form-control-sm rounded">
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label>End Date</label>
                                                <input type="date" name="end_date" id="end_date"
                                                    class="form-control form-control-sm rounded">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ url('program/event/create') }}" class="btn btn-sm btn-info text-nowrap w-100"><i
                                class="bi bi-plus-square me-1"></i>
                            Add</a>
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
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="eventTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Client Name</th>
                        <th>Event Name</th>
                        <th>Ticket No</th>
                        <th>Audience</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Child Name</th>
                        <th>Child Mail</th>
                        <th>Child Phone</th>
                        <th>Have you ever participated in ALL-in Event/program before</th>
                        <th>School Name</th>
                        <th>Graduation Year</th>
                        <th>Conversion Lead</th>
                        <th>Referral From</th>
                        <th>Country of Study Abroad</th>
                        <th>Joined Date</th>
                        <th>Notes</th>
                        <th>Number of Party</th>
                        <th>Attendance</th>
                        <th>Registration</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="19"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="modal fade" id="importData" tabindex="-1" aria-labelledby="importDataLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('program.event.import') }}" method="POST" enctype="multipart/form-data">
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
                                <input type="file" name="file" id="" class="form-control form-control-sm">
                            </div>
                            <small class="text-warning mt-3">
                                * Please clean the file first, before importing the csv file. <br>
                                You can download the csv template <a
                                    href="{{ url('api/download/excel-template/client-event') }}">here</a>
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

    <script>
        $(document).ready(function() {

            var table = $('#eventTable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
                ],
                buttons: [
                    'pageLength', {
                        extend: 'excel',
                        text: 'Export to Excel',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
                            format: {
                                body: function(data, row, column, node) {

                                    var result = '';

                                    switch (column) {

                                        case 1:
                                            if (data.indexOf('New') != -1) {
                                                removed_span = data.replace(
                                                    '<span class="badge text-bg-primary" style="font-size:8px;">New</span>',
                                                    '')
                                            } else {
                                                removed_span = data.replace(
                                                    '<span class="badge text-bg-success" style="font-size:8px";>Existing</span>',
                                                    '')
                                            }
                                            result = column >= 7 && column <= 9 ? removed_span
                                                .replace(/[$,.]/g, '') : removed_span.replace(
                                                    /(&nbsp;|<([^>]+)>)/ig, "");
                                            break;

                                        case 18:
                                            return $(data).is("input") ? $(data).val() : data;
                                            break;

                                        case 19:
                                            return $(data).is("input") && $(data).attr('checked') ?
                                                "✓" : "-";
                                            break;

                                        default:
                                            return data;
                                    }

                                    return result;
                                }
                            }
                        }
                    },

                ],
                scrollX: true,
                fixedColumns: {
                    left: window.matchMedia('(max-width: 767px)').matches ? 0 : 2,
                    right: 1
                },
                search: {
                    return: true
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: '',
                    data: function(params) {
                        params.event_name = $("#event-name").val()
                        params.audience = $("#audience").val()
                        params.school_name = $("#school-name").val()
                        params.graduation_year = $("#graduation-year").val()
                        params.conversion_lead = $("#conversion-lead").val()
                        params.attendance = $("#attendance").val()
                        params.registration = $("#registration").val()
                        params.start_date = $('#start_date').val()
                        params.end_date = $('#end_date').val()
                    },
                },
                pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
                columns: [{
                        data: 'clientevent_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'client_name',
                        name: 'client.full_name',
                        render: function(data, type, row, meta) {

                            var existing = moment(row.created_at).format('MMMM Do YYYY, h:mm') ==
                                moment(row.client_created_at).format('MMMM Do YYYY, h:mm');

                            var newClientEvent = moment().format("MMM Do YY") == moment(row
                                .created_at).format('MMM Do YY');

                            var URL = '#';
                            if (row.register_as != null){
                                var clientRole = (row.register_as).toLowerCase();
                                var intoURLParam = clientRole.replace("/", "-");
                                var intoURLParam = intoURLParam.replace('counsellor', 'counselor');
                                URL = "{{ url('/') }}/client/" + intoURLParam + "/" + row
                                    .client_id;
                            } 

                            return "<a class='text-dark text-decoration-none' href='" + URL + "'>" +
                                data + "</a>" + (existing == true ?
                                    ' <span class="badge text-bg-primary" style="font-size:8px;">New</span>' :
                                    ' <span class="badge text-bg-success" style="font-size:8px";>Existing</span>'
                                );
                        }
                    },
                    {
                        data: 'event_name',
                        name: 'tbl_events.event_title'
                    },
                    {
                        data: 'ticket_id',
                        name: 'tbl_client_event.ticket_id',
                        className: 'text-center'
                    },
                    {
                        data: 'register_as',
                        name: 'client.register_as',
                        render: function(data, type, row, meta) {
                            if (data != null){
                                return data?.charAt(0).toUpperCase() + data?.slice(1);
                            }else{
                                return '-';
                            }
                        }
                    },
                    {
                        data: 'client_mail',
                        name: 'client.mail',
                        defaultContent: '-'
                    },
                    {
                        data: 'client_phone',
                        name: 'client.phone',
                        defaultContent: '-'
                    },
                    {
                        data: 'child_name',
                        name: 'child.full_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'child_mail',
                        name: 'child.mail',
                        defaultContent: '-'
                    },
                    {
                        data: 'child_phone',
                        name: 'child.phone',
                        defaultContent: '-'
                    },
                    // {
                    //     data: 'parent_mail',
                    //     name: 'parent.mail',
                    //     defaultContent: '-'
                    // },
                    // {
                    //     data: 'parent_phone',
                    //     name: 'parent.phone',
                    //     defaultContent: '-'
                    // },
                    {
                        data: 'participated',
                        searchable: true
                        //    defaultContent: '-'
                    },
                    {
                        data: 'school_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'graduation_year',
                        defaultContent: '-'
                    },
                    {
                        data: 'conversion_lead',
                        name: 'conversion_lead',
                        className: 'text-center'
                        // name: 'tbl_lead.main_lead'
                    },
                    {
                        data: 'referral_from',
                        name: 'client_ref_code_view.full_name',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'abr_country',
                        name: 'client.abr_country',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        className: 'text-center',
                        data: 'joined_date',
                        render: function(data, type, row, meta) {

                            return moment(data).format('dddd, DD MMM YYYY');
                        }
                    },
                    {
                        className: 'text-center',
                        data: 'notes',
                        searchable: true,
                        defaultContent: '-'
                    },
                    {
                        data: 'number_of_party',
                        className: 'text-center',
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return '<input type="number" class="form-control form-control-sm num-party w-50 m-auto" value="' +
                                data + '" />'
                        }
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            switch (parseInt(row.status)) {
                                case 0:
                                    return '<input class="form-check-input attendance" value="1" type="checkbox">'
                                    break;

                                case 1:
                                    return '<input class="form-check-input attendance" value="0" type="checkbox" checked>'
                                    break;

                            }
                        }
                    },
                    {
                        data: 'registration_type',
                        className: 'text-center',
                    },
                    // {
                    //     data: 'parent_phone',
                    //     name: 'parent.phone',
                    //     defaultContent: '-'
                    // },
                    // {
                    //     data: 'mail',
                    //     name: 'client.mail',
                    //     defaultContent: '-'
                    // },
                    // {
                    //     data: 'phone',
                    //     name: 'client.phone',
                    //     defaultContent: '-'
                    // },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button"class="btn btn-sm btn-outline-warning detailEvent"><i class="bi bi-eye"></i></button>'
                    }
                ],
                createdRow: function(row, data, index) {
                    let currentDate = new Date().toJSON().slice(0, 10);
                    if (data['created_at'].slice(0, 10) == currentDate) {
                        $('td', row).addClass('table-success');
                    }
                }
            });

            // realtimeData(table)

            @php
                $privilage = $menus['Program']->where('submenu_name', 'Client Event')->first();
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

            $('#eventTable tbody').on('click', '.detailEvent ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('program/event') }}/" + data.clientevent_id;
            });

            $('#eventTable tbody').on('change', '.attendance ', function() {
                var data = table.row($(this).parents('tr')).data();
                var clientEventId = data.clientevent_id
                var status = this.value;

                var url = "{{ url('api/event/attendance/') }}" + '/' + clientEventId + '/' + status
                axios.get(url)
                    .then(function(response) {
                        const attend = response.data
                        if (attend.status == 1) {
                            notification('success', attend.name + ' attended this event.')
                        } else {
                            notification('success', attend.name + ' canceled attending this event.')
                        }
                    }).catch(function(error) {
                        notification('error', 'Ooops! Something went wrong. Please try again.')
                    })

                // merubah value status 
                this.value = status == 1 ? 0 : 1

            });

            $("#eventTable tbody").on('change', '.num-party', function() {

                var data = table.row($(this).parents('tr')).data();
                var clientEventId = data.clientevent_id;
                var number_of_party = this.value;

                var url = "{{ url('api/event/party') }}/" + clientEventId + "/" + number_of_party;
                axios.get(url)
                    .then(function(response) {
                        notification('success', response.data.message);
                    }).catch(function(error) {
                        console.log(error)
                        notification('error', 'Ooops! Something went wrong. Please try again.')
                    });

            });

            $("#audience").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#event-name").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#school-name").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#graduation-year").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#conversion-lead").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#attendance").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#registration").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#start_date").on('change', function(e) {
                var val = $(e.currentTarget).val()
                var value = moment(val).format('YYYY_MM_DD')
                table.draw();
            })

            $("#end_date").on('change', function(e) {
                var val = $(e.currentTarget).val()
                var value = moment(val).format('YYYY_MM_DD')
                table.draw();
            })

        });
    </script>

@endsection
