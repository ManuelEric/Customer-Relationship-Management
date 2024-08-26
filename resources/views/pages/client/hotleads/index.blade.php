@extends('layout.main')

@section('title', 'List of Hot Leadss')
@section('content')

    <div class="card bg-secondary mb-1 p-2">
        <div class="row align-items-center justify-content-between">
            <div class="col-md-6">
                <h5 class="text-white m-0">
                    <i class="bi bi-tag me-1"></i>
                    Hot Leads
                </h5>
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
            <ul class="nav nav-tabs flex-nowrap w-100 overflow-auto mb-3" style="overflow-y: hidden !important;">
                @foreach ($initialPrograms as $initialProgram)
                    <li class="nav-item">
                        <a class="nav-link text-nowrap {{ urldecode(Request::get('program')) == $initialProgram->name ? 'active' : '' }}"
                            aria-current="page"
                            href="{{ url('client/hot-leads?program=' . urlencode($initialProgram->name)) }}">{{ $initialProgram->name }}</a>
                    </li>
                @endforeach
            </ul>
            <style>
                #clientTable tr td.danger {
                    background: rgb(255, 151, 151)
                }
            </style>
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="clientTable">
                <thead class="bg-secondary text-white">
                    <tr class="text-center" role="row">
                        <th class="bg-info text-white">No</th>
                        <th class="bg-info text-white">Name</th>
                        <th>Lead Status</th>
                        <th>Mail</th>
                        <th>Phone</th>
                        <th>Parents Name</th>
                        <th>Parents Mail</th>
                        <th>Parents Phone</th>
                        <th>School</th>
                        <th>Graduation Year</th>
                        <th>Grade</th>
                        <th>Instagram</th>
                        <th>Location</th>
                        <th>Lead</th>
                        <th>Level of Interest</th>
                        <th>Interested Program</th>
                        {{-- <th>Success Program</th>
                        <th>Mentor/Tutor</th> --}}
                        <th>Year of Study Abroad</th>
                        <th>Country of Study Abroad</th>
                        <th>University Destination</th>
                        <th>Interest Major</th>
                        <th>Joined Date</th>
                        <th>Last Update</th>
                        <th>Status</th>
                        {{-- <th class="bg-info text-white">Score</th> --}}
                        <th class="bg-info text-white"># Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="21"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>


    {{-- Convert to Low Status --}}
    <div class="modal fade" id="hotLeadModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <span>
                        Reason
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100 text-start">
                    @csrf
                    <div class="form-group">
                        <div id="reason">
                            <div class="classReason">
                                <input type="hidden" name="clientId" id="clientId">
                                <input type="hidden" name="initProg" id="initProg">
                                <input type="hidden" name="leadStatus" id="leadStatus">
                                <input type="hidden" name="leadStatusOld" id="leadStatusOld">
                                <input type="hidden" name="groupId" id="groupId">
                                <select name="reason_id" class="w-100" id="selectReason"
                                    onchange="otherOption($(this).val())">
                                    <option data-placeholder="true"></option>
                                    {{-- @foreach ($reasons as $reason)
                                        <option value="{{ $reason->reason_id }}"
                                            {{ old('reason_id') == $reason->reason_id ? 'selected' : '' }}>
                                            {{ $reason->reason_name }}
                                        </option>
                                    @endforeach --}}
                                    <option value="other">
                                        Other option
                                    </option>
                                </select>
                                <div id="error-message">

                                </div>
                            </div>

                            <div class="d-flex align-items-center d-none" id="inputReason">
                                <input type="text" name="other_reason" class="form-control form-control-sm rounded"
                                    id="other_reason">
                                <div class="float-end cursor-pointer" onclick="resetOption()">
                                    <b>
                                        <i class="bi bi-x text-danger"></i>
                                    </b>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" href="#" class="btn btn-outline-danger btn-sm"
                            onclick="closeUpdateLead()">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</button>
                        <button type="button" onclick="updateHotLead()" class="btn btn-primary btn-sm">
                            <i class="bi bi-save2 me-1"></i>
                            Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#selectReason').select2({
                dropdownParent: $('#hotLeadModal'),
                placeholder: "Select value",
                allowClear: true
            });
        });

        function otherOption(value) {
            if (value == 'other') {
                $('.classReason').addClass('d-none')
                $('#inputReason').removeClass('d-none')
                $('#inputReason input').focus()
            } else {
                $('#inputReason').addClass('d-none')
                $('.classReason').removeClass('d-none')
            }
        }

        function resetOption() {
            $('.classReason').removeClass('d-none')
            $('#selectReason').val(null).trigger('change')
            $('#inputReason').addClass('d-none')
            $('#inputReason input').val(null)
        }
    </script>

    <script>
        // $('#cancel').click(function() {
        //     $(this).parents('.dropdown').find('button.dropdown-toggle').dropdown('toggle')
        // });

        var widthView = $(window).width();
        $(document).ready(function() {

            var table = $('#clientTable').DataTable({
                order: [
                    // [20, 'desc'],
                    [1, 'asc']
                ],
                dom: 'Bfrtip',
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
                search: {
                    return: true
                },
                processing: true,
                serverSide: true,
                ajax: '',
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'full_name',
                        render: function(data, type, row, meta) {
                            return data
                        }
                    },
                    {
                        data: 'status_lead',
                        searchable: false,
                        className: 'text-center',
                        defaultContent: '-',
                        render: function(data, type, row, meta) {
                            var warm = '';
                            var hot = '';
                            var cold = '';
                            switch (data) {
                                case 'Hot':
                                    hot = 'selected';
                                    break;

                                case 'Warm':
                                    warm = 'selected';
                                    break;

                                case 'Cold':
                                    cold = 'selected';
                                    break;
                            }
                            return data != null ?
                                '<select name="status_lead" style="color:#212b3d" class="select w-100 leads' +
                                row.id + '" id="status_lead"><option value="hot" ' +
                                hot + '>Hot</option><option value="warm" ' + warm +
                                '>Warm</option><option value="cold" ' + cold +
                                '>Cold</option></select>' : '-';
                        }

                    },
                    {
                        data: 'mail',
                        defaultContent: '-'
                    },
                    {
                        data: 'phone',
                        defaultContent: '-'
                    },
                    {
                        data: 'parent_name',
                        name: 'parent_name',
                        defaultContent: '-',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'parent_mail',
                        name: 'parent_mail',
                        defaultContent: '-'
                    },
                    {
                        data: 'parent_phone',
                        name: 'parent_phone',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'school_name',
                        name: 'school_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'graduation_year',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'grade_now',
                        defaultContent: '-',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return data > 12 ? 'Not high school' : data;
                        }
                    },
                    {
                        data: 'insta',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'address',
                        defaultContent: '-',
                    },
                    {
                        data: 'lead_source',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'st_levelinterest',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'interest_prog',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'st_abryear',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'abr_country',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'dream_uni',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'dream_major',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'created_at',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return moment(data).format('MMMM Do YYYY')
                        }
                    },
                    {
                        data: 'updated_at',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return moment(data).format('MMMM Do YYYY')
                        }
                    },
                    {
                        data: 'st_statusact',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return data == 1 ? "Active" : "Non-active";
                        }
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning editClient" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add Program"><i class="bi bi-send-plus"></i></button>'
                    }
                ],
            });

            @php
                $privilage = $menus['Client']->where('submenu_name', 'Students')->first();
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

            // Tooltip 
            $('#clientTable tbody').on('mouseover', 'tr', function() {
                $('[data-bs-toggle="tooltip"]').tooltip({
                    trigger: 'hover',
                    html: true
                });
            });

            // View More 
            $('#clientTable tbody').on('click', '.editClient ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.open("{{ url('client/student') }}/" + data.id + '/program/create', "_blank");
            });

            /* for advanced filter */
            $("#school-name").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#graduation-year").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#lead-sources").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#program-name").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#lead-source").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#active-status").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })
        });

        function closeUpdateLead() {
            const id = $('#clientId').val();
            const old_status = $('#leadStatusOld').val().toLowerCase();
            $('.leads' + id).val(old_status)
            $('#hotLeadModal').modal('hide');
            $('#selectReason').val('').trigger('change');
            $('#other_reason').val('');
        }

        function updateHotLead() {
            var link = '{{ url('client/student') }}/' + $('#clientId').val() + '/lead_status';
            $('#hotLeadModal').modal('hide');
            Swal.showLoading()
            axios.post(link, {
                    groupId: $('#groupId').val(),
                    clientId: $('#clientId').val(),
                    initProg: $('#initProg').val(),
                    leadStatus: $('#leadStatus').val(),
                    reason_id: $('#selectReason').val(),
                    other_reason: $('#other_reason').val(),
                })
                .then(function(response) {
                    swal.close();

                    let obj = response.data;

                    $('#clientTable').DataTable().ajax.reload(null, false);

                    switch (obj.code) {
                        case 200:
                            notification('success', obj.message)

                            break;
                        case 400:
                            $('#hotLeadModal').modal('show');
                            if (obj.message['reason_id'] != undefined) {
                                $('#error-message').html('<small class="text-danger fw-light">' + obj.message[
                                    'reason_id'] + '</small>')
                            } else if (obj.message['leadStatus'] != undefined) {
                                $('#error-message').html('<small class="text-danger fw-light">' + obj.message[
                                    'leadStatus'] + '</small>')
                            }
                            break;

                        case 500:
                            notification('error', 'Something went wrong while update lead status')
                            break;
                    }
                })
                .catch(function(error) {
                    swal.close();
                    notification('error', error)
                })
        }

        function updateHotLead() {
            var link = '{{ url('client/student') }}/' + $('#clientId').val() + '/lead_status';
            $('#hotLeadModal').modal('hide');
            Swal.showLoading()
            axios.post(link, {
                    groupId: $('#groupId').val(),
                    clientId: $('#clientId').val(),
                    initProg: $('#initProg').val(),
                    leadStatus: $('#leadStatus').val(),
                    reason_id: $('#selectReason').val(),
                    other_reason: $('#other_reason').val(),
                })
                .then(function(response) {
                    swal.close();

                    let obj = response.data;

                    $('#clientTable').DataTable().ajax.reload(null, false);

                    switch (obj.code) {
                        case 200:
                            notification('success', obj.message)

                            break;
                        case 400:
                            $('#hotLeadModal').modal('show');
                            if (obj.message['reason_id'] != undefined) {
                                $('#error-message').html('<small class="text-danger fw-light">' + obj.message[
                                    'reason_id'] + '</small>')
                            } else if (obj.message['leadStatus'] != undefined) {
                                $('#error-message').html('<small class="text-danger fw-light">' + obj.message[
                                    'leadStatus'] + '</small>')
                            }
                            break;

                        case 500:
                            notification('error', 'Something went wrong while update lead status')
                            break;
                    }
                })
                .catch(function(error) {
                    swal.close();
                    notification('error', error)
                })
        }
    </script>
@endpush
