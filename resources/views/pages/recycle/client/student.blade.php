@extends('layout.main')

@section('title', 'Student')

@push('styles')
    <link rel="stylesheet" href="{{ asset('library/dashboard/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">
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
@endpush

@section('content')

    <div class="card bg-secondary mb-1 p-2">
        <div class="row align-items-center justify-content-between g-3">
            <div class="col-md-6">
                <h5 class="text-white m-0">
                    <i class="bi bi-tag me-1"></i>
                    Deleted Students
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

    <div class="modal fade" id="importData" tabindex="-1" aria-labelledby="importDataLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('student.import') }}" method="POST" enctype="multipart/form-data">
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
                                <input type="file" name="file" id=""
                                    class="form-control form-control-sm">
                            </div>
                            <small class="text-warning mt-3">
                                * Please clean the file first, before importing the csv file. <br>
                                You can download the csv template <a
                                    href="{{ url('api/download/excel-template/student') }}">here</a>
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
            processing: true,
            serverSide: true,
            ajax: {
                url: '',
                data: function(params) {
                    params.school_name = $("#school-name").val()
                    params.graduation_year = $("#graduation-year").val()
                    params.lead_source = $("#lead-sources").val()
                    params.program_suggest = $("#program-name").val()
                    params.status_lead = $("#lead-source").val()
                    params.active_status = $("#active-status").val()
                }
            },
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
                    defaultContent: '-'
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
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return moment(data).format('MMMM Do YYYY')
                    }
                },
                {
                    data: 'updated_at',
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return moment(data).format('MMMM Do YYYY')
                    }
                },
                {
                    data: 'st_statusact',
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return data == 1 ? "Active" : "Non-active";
                    }
                },
                // {
                //     data: 'total_score',
                //     className: 'text-primary text-center',
                // },
                {
                    data: '',
                    className: 'text-center',
                    defaultContent: '<button type="button" class="btn btn-sm btn-outline-success restore"><i class="bi bi-arrow-counterclockwise"></i></button>'
                }
            ],
            // createdRow: function(row, data, index) {
            //     // temporary condition
            //     // while change soon
            //     if (data['total_score'] < 2) {
            //         $('td:nth-last-child(2)', row).addClass('bg-danger rounded text-white my-2');
            //         $('td:nth-last-child(2)', row).html(data['total_score'] + ' (Cold)');
            //     } else if ((data['total_score'] >= 2) && (data['total_score'] < 4)) {
            //         $('td:nth-last-child(2)', row).addClass('bg-danger rounded text-white my-2');
            //         $('td:nth-last-child(2)', row).html(data['total_score'] + ' (Warm)');
            //     } else {
            //         $('td:nth-last-child(2)', row).addClass('bg-danger rounded text-white my-2');
            //         $('td:nth-last-child(2)', row).html(data['total_score'] + ' (Hot)');
            //     }
            // }
            // createdRow: function(row, data, index) {
            //     // temporary condition
            //     // will change soon
            //     if (data['st_statusact'] == 0) {
            //         $('td', row).addClass('text-danger');
            //         $('td:nth-last-child(1) .deleteUser', row).addClass('d-none');
            //         // $('td:nth-last-child(2)', row).addClass('bg-danger rounded text-white my-2');
            //     }
            // }
        });

        $('#clientTable tbody').on('click', '.restore ', function() {
            var data = table.row($(this).parents('tr')).data();
            confirmRestore('restore/client/students', data.id)
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

        $('#clientTable tbody').on('click', '.editClient ', function() {
            var data = table.row($(this).parents('tr')).data();
            window.location.href = "{{ url('client/student') }}/" + data.id;
        });

        $('#clientTable tbody').on('change', '#status_lead ', function() {
            var data = table.row($(this).parents('tr')).data();
            var lead_status = $(this).val();

            $('#groupId').val(data.group_id);
            $('#clientId').val(data.id);
            $('#initProg').val(data.program_suggest);
            $('#leadStatus').val(lead_status);
            $('#hotLeadForm').attr('action', '{{ url('client/student') }}/' + data.id +
                '/lead_status/');
            $('#hotLeadModal').modal('show');

            confirmUpdateLeadStatus("{{ url('client/student') }}/" + data.id + "/lead_status/" + $(
                this).val(), data.id, data.program_suggest, lead_status)
        });

        // $('#clientTable tbody').on('click', '.deleteClient ', function() {
        //     var data = table.row($(this).parents('tr')).data();
        //     confirmDelete('asset', data.asset_id)
        // });

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