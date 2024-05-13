@extends('layout.main')

@section('title', 'Raw Students Data')

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
                    Students
                </h5>
            </div>
            <div class="col-md-6">
                <div class="row g-2">
                    {{-- <div class="col-md-3 col-6">
                        <a href="{{ url('api/download/excel-template/student') }}"
                            class="btn btn-sm btn-light text-info btn-download w-100"><i class="bi bi-download"></i> <span
                                class="ms-1">Template</span></a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="javascript:void(0)" class="btn btn-sm btn-light text-info btn-import w-100"
                            data-bs-toggle="modal" data-bs-target="#importData"><i class="bi bi-cloud-upload"></i> <span
                                class="ms-1">Import</span></a>
                    </div> --}}
                    <div class="col-md-3 offset-6">
                        <div class="dropdown">
                            <button href="#" class="btn btn-sm btn-light text-dark dropdown-toggle w-100"
                                data-bs-toggle="dropdown" data-bs-auto-close="false" id="filter">
                                <i class="bi bi-funnel me-2"></i> Filter
                            </button>
                            <form action="" class="dropdown-menu dropdown-menu-end pt-0 advance-filter shadow"
                                style="width: 400px;" id="advanced-filter">
                                <div class="dropdown-header bg-info text-dark py-2 d-flex justify-content-between">
                                    Advanced Filter
                                    <i class="bi bi-search"></i>
                                </div>
                                <div class="row p-3">
                                    <div class="col-md-12 mb-2">
                                        <label for="">School Name</label>
                                        <select name="school_name[]" class="select form-select form-select-sm w-100"
                                            multiple id="school-name">
                                            @foreach ($advanced_filter['schools'] as $school)
                                                <option value="{{ $school->sch_name }}">{{ $school->sch_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <label for="">Grade</label>
                                        <select name="grade[]" class="select form-select form-select-sm w-100"
                                            multiple id="grade">
                                            @for ($grade = 1; $grade <= 12; $grade++)
                                                <option value="{{ $grade }}">{{ $grade }}</option>
                                            @endfor
                                                <option value="not_high_school">Not High School</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <label for="">Graduation Year</label>
                                        <select name="graduation_year[]" class="select form-select form-select-sm w-100"
                                            multiple id="graduation-year">
                                            @for ($i = $advanced_filter['max_graduation_year']; $i >= 2016; $i--)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <label for="">Lead Source</label>
                                        <select name="lead_source[]" class="select form-select form-select-sm w-100"
                                            multiple id="lead-sources">
                                            @foreach ($advanced_filter['leads'] as $lead)
                                                <option value="{{ $lead['main_lead'] }}">{{ $lead['main_lead'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <label for="">Role</label>
                                        <select name="roles[]" class="select form-select form-select-sm w-100"
                                            multiple id="roles">
                                                <option value="Student">Student</option>
                                                <option value="Parent">Parent</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="row g-2">
                                            <label>Joined Date</label>
                                            <div class="col-md-6 mb-2">
                                                <input type="date" name="start_joined_date" id="start_joined_date"
                                                    class="form-control form-control-sm rounded">
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <input type="date" name="end_joined_date" id="end_joined_date"
                                                    class="form-control form-control-sm rounded">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3 d-none">
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                id="cancel">Cancel</button>
                                            <button type="button" id="submit"
                                                class="btn btn-sm btn-outline-success">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ url('client/student/create') }}" class="btn btn-sm btn-info w-100"><i
                                class="bi bi-plus-square me-1"></i> Add Student</a>
                    </div>
                </div>
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
            <x-client.student.nav />

            @push('styles')
                <style>
                    #clientTable tr td.danger {
                        background: rgb(255, 151, 151)
                    }
                </style>
            @endpush
            <div class="table-responsive">
                <table class="table table-bordered table-hover nowrap align-middle w-100" id="rawTable">
                    <thead class="bg-secondary text-white">
                        <tr class="text-center" role="row">
                            <th class="bg-info text-white">#</th>
                            <th class="bg-info text-white">
                                <i class="bi bi-check"></i>
                            </th>
                            <th class="bg-info text-white">Name</th>
                            <th class="bg-info text-white">Suggestion</th>
                            <th>Role</th>
                            <th>Mail</th>
                            <th>Phone</th>
                            <th>Student/Parents Name</th>
                            <th>Student/Parents Mail</th>
                            <th>Student/Parents Phone</th>
                            <th>School</th>
                            <th>Grade</th>
                            <th>Graduation Year</th>
                            <th>Lead</th>
                            <th>Referral Name</th>
                            <th>Country of Study Abroad</th>
                            <th>Joined Event</th>
                            <th>Interest Program</th>
                            <th>Scholarship Eligible</th>
                            <th>Joined Date</th>
                            <th class="bg-info text-white">Last Update</th>
                            <th class="bg-info text-white">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
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
        var widthView = $(window).width();
        $(document).ready(function() {

            // Formatting function for row details - modify as you need
            function format(d, clientSuggest) {
                var similar = '<table class="table w-auto table-hover">'
                var joined_program = '';
                var suggestion = d.suggestion;
                var arrSuggest = [];
                var roles = "'" + d.roles + "'";
                if (suggestion !== null && suggestion !== undefined) {
                    arrSuggest = suggestion.split(',');
                }

                if (arrSuggest.length > 0) {
                    similar +=
                        '<th colspan=8>Comparison with Similar Names:</th>' +
                        '</tr>' +
                        '<tr>' +
                        '<th>#</th><th>Name</th><th>Email</th><th>Phone Number</th><th>School Name</th><th>Parent Name</th><th>Graduation Year</th><th>Joined Program</th>' +
                        '</tr>';
                    clientSuggest.forEach(function(item, index) {
                        joined_program = '';
                        program_limit = 2;
                        program_number = item.client_program.length;
                        program_other = program_number - program_limit;
                        check_school = item.school?.is_verified == 'Y' ?
                            '<i class="bi bi-check-circle-fill text-success"></i>' :
                            '<i class="bi bi-x-circle-fill text-danger"></i>';

                        if (program_number > 0) {
                            item.client_program.forEach(function(clientprog, index) {
                                if (clientprog.status == 1 && index < program_limit) {
                                    joined_program += clientprog.program.program_name;
                                    (program_number !== index + 1 ? joined_program +=
                                        ', ' : '')

                                    if (index == (program_limit - 1) && program_number >
                                        program_limit) {
                                        joined_program +=
                                            '<div class="badge bg-warning text-dark py-1 px-2">' +
                                            program_other + ' More </div>'
                                    }
                                }

                            })
                        }

                        similar += '<tr onclick="comparison(' +
                            d.id + ',' + item.id + ', ' + roles.toLowerCase() + ')" class="cursor-pointer">' +
                            '<td><input type="radio" name="similar' + d.id +
                            '" class="form-check-input item-' + item.id + '" onclick="comparison(' +
                            d.id + ',' + item.id + ', ' + roles.toLowerCase() + ')" /></td>' +
                            '<td><i class="bi bi-person"></i> ' + item.first_name + ' ' + (item
                                .last_name !== null ? item.last_name :
                                '') + '</td>' +
                            '<td>' + (item.mail !== null ? item.mail : '-') + '</td>' +
                            '<td>' + (item.phone !== null ? item.phone : '-') + '</td>' +
                            '<td>' + (typeof item.school !== 'undefined' && item.school !== null ? item
                                .school.sch_name + ' ' + check_school : '-') + '</td>' +
                            '<td><i class="bi bi-person"></i> ' + (item.parents.length > 0 ? item.parents[0]
                                .first_name + ' ' + (item
                                    .parents[0].last_name !== null ? item.parents[0].last_name : '') : '-'
                            ) +
                            '</td>' +
                            '<td>' + (item.graduation_year_real !== null ? item.graduation_year_real :
                                '-') + '</td>' +
                            '<td>' +
                            (item.client_program.length > 0 ?
                                joined_program :
                                '-') +
                            '</td>' +
                            '</tr>'
                    });
                }

                similar += '</table>'
                // `d` is the original data object for the row
                return (similar);
            }

            var table = $('#rawTable').DataTable({
                order: [
                    // [20, 'desc'],
                    [18, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: [
                    'pageLength', {
                        extend: 'excel',
                        text: 'Export to Excel',
                    },
                    {
                        text: '<i class="bi bi-check-square me-1"></i> Select All',
                        action: function(e, dt, node, config) {
                            selectAll();
                        }
                    },
                    {
                        text: '<i class="bi bi-trash-fill me-1"></i> Delete',
                        action: function(e, dt, node, config) {
                            multipleDelete();
                        }
                    },
                ],
                lengthMenu: [
                    [10, 50, 100, -1],
                    ['10 Leads', '50 Leads', '100 Leads', 'Show all']
                ],
                scrollX: true,
                columnDefs: [{
                    width: 20,
                    targets: 0
                }],
                fixedColumns: {
                    left: (widthView < 768) ? 3 : 4,
                    right: 2
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: '',
                    data: function(params) {
                        params.school_name = $("#school-name").val()
                        params.grade = $("#grade").val()
                        params.graduation_year = $("#graduation-year").val()
                        params.lead_source = $("#lead-sources").val()
                        params.roles = $("#roles").val()
                        params.program_suggest = $("#program-name").val()
                        params.status_lead = $("#lead-source").val()
                        params.active_status = $("#active-status").val()
                        params.start_joined_date = $("#start_joined_date").val()
                        params.end_joined_date = $("#end_joined_date").val()
                    }
                },
                rowCallback: function(row, data) {
                    if (data.suggestion) {
                        $('td:eq(0)', row).addClass('dt-control');
                    }
                },
                columns: [{
                        orderable: false,
                        data: null,
                        defaultContent: '',
                    },
                    {
                        data: 'id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return '<input type="checkbox" class="editor-active cursor-pointer" data-id="' +
                                data + '">'
                        }
                    },
                    {
                        data: 'fullname',
                        render: function(data, type, row, meta) {
                            return data
                        }
                    },
                    {
                        data: 'suggestion',
                        className: 'text-center',
                        searchable: false,
                        render: function(data, type, row, meta) {
                            if (data == undefined && data == null) {
                                return '-'
                            } else {
                                var arraySuggestion = data.split(',');
                                return '<div class="badge badge-warning py-1 px-2 ms-2">' +
                                    arraySuggestion.length + ' Similar Names</div>'
                            }
                        }
                    },
                    {
                        data: 'roles',
                        defaultContent: '-'
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
                        data: 'second_client_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'second_client_mail',
                        defaultContent: '-'
                    },
                    {
                        data: 'second_client_phone',
                        defaultContent: '-'
                    },
                    {
                        data: 'school_name',
                        defaultContent: '-',
                        render: function(data, type, row, meta) {
                            if (data != null) {
                                if (row.is_verified_school == 'Y') {
                                    return data +
                                        '<i class="bi bi-check-circle-fill text-success ms-1" data-bs-toggle="tooltip" data-bs-placement="top" ' +
                                        'data-bs-custom-class="custom-tooltip" ' +
                                        'data-bs-title="Verified"></i>'
                                } else {
                                    return data +
                                        '<i class="bi bi-x-circle-fill text-danger ms-1" data-bs-toggle="tooltip" data-bs-placement="top" ' +
                                        'data-bs-custom-class="custom-tooltip" ' +
                                        'data-bs-title="Not Verified"></i>'
                                }
                            } else {
                                return data
                            }
                        }
                    },
                    {
                        data: 'grade_now',
                        className: 'text-center',
                        defaultContent: '-',
                        render: function(data, type, row, meta) {
                            if (data > 12)
                                return "Not High School";

                            return data;
                        }
                    },
                    {
                        data: 'graduation_year_real',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'lead_source',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'referral_name',
                        className: 'text-center',
                        defaultContent: '-',
                        render: function(data, type, row, meta) {
                            if (row.lead_source == "Referral"){
                                return data;
                            }else{
                                return '-';
                            }
                        }
                    },
                    {
                        data: 'interest_countries',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'joined_event',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'interest_prog',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'scholarship',
                        className: 'text-center',
                        searchable: false,
                        render: function(data, type, row, meta) {
                            if (data == "Y")
                                return "Yes"
                            else
                                return "No"
                        }
                    },
                    {
                        data: 'created_at',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'updated_at',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 deleteRawClient"><i class="bi bi-eraser"></i></button>',
                        render: function(data, type, row, meta) {
                            var roles = "'" + row.roles + "'";
                            return '<div class="d-flex gap-1 justify-content-center">' +
                                '<small class="btn btn-sm btn-info px-1 pt-1 pb-0  cursor-pointer item-' +
                                row
                                .id +
                                '" data-bs-toggle="tooltip" data-bs-placement="top" ' +
                                'data-bs-custom-class="custom-tooltip" ' +
                                'data-bs-title="Convert to New Lead" onclick="newLeads(' +
                                row.id + ', ' + roles.toLowerCase() + ')">' +
                                '<i class="bi bi-send-check-fill me-1 text-secondary"></i>' +
                                '</small>' +
                                '<small data-bs-toggle="tooltip" data-bs-placement="top" ' +
                                'data-bs-custom-class="custom-tooltip" ' +
                                'data-bs-title="Delete" class="btn btn-sm btn-danger px-1 pt-1 pb-0  cursor-pointer deleteRawClient">' +
                                '<i class="bi bi-trash"></i>' +
                                '</small>' +
                                '</div>';
                        }
                    },
                ],
                createdRow: function(row, data, index) {
                    let currentDate = new Date().toJSON().slice(0, 10);
                    if (moment(data['updated_at']).format('YYYY-MM-DD') == currentDate) {
                        $('td', row).addClass('table-success');
                    }
                }
            });

            /* for advanced filter */
            $("#school-name").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#grade").on('change', function(e) {
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

            $("#roles").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#start_joined_date").on('change', function (e) {
                var value = $(e.currentTarget).val();
                table.draw();
            })

            $("#end_joined_date").on('change', function (e) {
                var value = $(e.currentTarget).val();
                table.draw();
            })

            // Add a click event listener to each row in the parent DataTable
            table.on('click', 'td.dt-control', function(e) {
                let tr = e.target.closest('tr');
                let row = table.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                } else {
                    // Open this row
                    var suggestion = row.data().suggestion;
                    if (suggestion !== null && suggestion !== undefined) {
                        var arrSuggest = suggestion.split(',');
                        var intArrSuggest = [];
                        for (var i = 0; i < arrSuggest.length; i++)
                            intArrSuggest.push(parseInt(arrSuggest[i]));

                        showLoading()
                        axios.get("{{ url('api/client/suggestion') }}", {
                                params: {
                                    clientIds: intArrSuggest,
                                    roleName: row.data().roles.toLowerCase()
                                }
                            })
                            .then(function(response) {
                                const data = response.data.data
                                row.child(format(row.data(), data)).show();

                                swal.close()
                            })
                            .catch(function(error) {
                                swal.close()
                                console.log(error);
                            })
                    } else {

                        row.child(format(row.data(), null)).show();
                    }
                }
            });

            $('#rawTable tbody').on('click', '.deleteRawClient ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('client/student/raw', data.id)
            });

            // Tooltip 
            $('#rawTable tbody').on('mouseover', 'tr', function() {
                $('[data-bs-toggle="tooltip"]').tooltip({
                    trigger: 'hover',
                    html: true
                });
            });

            function selectAll() {
                const check_number = $('input.editor-active').length;
                const checked_number = $('input.editor-active:checked').length;
                const uncheck_number = check_number - checked_number;

                $('input.editor-active').each(function() {
                    if (uncheck_number == check_number) {
                        $(this).prop('checked', true)
                        table.button(2).text('<i class="bi bi-x me-1"></i> Unselect All')
                    } else if (checked_number == check_number) {
                        $(this).prop('checked', false)
                        table.button(2).text('<i class="bi bi-check-square me-1"></i> Select All')
                    } else {
                        $(this).prop('checked', true)
                        table.button(2).text('<i class="bi bi-x me-1"></i> Unselect All')
                    }
                });
            }

        });

        function multipleDelete() {
            var selected = [];
            $('input.editor-active').each(function() {
                if ($(this).prop('checked')) {
                    selected.push($(this).data('id'));
                }
            });

            if (selected.length > 0) {
                Swal.fire({
                    title: "Confirmation!",
                    text: 'Are you sure to delete the raw data?',
                    showCancelButton: true,
                    confirmButtonText: "Yes",
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        showLoading();
                        var link = '{{ route('client.raw.bulk.destroy') }}';
                        axios.post(link, {
                                choosen: selected
                            })
                            .then(function(response) {
                                swal.close();
                                notification('success', response.data.message);
                                $("#rawTable").DataTable().ajax.reload()
                            })
                            .catch(function(error) {
                                swal.close();
                                notification('error', error.message);
                            })
                    }
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Please select the raw data first!",
                });
            }
        }

        function comparison(id, id2, roles) {
            $('input.item-' + id2).prop('checked', true);
            window.open("{{ url('client/') }}" + '/' + roles + '/raw/' + id + '/comparison/' + id2, "_blank");
        }

        function newLeads(id, roles) {
            $('input.item-' + id).prop('checked', true);
            window.open("{{ url('client/') }}" + '/' + roles + '/raw/' + id + '/new', "_blank");
        }
    </script>
@endpush
