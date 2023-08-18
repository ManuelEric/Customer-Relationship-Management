@extends('layout.main')

@section('title', 'Event Tracking - Bigdata Platform')

@section('content')
    @if (isset($choosen_event))
        <div class="row">
            <div class="col">
                <div class="alert alert-success">
                    Event Tracking of <u>{{ $choosen_event->event_title }}</u>
                </div>
            </div>
        </div>
    @endif

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
                    <h6 class="p-0 m-0">Existing Client</h6>
                    {{-- <span class="badge bg-primary">{{ count($clientEvents) }}</span> --}}
                </div>
                <div class="card-body p-2 overflow-auto" style="max-height: 150px ">
                    <ul class="list-group">
                        <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center"
                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                            title="
                                        @foreach ($existingMentee as $existMentee)
                                            <p>
                                                <b>{{ $existMentee->client->full_name }}</b> From {{ $existMentee->client->school->sch_name ?? '-' }}
                                            </p> @endforeach
                                        ">
                            <div class="">Mentee</div>
                            <span class="badge badge-info">{{ $existingMentee->count() }}</span>
                        </li>
                        <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center"
                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                            title="
                                        @foreach ($existingNonMentee as $existNonMentee)
                                            <p>
                                                <b>{{ $existNonMentee->client->full_name }}</b> From {{ $existNonMentee->client->school->sch_name ?? '-' }}
                                            </p> @endforeach
                                        ">
                            <div class="">Non Mentee</div>
                            <span class="badge badge-info">{{ $existingNonMentee->count() }}</span>
                        </li>
                        <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center"
                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                            title="
                                        @foreach ($existingNonClient as $existNonClient)
                                            <p>
                                                <b>{{ $existNonClient->client->full_name }}</b> From {{ $existNonClient->client->school->sch_name ?? '-'}}
                                            </p> @endforeach">
                            <div class="">Non Client</div>
                            <span class="badge badge-info">{{ $existingNonClient->count() }}</span>
                        </li>

                    </ul>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header  d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">New Client</h6>
                    {{-- <span class="badge bg-primary">{{ count($clientEvents) }}</span> --}}
                </div>
                <div class="card-body p-2 overflow-auto" style="max-height: 150px ">
                    <ul class="list-group">
                        <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center"
                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                            title="
                                        @foreach ($newClient->where('register_as', 'student') as $newStudent)
                                            <p>
                                                <b>{{ $newStudent->client->full_name }}</b> From {{ $newStudent->client->school->sch_name ?? '' }}
                                            </p> @endforeach">
                            <div class="">Student</div>
                            <span
                                class="badge badge-info">{{ $newClient->where('register_as', 'student')->count() }}</span>
                        </li>
                        <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center"
                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                            title="
                                        @foreach ($newClient->where('register_as', 'parent') as $newParent)
                                            <p>
                                                <b>{{ $newParent->client->full_name }}</b> 
                                            </p> @endforeach">
                            <div class="">Parent</div>
                            <span class="badge badge-info">{{ $newClient->where('register_as', 'parent')->count() }}</span>
                        </li>
                        <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center"
                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                            title="
                                        @foreach ($newClient->where('register_as', 'teacher/counselor') as $newTeacher)
                                            <p>
                                                <b>{{ $newTeacher->client->full_name }}</b> From {{ $newTeacher->client->school->sch_name ?? '' }}
                                            </p> @endforeach">
                            <div class="">Teacher / Counselor</div>
                            <span
                                class="badge badge-info">{{ $newClient->where('register_as', 'teacher/counselor')->count() }}</span>
                        </li>

                    </ul>
                </div>
            </div>

            {{-- Conversion Lead  --}}
            <div class="card mb-3">
                <div class="card-header  d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Lead Source</h6>
                </div>
                <div class="card-body p-2 overflow-auto" style="max-height: 150px ">
                    <ul class="list-group">
                        @forelse ($conversionLeads as $conversionLead)
                            <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                                <div class="">{{ $conversionLead->conversion_lead }}</div>
                                <span class="badge badge-warning">{{ $conversionLead->count_conversionLead }}</span>
                            </li>
                        @empty
                            <li class="text-center">Not lead source yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Feeder Schools --}}
            <div class="card mb-3">
                <div class="card-header  d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Feeder Schools</h6>
                </div>
                <div class="card-body p-2 overflow-auto" style="max-height: 150px ">
                    <ul class="list-group">
                        @if ($feeder !== null)
                            @foreach ($feeder as $key => $val)
                                <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                                    <div class="">{{ $key }}</div>
                                    <span class="badge badge-warning">{{ $val }}</span>
                                </li>
                            @endforeach
                        @else
                            <li class="text-center">There's no feeder schools</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Client Event</h6>
                    {{-- <div class="">
                        <button onclick="ExportToExcel()" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark-excel me-1"></i> Print
                        </button>
                    </div> --}}
                </div>
                <div class="card-body overflow-auto" style="max-height: 500px">
                    {{-- <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100 table2excel" id="tbl_event">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    @if (isset($clientEvents->first()->filter) && $clientEvents->first()->filter == 'ByMonth')                                       
                                        <th>Event Name</th>
                                    @endif
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
                                        @if ($clientEvent->filter == 'ByMonth')                                       
                                            <td>{{ $clientEvent->event_title }}</td>
                                        @endif
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
                    </div> --}}
                    <table class="table table-bordered table-hover nowrap align-middle w-100" id="eventTrackTable">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th class="bg-info text-white">Event ID</th>
                                <th class="bg-info text-white">Client Name</th>
                                <th>Parent Name</th>
                                <th>Parent Mail</th>
                                <th>Parent Phone</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>School Name</th>
                                <th>Grade</th>
                                <th>Graduation Year</th>
                                <th>Lead Source</th>
                                <th class="bg-info text-white">Joined At</th>
                            </tr>
                        </thead>

                        <tfoot class="bg-light text-white">
                            <tr>
                                <td colspan="8"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <script>
        @php
            $privilage = $menus['Report']->where('submenu_name', 'Event Tracking')->first();
        @endphp
        $(document).ready(function() {
            @if ($privilage['copy'] == 0)
                document.oncontextmenu = new Function("return false");

                $('body').bind('cut copy paste', function(event) {
                    event.preventDefault();
                });
            @endif

            $('[data-toggle="tooltip"]').tooltip();

        });

        $(document).ready(function() {
            // $('#cancel').click(function() {
            //     $(this).parents('.dropdown').find('button.dropdown-toggle').dropdown('toggle')
            // });

            var i = 1;

            var table = $('#eventTrackTable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
                ],
                buttons: [
                    'pageLength', {
                        extend: 'excel',
                        text: 'Export to Excel',
                    }
                ],
                scrollX: true,
                fixedColumns: {
                    left: 2,
                    right: 1
                },
                processing: true,
                serverSide: true,
                ajax: '',
                columns: [{
                        data: 'event_id',
                        defaultContent: '-',
                    },
                    {
                        data: 'client_name',
                        name: 'client.full_name',
                        defaultContent: '-',
                    },
                    {
                        data: 'parent_name',
                        defaultContent: '-',
                    },
                    {
                        data: 'parent_mail',
                        name: 'parent.mail',
                        defaultContent: '-',
                    },
                    {
                        data: 'parent_phone',
                        name: 'parent.phone',
                        defaultContent: '-',
                    },
                    {
                        data: 'mail',
                        name: 'client.mail',
                        defaultContent: '-',
                    },
                    {
                        data: 'phone',
                        name: 'client.phone',
                        defaultContent: '-',
                    },
                    {
                        data: 'school_name',
                        name: 'client.school_name',
                        defaultContent: '-',
                    },
                    {
                        data: 'grade_now',
                        name: 'client.grade_now',
                        defaultContent: '-',
                    },
                    {
                        data: 'graduation_year_real',
                        name: 'client.graduation_year_real',
                        defaultContent: '-',
                    },
                    {
                        data: 'conversion_lead',
                        // name: 'client.lead_source',
                        defaultContent: '-',
                    },
                    {
                        data: 'joined_date',
                        defaultContent: '-',
                    },
                ]
            });
            
            // realtimeData(table)

        });
        // function ExportToExcel() {

        //     var workbook = XLSX.utils.book_new();
        //     var ws = XLSX.utils.table_to_sheet(document.getElementById("tbl_event"));
        //     XLSX.utils.book_append_sheet(workbook, ws, "Client Events");

        //     XLSX.writeFile(workbook, "report-event-tracking.xlsx");

        // }
    </script>
@endsection
