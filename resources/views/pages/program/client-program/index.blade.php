@extends('layout.main')

@section('title', 'Client Program ')
@push('styles')
    <style>
        @media only screen and (max-width: 600px) {
            .filter-clientprog {
                width: 300px !important;
            }
        }
    </style>
@endpush
@section('content')

    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Client Program
            </h5>

            <div class="dropdown">
                <button href="#" class="btn btn-sm btn-light text-dark dropdown-toggle" data-bs-toggle="dropdown"
                    data-bs-auto-close="false" id="filter">
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
                            <label for="">Main Program</label>
                            <select name="main_program[]" class="select form-select form-select-sm w-100" multiple
                                id="main-program">
                                @foreach ($mainPrograms as $mainProgram)
                                    <option value="{{ $mainProgram->main_prog_id }}"
                                        @if ($request->get('main_program') !== null && in_array($mainProgram->main_prog_name, $request->get('main_program'))) {{ 'selected' }} @endif>
                                        {{ $mainProgram->main_prog_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Program Name</label>
                            <select name="program_name[]" class="select form-select form-select-sm w-100" multiple
                                id="program-name">
                                @foreach ($programs as $program)
                                    <option value="{{ $program->prog_id }}"
                                        @if ($request->get('program_name') !== null && in_array($program->program_name, $request->get('program_name'))) {{ 'selected' }} @endif>
                                        {{ $program->program_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">School Name</label>
                            <select name="school_name[]" class="select form-select form-select-sm w-100" multiple
                                id="school-name">
                                @foreach ($schools as $school)
                                    <option value="{{ $school->sch_id }}"
                                        @if ($request->get('school_name') !== null && in_array($school->sch_name, $request->get('school_name'))) {{ 'selected' }} @endif>
                                        {{ $school->sch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Conversion Lead</label>
                            <select name="conversion_lead[]" class="select form-select form-select-sm w-100" multiple
                                id="conversion-lead">
                                @foreach ($conversion_leads as $lead)
                                    <option value="{{ $lead->lead_id }}"
                                        @if ($request->get('conversion_lead') !== null && in_array($lead->lead_id, $request->get('conversion_lead'))) {{ 'selected' }} @endif>
                                        {{ $lead->conversion_lead }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Grade</label>
                            <select name="grade[]" class="select form-select form-select-sm w-100" multiple
                                id="grade">
                                @for ($grade = 1; $grade <= 12; $grade++)
                                    <option value="{{ $grade }}"
                                        @if ($request->get('grade') !== null && in_array($grade, $request->get('grade'))) {{ 'selected' }} @endif>
                                        {{ $grade }}</option>
                                @endfor
                                    <option value="not_high_school" @if ($request->get('grade') !== null && in_array('not_high_school', $request->get('grade'))) {{ 'selected' }} @endif>
                                        Not High School
                                    </option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <div class="row g-2">
                                <div class="col-md-6 mb-2">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date"
                                        value="{{ $request->get('start_date') !== null ? $request->get('start_date') : null }}"
                                        class="form-control form-control-sm rounded">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>End Date</label>
                                    <input type="date" name="end_date"
                                        value="{{ $request->get('end_date') !== null ? $request->get('end_date') : null }}"
                                        class="form-control form-control-sm rounded">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Program Status</label>
                            @php
                                $program_status = ['Pending', 'Success', 'Failed', 'Refund'];
                            @endphp
                            <select name="program_status[]" class="select form-select form-select-sm w-100" multiple
                                id="program-status">
                                @foreach ($program_status as $key => $value)
                                    <option value="{{ Crypt::encrypt($loop->iteration - 1) }}"
                                        @if ($status_decrypted !== null && in_array($loop->iteration - 1, $status_decrypted)) {{ 'selected' }} @endif>{{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Mentor / Tutor Name</label>
                            <select name="mentor_tutor[]" class="select form-select form-select-sm w-100" multiple>
                                @foreach ($mentor_tutors as $user)
                                    <option value="{{ Crypt::encrypt($user->id) }}"
                                        @if ($mentor_tutor_decrypted !== null && in_array($user->id, $mentor_tutor_decrypted)) {{ 'selected' }} @endif>{{ $user->fullname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">PIC</label>
                            <select name="pic[]" id="" class="select form-select form-select-sm w-100" multiple>
                                @foreach ($pics as $pic)
                                    <option value="{{ $pic->uuid }}" @selected($picUUID_arr !== null && in_array($pic->uuid, $picUUID_arr))>{{ $pic->pic_name }}
                                    </option>
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
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Client Program ID</th>
                        <th class="bg-info text-white">Client Name</th>
                        <th>Student Mail</th>
                        <th>Student Phone</th>
                        <th>School</th>
                        <th>Grade</th>
                        <th>Program Name</th>
                        <th>Register As</th>
                        <th>Parent Name</th>
                        <th>Parent Mail</th>
                        <th>Parent Phone</th>
                        <th>Mentor/Tutor Name</th>
                        <th>End Program Date</th>
                        <th>Lead Source</th>
                        <th>Conversion Lead</th>
                        <th>Referral Name</th>
                        <th>Notes</th>
                        <th>Program Status</th>
                        <th>Running Status</th>
                        <th>Reason</th>
                        <th>PIC</th>
                        <th>Initial Consult</th>
                        <th>Initial Assessment Sent</th>
                        <th>First Discuss Date</th>
                        <th>Failed Program Date</th>
                        <th>Success Program Date</th>
                        <th>Created At</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="17"></td>
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

        @if ($privilage['copy'] == 0)
            document.oncontextmenu = new Function("return false");

            $('body').bind('cut copy paste', function(event) {
                event.preventDefault();
            });
        @endif

        @if ($privilage['export'] == 0)
            table.button(1).disable();
        @endif

        @if ($request->get('main_program') !== null)
            var main_program = new Array();
            @foreach ($request->get('main_program') as $key => $val)
                main_program.push("{{ $val }}")
            @endforeach
            $("#main-program").val(main_program).trigger('change')
        @endif
        
        @if ($request->get('program_name') !== null)
            var program_name = new Array();
            @foreach ($request->get('program_name') as $key => $val)
                program_name.push("{{ $val }}")
            @endforeach
            $("#program-name").val(program_name).trigger('change')
        @endif

        @if ($request->get('school_name') !== null)
            var school_name = new Array();
            @foreach ($request->get('school_name') as $key => $val)
                school_name.push("{{ $val }}")
            @endforeach
            $("#school-name").val(school_name).trigger('change')
        @endif

        @if ($request->get('conversion_lead') !== null)
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
                order: [
                    26, 'desc'
                ],
                
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
                                body: function (data, row, column, node){
                                    var clearHtml = '';
                                    var result = '';
                                    if(column === 2){
                                        clearHtml = data.replace(/<[^>]*>?/gm, '');
                                        if (clearHtml.indexOf('{}') === -1) {
                                            result = clearHtml.replace(/{.*}/, '');
                                        }
                                    }else if(column === 1 || column === 18 || column === 28){
                                        result = data.replace(/<[^>]*>?/gm, '');
                                    }else{
                                        result = data;
                                    }
                                    return result;
                                }
                            }
                        },
                    },
                    {
                        text: '<i class="bi bi-bag-plus"></i> Create Bundle',
                        action: function(e, dt, node, config) {
                            addBundle();
                        }
                    },
                    {
                        text: '<i class="bi bi-bag-x"></i> Cancel Bundle',
                        action: function(e, dt, node, config) {
                            cancelBundle();
                        }
                    },
                ],
                scrollX: true,
                fixedColumns: {
                    left: window.matchMedia('(max-width: 767px)').matches ? 0 : 3,
                    right: 1
                },
                processing: true,
                serverSide: true,
                ajax: '',
                pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
                columns: [
                    {
                        data: 'clientprog_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'custom_clientprog_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return row.has_invoice > 0 ? data + ' <i class="bi bi-receipt text-info"></i>' : data;
                        }
                    },
                    {
                        data: 'fullname',
                        render: function(data, type, row, meta) {
                            var bundling_id = null;
                            if(row.bundling_id !== null){
                                bundling_id = row.bundling_id.substring(0, 3).toUpperCase();
                            }
                            return row.is_bundle > 0 ? data + ' <span class="badge badge-bundle text-bg-success" style="font-size:8px";>{Bundle '+ bundling_id +'}</span>' : data;
                        }
                    },
                    {
                        data: 'student_mail',
                    },
                    {
                        data: 'student_phone',
                    },
                    {
                        data: 'school_name',
                    },
                    {
                        data: 'grade_now',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            if (data > 12)
                                return "Not High School";

                            return data;
                        }
                    },
                    {
                        data: 'program_name',
                        render: function(data, type, row, meta) {
                            return row.referral_type == "Out" ? row.additional_prog_name : row
                                .program_name
                        }
                    },
                    {
                        data: 'register_as',
                    },
                    {
                        data: 'parent_fullname',
                    },
                    {
                        data: 'parent_mail',
                    },
                    {
                        data: 'parent_phone',
                    },
                    {
                        data: 'mentor_tutor_name',
                    },
                    {
                        data: 'prog_end_date',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            if (data) {
                                return moment(data).format("MMM Do YY");
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        data: 'lead_source',
                        className: 'text-center'
                    },
                    {
                        data: 'conversion_lead',
                        className: 'text-center'
                    },
                    {
                        data: 'referral_name',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'strip_tag_notes',
                        className: 'text-center',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            switch (parseInt(data)) {
                                case 0:
                                    return '<i class="bi bi-hourglass-split me-2 text-warning"></i>Pending'
                                    break;

                                case 1:
                                    return '<i class="bi bi-check me-2 text-success"></i>Success'
                                    break;

                                case 2:
                                    return '<i class="bi bi-x me-2 text-danger"></i>Failed'
                                    break;

                                case 3:
                                    return '<i class="bi bi-arrow-counterclockwise me-2 text-info"></i>Refund'
                                    break;
                            }
                        }
                    },
                    {
                        data: 'prog_running_status',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            switch (parseInt(data)) {
                                case 0:
                                    return 'Not yet'
                                    break;

                                case 1:
                                    return 'Ongoing'
                                    break;

                                case 2:
                                    return 'Done'
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
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            if (data) {
                                return moment(data).format("MMM Do YY");
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        data: 'assessmentsent_date',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            if (data) {
                                return moment(data).format("MMM Do YY");
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        data: 'first_discuss_date',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            if (data) {
                                return moment(data).format("MMM Do YY");
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        data: 'failed_date',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            if (data) {
                                return moment(data).format("MMM Do YY");
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        data: 'success_date',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            if (data) {
                                return moment(data).format("MMM Do YY");
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        data: 'created_at',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            if (data) {
                                return moment(data).format("MMM Do YY");
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button"class="btn btn-sm btn-outline-warning showClientProgram"><i class="bi bi-eye"></i></button>'
                    }
                ],
                // createdRow: function(row, data, index) {
                //     let currentDate = new Date().toJSON().slice(0, 10);
                //     if (data['created_at'].slice(0, 10) == currentDate) {
                //         $('td', row).addClass('table-success');
                //     }
                // }
            })

            var selectedRows = [];
            var customClientProgId = [];
            var bundlingIds = [];

            function updateRowSelection() {
                table.rows().every(function () {
                    const rowData = this.data();
                    const isSelected = selectedRows.includes(rowData.clientprog_id);
                    if (isSelected) {
                        this.nodes().to$().addClass('selected');
                    } else {
                        this.nodes().to$().removeClass('selected');

                    }
                });
            }

            table.on('click', 'tbody tr', function (e) {
                const rowData = table.row(this).data();
                const index = selectedRows.indexOf(rowData.clientprog_id);
                const isSelected = selectedRows.includes(rowData.clientprog_id);

                if(index === -1){
                    
                    selectedRows.push(rowData.clientprog_id);
                    customClientProgId.push(rowData.custom_clientprog_id);
                    bundlingIds.push(rowData.bundling_id);
                    // e.currentTarget.classList.add('selected');
                }else{
                    selectedRows.splice(index, 1);
                    customClientProgId.splice(index, 1);
                    bundlingIds.splice(index, 1);
                    // e.currentTarget.classList.remove('selected');
                }


                updateRowSelection();
            });

            table.on('draw', updateRowSelection)
            
            function addBundle() {
                var html = '';

                if (selectedRows.length > 1) {
                    Swal.fire({
                        title: "Confirmation!",
                        text: 'Are you sure to create bundle this program?',
                        showCancelButton: true,
                        confirmButtonText: "Yes",
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            showLoading();
                            var link = '{{ route('program.client.bundle') }}';
                            axios.post(link, {
                                    choosen: selectedRows,
                                    number: customClientProgId,
                                })
                                .then(function(response) {
                                    
                                    html = '';
                                    html += `<ul>`;

                                    if(response.data.success == false){
                                        var error = response.data.error
                                        if(Object.keys(error).length){
                                            Object.keys(error).forEach(key => {
                                                html += `<li class="text-danger">${key + ': ' + error[key]}</li>`
                                            });
                                        }
                                        html += `</ul>`;
                                        Swal.fire({
                                            icon: "error",
                                            title: "Oops...",
                                            html: html,
                                        });
                                    }else{
                                        swal.close();
                                        notification('success', 'Successfully created a bundle program');
                                        // location.reload();
                                    }
                                    
                                    $("#programTable").DataTable().ajax.reload()
                                })
                                .catch(function(error) {
                                    
                                    swal.close();
                                    notification('error', error.message);
                                })
                        }
                    });

                } else if(selectedRows.length === 1){
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Please select at least 2 client program!",
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Please select the client program data first!",
                    });
                }
                
            }

            function cancelBundle(){
                var html = '';
                
                if (selectedRows.length > 1) {
                    Swal.fire({
                        title: "Confirmation!",
                        text: 'Are you sure to cancel bundle this program?',
                        showCancelButton: true,
                        confirmButtonText: "Yes",
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            showLoading();
                            var link = '{{ route('program.client.bundle.destroy') }}';
                            axios.post(link, {
                                    choosen: selectedRows,
                                    number: customClientProgId,
                                    bundlingId: bundlingIds
                                })
                                .then(function(response) {
                                    html = '';
                                    html += `<ul>`;

                                    if(response.data.success == false){
                                        var error = response.data.error
                                        if(Object.keys(error).length){
                                            Object.keys(error).forEach(key => {
                                                html += `<li class="text-danger">${key + ': ' + error[key]}</li>`
                                            });
                                        }
                                        html += `</ul>`;
                                        Swal.fire({
                                            icon: "error",
                                            title: "Oops...",
                                            html: html,
                                        });
                                    }else{
                                        swal.close();
                                        notification('success', 'Successfully canceled a bundle program');
                                        // location.reload();
                                    }

                                    $("#programTable").DataTable().ajax.reload()

                                })
                                .catch(function(error) {
                                    swal.close();
                                    notification('error', error.message);
                                })
                        }
                    });

                } else if(selectedRows.length === 1){
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Please select at least 2 client program!",
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Please select the client program data first!",
                    });
                }
            }

            // realtimeData(table)

            $('#programTable tbody').on('click', '.showClientProgram ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('client/student') }}/" + data.client_id + "/program/" + data
                    .clientprog_id;
            });

            // $('#programTable tbody').on('click', '.deleteEvent ', function() {
            //     var data = table.row($(this).parents('tr')).data();
            //     confirmDelete('master/event', data.event_id)
            // });

        });


    </script>
@endsection
