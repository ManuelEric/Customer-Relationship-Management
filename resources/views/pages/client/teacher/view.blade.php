@extends('layout.main')
<style>
    .lcs_wrap { scale: 0.7; margin-top: -4px; margin-left: -10px; }
</style>
@section('title', 'Teacher - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ route('teacher-counselor.index') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Teacher/Counselor
        </a>
    </div>


    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="card rounded mb-2">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="">
                            <h3 class="m-0 p-0">{{ $teacher_counselor->fullname }}</h3>
                            <small class="text-muted">
                                <i class="bi bi-calendar-day me-1"></i> Join Date: {{ date('d M Y', strtotime($teacher_counselor->created_at)) }} |
                                <i class="bi bi-calendar-date mx-1"></i> Last Update: {{ date('d M Y', strtotime($teacher_counselor->updated_at)) }}
                            </small>
                        </div>
                        <div class="">
                            <a href="{{ route('teacher-counselor.edit', ['teacher_counselor' => $teacher_counselor->id]) }}" class="btn btn-warning btn-sm rounded"><i
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
                            {{ $teacher_counselor->mail }}
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
                            {{ $teacher_counselor->phone }}
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
                            {!! $teacher_counselor->address !!} 
                            {!! $teacher_counselor->postal_code ? $teacher_counselor->postal_code."<br>" : null !!} 
                            {{ $teacher_counselor->city }} {{ $teacher_counselor->state }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col-md-3 d-flex justify-content-between">
                            <label>
                                Date of Birth
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9">
                            {{ date('d M Y', strtotime($teacher_counselor->dob)) }}
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
                            {{ $teacher_counselor->leadSource }}
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
                            <input type="checkbox" name="st_status" id="status" value="" @checked($teacher_counselor->st_statusact == 1)>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-center">
            <div class="mb-2">
                <img src="{{ asset('img/teacher.jpg') }}" alt="" class="w-50 rounded-circle">
            </div>
        </div>

        <div class="col-md-12">
            <div class="card rounded">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h5 class="m-0 p-0">Events</h5>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover nowrap align-middle w-100" id="eventTable">
                        <thead class="bg-dark text-white">
                            <tr class="text-center" role="row">
                                <th class="text-white">No</th>
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

    <script>
        $(document).ready(function(){
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
                    }
                ],
                scrollX: true,
                fixedColumns: {
                    left: 2,
                    right: 2
                },
                processing: true,
                serverSide: true,
                ajax: '',
                columns: [{
                        data: 'clientevent_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'event_name',
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
        });
    </script>
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

    <script>
        // Select2 Modal 
        $(document).ready(function() {
            $('.modal-select').select2({
                dropdownParent: $('#programForm .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });
        });
    </script>

    <script type="text/javascript">
        $('.lcs_switch').on('click', async function() {
            
            var class_names = $(this).attr('class');
            var getLcsStatus = class_names.split(' ');
            var current_value = getLcsStatus[2];
            
            var val = current_value == "lcs_off" ? 0 : 1;

            var link = "{{ url('/') }}/client/teacher-counselor/{{ $teacher_counselor->id }}/status/" + val

            await axios.get(link)
                .then(function(response) {
                    console.log(response)
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
