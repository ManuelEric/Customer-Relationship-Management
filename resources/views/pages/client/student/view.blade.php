@extends('layout.main')
<style>
    .lcs_wrap {
        scale: 0.7;
        margin-top: -4px;
        margin-left: -10px;
    }
</style>

@section('title', 'Student')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Students</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Detail</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="card rounded mb-2">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="col">
                            <h3 class="m-0 mb-2 p-0">{{ $student->fullname }}</h3>
                            <small class="text-muted">
                                <i class="bi bi-calendar-day me-1"></i> Join Date:
                                {{ date('d M Y', strtotime($student->created_at)) }} |
                                <i class="bi bi-calendar-date mx-1"></i> Last Update:
                                {{ date('d M Y', strtotime($student->updated_at)) }}
                            </small>
                        </div>
                        <div class="col-2 text-end">
                            <a href="{{ url('client/student/' . $student->id . '/edit') }}"
                                class="btn btn-warning btn-sm rounded p-2"><i class="bi bi-pencil"></i></a>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                E-mail
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ $student->mail }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Phone Number
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ $student->phone }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Address
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {!! $student->address !!}
                            {!! $student->postal_code ? $student->postal_code . '<br>' : null !!}
                            {{ $student->city }} {{ $student->state }}
                        </div>
                    </div>
                    @if ($student->school != null)
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    School Name
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-9 col-8">
                                {{ $student->school->sch_name }}
                            </div>
                        </div>
                    @endif
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Grade
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ $viewStudent->grade_now <= 12 ? $viewStudent->grade_now : 'Not High School' }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Graduation Year
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ $student->graduation_year }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Follow-up Priority
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ $student->st_levelinterest }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Lead
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ $student->leadSource }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Is Funding
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            @switch($student->is_funding)
                                @case('1')
                                    Yes
                                @break

                                @case('0')
                                    No
                                @break

                                @case(null)
                                @break
                            @endswitch
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Register As
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            @switch($student->register_as)
                                @case('student')
                                    Student
                                @break

                                @case('parent')
                                    Parent
                                @break

                                @case(null)
                                @break
                            @endswitch
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Active Status
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            <input type="checkbox" name="st_status" id="status" value=""
                                @checked($student->st_statusact == 1)>
                            {{-- <select name="st_status" id="status">
                                <option value="1" {{ $student->st_statusact == 1 ? "selected" : null }}>Active</option>
                                <option value="0" {{ $student->st_statusact == 0 ? "selected" : null }}>Inactive</option>
                            </select> --}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card rounded mb-2">
                <div class="card-header">
                    <div class="">
                        <h5 class="m-0 p-0">Interest Program</h5>
                    </div>
                </div>
                <div class="card-body">
                    @forelse ($student->interestPrograms as $program)
                        <a href="{{ url('client/student/' . $student->id . '/program/create?p=' . $program->prog_id) }}"
                            class="btn btn-sm btn-outline-info
                        me-1 rounded-4 mb-2">
                            {{ $program->program_name }}</a>
                    @empty
                        There's no interest program yet
                    @endforelse
                </div>
            </div>


        </div>
        <div class="col-md-5">
            <div class="card rounded mb-2">
                <div class="card-header">
                    <div class="">
                        <h5 class="m-0 p-0">Interest Countries</h5>
                    </div>
                </div>
                <div class="card-body">
                    @forelse ($student->destinationCountries as $country)
                        <div class="badge badge-success me-1 mb-2">{{ $country->name }}</div>
                    @empty
                        There's no interest countries yet
                    @endforelse
                </div>
            </div>
            <div class="card rounded mb-2">
                <div class="card-header">
                    <div class="">
                        <h5 class="m-0 p-0">Dream University</h5>
                    </div>
                </div>
                <div class="card-body">
                    @forelse ($student->interestUniversities as $university)
                        <div class="badge badge-danger me-1 mb-2">{{ $university->univ_name }}</div>
                    @empty
                        There's no dream university
                    @endforelse
                </div>
            </div>
            <div class="card rounded mb-2">
                <div class="card-header">
                    <div class="">
                        <h5 class="m-0 p-0">Interest Major</h5>
                    </div>
                </div>
                <div class="card-body">
                    @forelse ($student->interestMajor as $major)
                        <div class="badge badge-primary me-1 mb-2">{{ $major->name }}</div>
                    @empty
                        There's no interest major yet
                    @endforelse
                </div>
            </div>
            <div class="card rounded mb-2">
                <div class="card-header">
                    <div class="">
                        <h5 class="m-0 p-0">Parents Information</h5>
                    </div>
                </div>
                <div class="card-body">
                    @forelse ($student->parents as $parent)
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Parents Name
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-9 col-8">
                                {{ $parent->fullname }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Parents Email
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-9 col-8">
                                {{ $parent->mail }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Parents Phone
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-9 col-8">
                                {{ $parent->phone }}
                            </div>
                        </div>

                    @empty
                        There's no parent information yet
                    @endforelse
                </div>
            </div>

            @if(isset($historyLeads) && $historyLeads->count() > 0)
                <div class="card rounded mb-2">
                    <div class="card-header">
                        <div class="">
                            <h5 class="m-0 p-0">Lead Status Tracking</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        @foreach ($historyLeads as $initprog => $historyLead)
                            <div class="row align-items-center border-b-2">
                                @php
                                    $currentLead = $historyLead->where('status', 1)->where('initprog', $initprog)->first();
                                    $oldLeads = $historyLead->where('status', 0)->where('initprog', $initprog);
                                @endphp
                                <div class="col-5">
                                    {{ $initprog }}
                                </div>
                                <div class="col-3 text-center d-flex align-items-center">
                                    <i class="{{ $currentLead['total_result_program'] >= 0.5 ? 'bi bi-check text-success' : 'bi bi-x text-danger' }}  fs-3"></i>
                                    <small class="text-muted">({{ $currentLead['total_result_program'] }}/1)</small>
                                </div>
                                <div class="col-3 text-center d-flex align-items-center">
                                    @if ($currentLead['lead_status'] == 'Hot')
                                        <i class="bi bi-fire text-danger fs-5 me-2"></i> {{ $currentLead['lead_status'] }}
                                        <small class="text-muted">({{$currentLead['total_result_lead']}}/1)</small>
                                    @elseif($currentLead['lead_status'] == 'Warm')
                                        <i class="bi bi-fire text-warning fs-5 me-2"></i> {{ $currentLead['lead_status'] }}
                                        <small class="text-muted">({{$currentLead['total_result_lead']}}/1)</small>
                                    @elseif($currentLead['lead_status'] == 'Cold')
                                        <i class="bi bi-snow3 text-info fs-5 me-2"></i> {{ $currentLead['lead_status'] }}
                                        <small class="text-muted">({{$currentLead['total_result_lead']}}/1)</small>
                                    @endif
                                </div>
                                <div class="col-1 text-end">
                                    <div class="dropdown">
                                        <i class="bi bi-info-circle cursor-pointer" title="History"
                                            data-bs-toggle="dropdown"></i>
                                        <div class="dropdown-menu">
                                            <div class="dropdown-header">
                                                History of Lead Status Tracking
                                            </div>
                                            <div class="px-3 overflow-auto" style="max-height: 150px">
                                                <table class="table table-hover table-striped" style="font-size: 10px">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Main Program</th>
                                                        <th>Status</th>
                                                        <th>Last Date</th>
                                                        <th>Reason</th>
                                                    </tr>
                                                    @forelse ($oldLeads->sortByDesc('updated_at') as $oldLead)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $initprog }}</td>
                                                            <td>{{ $oldLead['lead_status'] }}</td>
                                                            <td>{{ $oldLead['updated_at'] }}</td>
                                                            <td>{{ $oldLead['reason'] != null ? $oldLead['reason'] : '-' }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr class="text-center">
                                                            <td colspan="5">No data</td>
                                                        </tr>
                                                    @endforelse
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
        <div class="col-md-12">
            <div class="card rounded">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h5 class="m-0 p-0">Programs</h5>
                    </div>
                    <div class="">
                        <a href="{{ route('student.program.create', ['student' => $student->id]) }}"
                            class="btn btn-sm btn-primary">Add Program</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
                        <thead class="bg-secondary text-white">
                            <tr class="text-center" role="row">
                                <th class="bg-info text-white">No</th>
                                <th class="bg-info text-white">Program Name</th>
                                <th>Conversion Lead</th>
                                <th>First Discuss</th>
                                <th>PIC</th>
                                <th>Program Status</th>
                                <th>Running Status</th>
                                <th>#</th>
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
        <div class="col-md-12 mt-2">
            <div class="card rounded">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h5 class="m-0 py-2">Events</h5>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover nowrap align-middle w-100" id="eventTable">
                        <thead class="bg-secondary text-white">
                            <tr class="text-center" role="row">
                                <th class="bg-info text-white">No</th>
                                <th class="bg-info text-white">Event Name</th>
                                <th>Event Start Date</th>
                                <th>Joined Date</th>
                            </tr>
                        </thead>
                        <tfoot class="bg-light text-white">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/lc_switch.min.js') }}"></script>
    <script>
        lc_switch('input[type=checkbox]', {

            // ON text
            on_txt: 'ON',

            // OFF text
            off_txt: 'OFF',

            // Custom ON color. Supports gradients
            on_color: '#0083B8',

            // enable compact mode
            compact_mode: false

        });
    </script>
    {{-- Need Changing --}}
    <script>
        var url = "{{ url('client/student') . '/' . $student->id . '/program' }}"
        $(document).ready(function() {
            var table = $('#programTable').DataTable({
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
                    left: 0,
                    right: 0
                },
                processing: true,
                serverSide: true,
                ajax: '{{ url('api/client/' . $student->id . '/programs') }}',
                columns: [{
                        data: 'prog_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'program_name',
                    },
                    {
                        data: 'conversion_lead',
                        className: 'text-center',
                    },
                    {
                        data: 'first_discuss_date',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return data ? moment(data).format("MMMM Do YYYY") : '-'
                        }
                    },
                    {
                        data: 'pic_name',
                        className: 'text-center',
                    },
                    {
                        data: 'program_status',
                        className: 'text-center',
                    },
                    {
                        data: 'prog_running_status',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            switch (parseInt(data)) {
                                case 0:
                                    return "Not yet"
                                    break;

                                case 1:
                                    return "Ongoing"
                                    break;

                                case 2:
                                    return "Done"
                                    break;
                            }
                        }

                    },
                    {
                        data: 'clientprog_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return '<a href="' + url + '/' + data +
                                '" class="btn btn-sm btn-warning"><i class="bi bi-info-circle me-2"></i>More</a>'
                        }
                    }
                ]
            });

            $('#programTable tbody').on('click', '.editClient ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('asset') }}/" + data.asset_id.toLowerCase() + '/edit';
            });

            $('#programTable tbody').on('click', '.deleteClient ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('asset', data.asset_id)
            });

            var table_event = $('#eventTable').DataTable({
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
                    right: 0
                },
                processing: true,
                serverSide: true,
                ajax: '{{ url('api/client/' . $student->id . '/events') }}',
                columns: [{
                        data: 'clientevent_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'event_name',
                        name: 'tbl_events.event_title'
                    },
                    {
                        data: 'event_startdate',
                        name: 'tbl_events.event_startdate',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return moment(data).format('DD MMMM YYYY HH:mm:ss')
                        }
                    },
                    {
                        data: 'joined_date',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return moment(data).format('DD MMMM YYYY')
                        }
                    },
                ]
            });
        });
    </script>



    <script type="text/javascript">
        // $("#status").on('change', async function() {
        $('.lcs_switch').on('click', async function() {

            // Swal.fire({
            //     width: 100,
            //     backdrop: '#4e4e4e7d',
            //     allowOutsideClick: false,
            // })
            // swal.showLoading()

            // var val = $(this).val() // for select option
            var class_names = $(this).attr('class');
            var getLcsStatus = class_names.split(' ');
            var current_value = getLcsStatus[2];

            var val = current_value == "lcs_off" ? 0 : 1;

            var link = "{{ url('/') }}/client/student/{{ $student->id }}/status/" + val

            await axios.get(link)
                .then(function(response) {

                    Swal.close()
                    notification("success", response.data.message)
                })
                .catch(function(error) {
                    // handle error
                    Swal.close()
                    notification("error", error.response.data.message)
                })
        })
    </script>
@endsection
