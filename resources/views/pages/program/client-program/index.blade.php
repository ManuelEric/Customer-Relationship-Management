@extends('layout.main')

@section('title', 'Client Program - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Client Program
        </a>

        <div class="dropdown">
            <button href="#" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"
                data-bs-auto-close="false" id="filter">
                <i class="bi bi-funnel me-2"></i> Filter
            </button>
            <form action="" class="dropdown-menu dropdown-menu-end pt-0 shadow" style="width: 450px" id="advanced-filter">
                <h6 class="dropdown-header bg-secondary text-white rounded-top">Advanced Filter</h6>
                <div class="row p-3">
                    <div class="col-md-12 mb-2">
                        <label for="">Program Name</label>
                        <select name="program_name[]" class="select form-select form-select-sm w-100" multiple id="program-name">
                            @foreach ($programs as $program)
                                <option value="{{ $program->prog_id }}"
                                    @if ($request->get('program_name') !== NULL && in_array($program->program_name, $request->get('program_name')))
                                        {{ "selected" }}
                                    @endif
                                    >{{ $program->program_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="">Conversion Lead</label>
                        <select name="conversion_lead[]" class="select form-select form-select-sm w-100" multiple id="conversion-lead">
                            @foreach ($conversion_leads as $lead)
                                <option value="{{ $lead->lead_id }}"
                                    @if ($request->get('conversion_lead') !== NULL && in_array($lead->lead_id, $request->get('conversion_lead')))
                                        {{ "selected" }}
                                    @endif
                                    >{{ $lead->conversion_lead }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <label>Start Date</label>
                                <input type="date" name="start_date" value="{{ $request->get('start_date') !== NULL ? $request->get('start_date') : null }}"
                                    class="form-control form-control-sm rounded">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>End Date</label>
                                <input type="date" name="end_date" value="{{ $request->get('end_date') !== NULL ? $request->get('end_date') : null }}"
                                    class="form-control form-control-sm rounded">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="">Program Status</label>
                        @php
                            $program_status = ['Pending', 'Success', 'Failed', 'Refund'];
                        @endphp
                        <select name="program_status[]" class="select form-select form-select-sm w-100" multiple id="program-status">
                            @foreach ($program_status as $key => $value)
                                <option value="{{ Crypt::encrypt($loop->iteration-1) }}"
                                    @if ($status_decrypted !== NULL && in_array($loop->iteration-1, $status_decrypted))
                                        {{ "selected" }}
                                    @endif
                                    >{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="">Mentor / Tutor Name</label>
                        <select name="mentor_tutor[]" class="select form-select form-select-sm w-100" multiple>
                            @foreach ($mentor_tutors as $user)
                                <option value="{{ Crypt::encrypt($user->id) }}"
                                    @if ($mentor_tutor_decrypted !== NULL && in_array($user->id, $mentor_tutor_decrypted))
                                        {{ "selected" }}
                                    @endif
                                    >{{ $user->fullname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="">PIC</label>
                        <select name="pic[]" id="" class="select form-select form-select-sm w-100" multiple>
                            @foreach ($pics as $pic)
                                <option value="{{ Crypt::encrypt($pic->empl_id) }}"
                                    @if ($pic_decrypted !== NULL && in_array($pic->empl_id, $pic_decrypted))
                                        {{ "selected" }}
                                    @endif
                                    >{{ $pic->pic_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 mt-3">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-sm btn-outline-danger" id="cancel">Cancel</button>
                            <button type="button" id="submit" class="btn btn-sm btn-outline-success">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Client Name</th>
                        <th>Grade</th>
                        <th>Program Name</th>
                        <th>Mentor/Tutor Name</th>
                        <th>End Program Date</th>
                        <th>Lead Source</th>
                        <th>Conversion Lead</th>
                        <th>Program Status</th>
                        <th>Running Status</th>
                        <th>Reason</th>
                        <th>PIC</th>
                        <th>Initial Consult</th>
                        <th>Initial Assessment Sent</th>
                        <th>Success Program Date</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="16"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script type="text/javascript" async defer>
    $(document).ready(function() {

        $("#advanced-filter #submit").click(function(e) {

            var query = "";
            var separator = "?"

            let data = $("#advanced-filter").serializeArray()
            $.each(data, function(index, field) {

                if (field.value != null && field.value != "") {

                    if (query != false)
                        separator = "&"

                    query += separator + field.name + "=" + field.value
                }
            })

            window.location = "{{ url('/program/client') }}" + query
        })
    })

    @php            
        $privilage = $menus['Program']->where('submenu_name', 'Client Program')->first();
    @endphp

    @if($privilage['copy'] == 0)
        document.oncontextmenu = new Function("return false"); 
                
        $('body').bind('cut copy paste', function(event) {
            event.preventDefault();
        });
    @endif

    @if ($privilage['export'] == 0)
        table.button(1).disable();
    @endif

    @if ($request->get('program_name') !== NULL)
        var program_name = new Array();
        @foreach ($request->get('program_name') as $key => $val)
            program_name.push("{{ $val }}")
        @endforeach
        $("#program-name").val(program_name).trigger('change')
    @endif

    @if ($request->get('conversion_lead') !== NULL)
        var conversion_lead = new Array();
        @foreach ($request->get('conversion_lead') as $key => $val)
            conversion_lead.push("{{ $val }}")
        @endforeach
        $("#conversion-lead").val(conversion_lead).trigger('change')
    @endif
    </script>
    {{-- Need Changing --}}
    <script>
        $('#cancel').click(function() {
            $(this).parents('.dropdown').find('button.dropdown-toggle').dropdown('toggle')
        });

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
                    right: 1
                },
                processing: true,
                serverSide: true,
                ajax: '',
                columns: [{
                        data: 'clientprog_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'fullname',
                    },
                    {
                        data: 'st_grade',
                    },
                    {
                        data: 'program_name',
                        render: function(data, type, row, meta) {
                            return row.referral_type == "Out" ? row.additional_prog_name : row.program_name
                        }
                    },
                    {
                        data: 'mentor_tutor_name',
                    },
                    {
                        data: 'prog_end_date',
                    },
                    {
                        data: 'lead_source',
                    },
                    {
                        data: 'conversion_lead',
                    },
                    {
                        data: 'status',
                        render: function(data, type, row, meta) {
                            switch(parseInt(data)) {
                                case 0:
                                    return 'pending'
                                    break;

                                case 1:
                                    return 'success'
                                    break;

                                case 2:
                                    return 'failed'
                                    break;

                                case 3:
                                    return 'refund'
                                    break;
                            }
                        }
                    },
                    {
                        data: 'prog_running_status',
                        render: function(data, type, row, meta) {
                            switch(parseInt(data)) {
                                case 0:
                                    return 'not yet'
                                    break;

                                case 1:
                                    return 'ongoing'
                                    break;

                                case 2:
                                    return 'done'
                                    break;
                            }
                        }
                    },
                    {
                        data: 'reason',
                    },
                    {
                        data: 'pic_name',
                    },
                    {
                        data: 'initconsult_date',
                    },
                    {
                        data: 'assessmentsent_date',
                    },
                    {
                        data: 'success_date',
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button"class="btn btn-sm btn-outline-warning showClientProgram"><i class="bi bi-eye"></i></button>'
                    }
                ]
            })

            // realtimeData(table)

            $('#programTable tbody').on('click', '.showClientProgram ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('client/student') }}/" + data.client_id + "/program/" + data.clientprog_id;
            });

            // $('#programTable tbody').on('click', '.deleteEvent ', function() {
            //     var data = table.row($(this).parents('tr')).data();
            //     confirmDelete('master/event', data.event_id)
            // });

        });
    </script>
@endsection
