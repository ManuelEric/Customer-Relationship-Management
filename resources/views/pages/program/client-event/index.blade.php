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
            <div class="col-md-5">
                <h5 class="text-white m-0">
                    <i class="bi bi-tag me-1"></i>
                    Client Event
                </h5>
            </div>
            <div class="col-md-7 d-flex align-items-center gap-2">
                <select class="select w-100" name="event_name" id="event-name">
                    <option data-placeholder="true"></option>
                    @foreach ($events as $event)
                        <option value="{{ $event->event_title }}">{{ $event->event_title }}</option>
                    @endforeach
                </select>

                <a href="{{ url('api/download/excel-template/client-event') }}"
                    class="btn btn-sm btn-light text-info btn-download text-nowrap"><i class="bi bi-download me-1"></i>
                    <span>Download
                        Templates</span></a>
                <a href="#" class="btn btn-sm btn-light text-info btn-import text-nowrap" data-bs-toggle="modal"
                    data-bs-target="#importData"><i class="bi bi-cloud-upload me-1"></i> <span>Import</span></a>
                <a href="{{ url('program/event/create') }}" class="btn btn-sm btn-info text-nowrap"><i
                        class="bi bi-plus-square me-1"></i>
                    Add Client Event </a>
            </div>
        </div>
    </div>

    {{-- @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif --}}

    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="eventTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Client Name</th>
                        <th>Event Name</th>
                        <th>Audience</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Child Name</th>
                        <th>Have you ever participated in ALL-in Event/program before</th>
                        <th>School Name</th>
                        <th>Graduation Year</th>
                        <th>Conversion Lead</th>
                        <th>Country of Study Abroad</th>
                        <th>Joined Date</th>
                        <th>Notes</th>
                        <th>Number of Party</th>
                        <th>Attendance</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="14"></td>
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
                            format: {
                                body: function(data, row, column, node) {
                                    var result = '';
                                    if (column === 1) {
                                        if (data.indexOf('New') != -1) {
                                            console.log('true')
                                            result = data.replace(
                                                '<span class="badge text-bg-primary" style="font-size:8px;">New</span>',
                                                '')
                                        } else {
                                            result = data.replace(
                                                '<span class="badge text-bg-success" style="font-size:8px";>Existing</span>',
                                                '')
                                        }
                                    } else {
                                        result = data;
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
                processing: true,
                serverSide: true,
                ajax: {
                    url: '',
                    data: function(params) {
                        params.event_name = $("#event-name").val()
                    }
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

                            // if (newClientEvent == true) {
                            return data + (existing == true ?
                                ' <span class="badge text-bg-primary" style="font-size:8px;">New</span>' :
                                ' <span class="badge text-bg-success" style="font-size:8px";>Existing</span>'
                            );
                            // } else {
                            //     return data;
                            // }
                        }
                    },
                    {
                        data: 'event_name',
                        name: 'tbl_events.event_title'
                    },
                    {
                        data: 'register_as',
                        name: 'client.register_as',
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
                    },
                    {
                        data: 'number_of_party',
                        className: 'text-center',
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return '<input type="number" class="form-control form-control-sm num-party w-50 m-auto" value="'+ data +'" />'
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

            $("#event-name").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })
        });
    </script>

@endsection
