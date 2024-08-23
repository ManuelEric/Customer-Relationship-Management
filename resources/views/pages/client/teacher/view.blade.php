@extends('layout.main')
<style>
    .lcs_wrap { scale: 0.7; margin-top: -4px; margin-left: -10px; }
</style>
@section('title', 'Teachers Detail')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Teacher/Counselor</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Detail</li>
@endsection
@section('content')
    <div class="row g-2">
        <div class="col-md-8 order-md-1 order-2">
            <div class="card rounded mb-2">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="col">
                            <h3 class="m-0 mb-2 p-0">{{ $teacher_counselor->full_name }}</h3>
                            <small class="text-muted">
                                <i class="bi bi-calendar-day me-1"></i> Join Date: {{ date('d M Y', strtotime($teacher_counselor->created_at)) }} |
                                <i class="bi bi-calendar-date mx-1"></i> Last Update: {{ date('d M Y', strtotime($teacher_counselor->updated_at)) }}
                            </small>
                        </div>
                        <div class="col-2 text-end">
                            <a href="{{ route('teacher-counselor.edit', ['teacher_counselor' => $teacher_counselor->id]) }}" class="btn btn-warning btn-sm rounded p-2"><i class="bi bi-pencil"></i></a>
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
                            {{ $teacher_counselor->mail }}
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
                            {{ $teacher_counselor->phone }}
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
                            {!! $teacher_counselor->address !!} 
                            {!! $teacher_counselor->postal_code ? $teacher_counselor->postal_code."<br>" : null !!} 
                            {{ $teacher_counselor->city }} {{ $teacher_counselor->state }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Date of Birth
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ isset($teacher_counselor->dob) ? date('d M Y', strtotime($teacher_counselor->dob)) : null }}
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
                            {{ $teacher_counselor->lead_source }} {{ $teacher_counselor->referral_code != null && $teacher_counselor->lead_source == "Referral" ? '(' . $teacher_counselor->referral_name . ')' : null }}
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
                            <input type="checkbox" name="st_status" id="status" value="" @checked($teacher_counselor->st_statusact == 1)>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 order-md-2 order-1 text-center">
            <div class="card card-body d-flex justify-content-center align-items-center">
                <img loading="lazy"  src="{{ asset('img/teacher.webp') }}" alt="" class="w-25">
            </div>
        </div>
        <div class="col-md-12 order-3">
            <div class="card rounded">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h5 class="m-0 p-0">Events</h5>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover nowrap align-middle w-100" id="eventTable">
                        <thead class="bg-secondary text-white">
                            <tr class="text-center" role="row">
                                <th class="bg-dark text-white">No</th>
                                <th class="bg-info text-white">Event Name</th>
                                <th class="bg-dark text-white">Event Start Date</th>
                                <th class="bg-dark text-white">Joined Date</th>
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
        var widthView = $(window).width();
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
                    left: (widthView < 768) ? 1 : 2,
                    right: 0
                },
                processing: true,
                serverSide: true,
                ajax: '{{ url('api/teacher/'.$teacher_counselor->id.'/events') }}',
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
                    // console.log(response)
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
