@extends('layout.main')
<style>
    .lcs_wrap { scale: 0.7; margin-top: -4px; margin-left: -10px; }
</style>

@section('title', 'Student - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('client/student?st=potential') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Student
        </a>
    </div>


    <div class="row">
        <div class="col-md-7">
            <div class="card rounded mb-2">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="">
                            <h3 class="m-0 p-0">{{ $student->fullname }}</h3>
                            <small class="text-muted">
                                <i class="bi bi-calendar-day me-1"></i> Join Date: {{ date('d M Y', strtotime($student->created_at)) }} |
                                <i class="bi bi-calendar-date mx-1"></i> Last Update: {{ date('d M Y', strtotime($student->updated_at)) }}
                            </small>
                        </div>
                        <div class="">
                            <a href="{{ url('client/student/'.$student->id.'/edit') }}" class="btn btn-warning btn-sm rounded"><i
                                    class="bi bi-pencil"></i></a>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2 g-1">
                        <div class="col-md-3 d-flex justify-content-between">
                            <label>
                                E-mail
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9">
                            {{ $student->mail }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col-md-3 d-flex justify-content-between">
                            <label>
                                Phone Number
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9">
                            {{ $student->phone }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col-md-3 d-flex justify-content-between">
                            <label>
                                Address
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9">
                            {!! $student->address !!} 
                            {!! $student->postal_code ? $student->postal_code."<br>" : null !!} 
                            {{ $student->city }} {{ $student->state }}
                        </div>
                    </div>
                    @if ($student->school != NULL)
                    <div class="row mb-2 g-1">
                        <div class="col-md-3 d-flex justify-content-between">
                            <label>
                                School Name
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9">
                            {{ $student->school->sch_name }}
                        </div>
                    </div>
                    @endif
                    <div class="row mb-2 g-1">
                        <div class="col-md-3 d-flex justify-content-between">
                            <label>
                                Graduation Year
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9">
                            {{ $student->graduation_year }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col-md-3 d-flex justify-content-between">
                            <label>
                                Follow-up Priority
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9">
                            {{ $student->st_levelinterest }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col-md-3 d-flex justify-content-between">
                            <label>
                                Lead
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9">
                            {{ $student->leadSource }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col-md-3 d-flex justify-content-between">
                            <label>
                                Active Status
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9">
                            <input type="checkbox" name="st_status" id="status" value="" @checked($student->st_statusact == 1)>
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
                        <a href="{{ url('client/student/'.$student->id.'/program/create?p='.$program->prog_id) }}"
                        class="btn btn-sm btn-outline-info
                        me-1 rounded-4">
                        {{ $program->prog_program }}</a>
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
                        <div class="badge badge-success me-1">{{ $country->name }}</div>
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
                        <div class="badge badge-danger me-1">{{ $university->univ_name }}</div>
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
                        <div class="badge badge-primary me-1">{{ $major->name }}</div>
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
                            <div class="col-md-3 d-flex justify-content-between">
                                <label>
                                    Parents Name
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-9">
                                {{ $parent->fullname }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col-md-3 d-flex justify-content-between">
                                <label>
                                    Parents Email
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-9">
                                {{ $parent->mail }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col-md-3 d-flex justify-content-between">
                                <label>
                                    Parents Phone
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-9">
                                {{ $parent->phone }}
                            </div>
                        </div>

                        @empty
                            There's no parent information yet
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card rounded">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h5 class="m-0 p-0">Programs</h5>
                    </div>
                    <div class="">
                        <a href="{{ route('student.program.create', ['student' => $student->id]) }}" class="btn btn-sm btn-primary">Add Program</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
                        <thead class="bg-dark text-white">
                            <tr class="text-center" role="row">
                                <th class="text-dark">No</th>
                                <th class="bg-info text-white">Program Name</th>
                                <th>Conversion Lead</th>
                                <th>Last Discuss</th>
                                <th>PIC</th>
                                <th>Program Status</th>
                                <th class="text-dark">Running Status</th>
                                <th class="text-dark">#</th>
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
                        <thead class="bg-dark text-white">
                            <tr class="text-center" role="row">
                                <th class="text-dark">No</th>
                                <th class="bg-info text-white">Event Name</th>
                                <th class="text-dark">Event Start Date</th>
                                <th class="text-dark">Joined Date</th>
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
        var url = "{{ url('client/student').'/'.$student->id.'/program' }}"
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
                    left: 2,
                    right: 2
                },
                processing: true,
                serverSide: true,
                ajax: '{{ url('api/client/'.$student->id.'/programs') }}',
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
                    },
                    {
                        data: 'last_discuss_date',
                    },
                    {
                        data: 'pic_name',
                    },
                    {
                        data: 'program_status',
                    },
                    {
                        data: 'prog_running_status',
                        render: function(data, type, row, meta) {
                            switch(data) {
                                case 0:
                                    return "not yet"
                                    break;

                                case 1:
                                    return "ongoing"
                                    break;

                                case 2:
                                    return "done"
                                    break;
                            }
                        }
                        
                    },
                    {
                        data: 'clientprog_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return '<a href="' + url + '/' + data +'" class="btn btn-sm btn-warning"><i class="bi bi-info-circle me-2"></i>More</a>'
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
                    right: 2
                },
                processing: true,
                serverSide: true,
                ajax: '{{ url('api/client/'.$student->id.'/events') }}',
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
                        render: function(data, type, row) {
                            return moment(data).format('DD MMMM YYYY HH:mm:ss')
                        }
                    },
                    {
                        data: 'joined_date',
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