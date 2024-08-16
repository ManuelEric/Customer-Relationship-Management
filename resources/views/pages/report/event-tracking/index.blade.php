@extends('layout.main')

@section('title', 'Event Tracking - Bigdata Platform')

@section('content')
    @if (isset($choosen_event))
        <div class="row" id="event-selected">
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
                    <form method="GET" id="select-event">
                        {{-- @csrf --}}
                        <div class="mb-3">
                            <label>Event Name</label>
                            <select name="event_name" id="event-name" class="select w-100">
                                <option data-placeholder="true"></option>
                                @foreach ($events as $event)
                                    <option value="{{ $event->event_title }}">{{ $event->event_title }}</option>
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

            {{-- Existing Client  --}}
            @include('pages.report.event-tracking.component.existing-client')

            {{-- New Client  --}}
            @include('pages.report.event-tracking.component.new-client')

            {{-- Conversion Lead  --}}
            @include('pages.report.event-tracking.component.conversion-lead')

            {{-- Feeder Schools --}}
            @include('pages.report.event-tracking.component.feeder-school')
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
                                <th>Event Name</th>
                                <th>Audience</th>
                                {{-- <th>Parent Mail</th>
                                    <th>Parent Phone</th> --}}
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Child Name</th>
                                <th>Have you ever participated in ALL-in Event/program before</th>
                                <th>School Name</th>
                                <th>Grade</th>
                                <th>Graduation Year</th>
                                <th>Country of Study Abroad</th>
                                <th>Lead Source</th>
                                <th>Referral From</th>
                                <th>Notes</th>
                                <th>Number of Party</th>
                                <th>Attendance</th>
                                <th>Registration</th>
                                <th class="bg-info text-white">Joined At</th>
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
        </div>
    </div>

@endsection

@push('scripts')
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
        var widthView = $(window).width();
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
                left: (widthView < 768) ? 1 : 2,
                right: 1
            },
            processing: true,
            serverSide: true,
            // ajax: {
            //     url: '',
            //     data: function(params) {
            //         params.event_name = $("#event-name").val()
            //     }
            // },
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
                    data: 'event_name',
                    name: 'tbl_events.event_title'
                },
                {
                    data: 'register_as',
                    name: 'client.register_as',
                    render: function(data, type, row, meta) {
                        if (data != null){
                            return data.charAt(0).toUpperCase() + data.slice(1);
                        }else{
                            return '-';
                        }
                    }
                },
                // {
                //     data: 'parent_name',
                //     defaultContent: '-',
                // },
                {
                    data: 'client_mail',
                    name: 'client.mail',
                    defaultContent: '-',
                },
                {
                    data: 'client_phone',
                    name: 'client.phone',
                    defaultContent: '-',
                },
                {
                    data: 'child_name',
                    name: 'child_name',
                    defaultContent: '-',
                },
                {
                    data: 'participated',
                    searchable: true
                    //    defaultContent: '-'
                },
                {
                    data: 'school_name',
                    name: 'school_name',
                    defaultContent: '-',
                },
                {
                    data: 'grade_now',
                    name: 'grade_now',
                    defaultContent: '-',
                },
                {
                    data: 'graduation_year',
                    name: 'graduation_year',
                    defaultContent: '-',
                },
                {
                    data: 'abr_country',
                    name: 'client.abr_country',
                    defaultContent: '-',
                },
                {
                    data: 'conversion_lead',
                    // name: 'client.lead_source',
                    defaultContent: '-',
                },
                {
                    data: 'referral_from',
                    name: 'client_ref_code_view.full_name',
                    defaultContent: '-',
                    className: 'text-center',
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
                        return data == 1 ? "Attended" : "Join"
                    }
                },
                {
                    data: 'registration_type',
                    className: 'text-center',
                },
                {
                    data: 'joined_date',
                    defaultContent: '-',
                },
            ]
        });

        // realtimeData(table)

        $("#select-event").on("submit", function(e){
            var value = $('#event-name').find("option:selected").val();
            table.draw();
        })

    });
    // function ExportToExcel() {

    //     var workbook = XLSX.utils.book_new();
    //     var ws = XLSX.utils.table_to_sheet(document.getElementById("tbl_event"));
    //     XLSX.utils.book_append_sheet(workbook, ws, "Client Events");

    //     XLSX.writeFile(workbook, "report-event-tracking.xlsx");

    // }
</script>
@endpush
