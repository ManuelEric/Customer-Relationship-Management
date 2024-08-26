@extends('layout.main')

@section('title', 'List of School')

@section('content')

    @if ($duplicates_schools_string)
        <div class="alert alert-warning">

            <p><i class="bi bi-exclamation-triangle"></i>
                Please review the school data and make any necessary updates. There appear to be a few duplicate
                entries.<br><br>
                Such as : <b>{{ $duplicates_schools_string }}</b>
            </p>
        </div>
    @endif

    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Schools
            </h5>
            <a href="{{ url('instance/school/create') }}" class="btn btn-sm btn-info"><i class="bi bi-plus-square me-1"></i>
                Add
                School</a>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs flex-nowrap overflow-auto w-100 mb-3" style="overflow-y: hidden !important;">
                <li class="nav-item">
                    <a class="nav-link text-nowrap" aria-current="page" href="{{ url('instance/school/raw') }}">Raw
                        Data</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap active" aria-current="page"
                        href="{{ url('instance/school') }}">School</a>
                </li>
            </ul>


            <table class="table table-bordered table-hover nowrap align-middle w-100" id="schoolTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">School Name</th>
                        <th>Type</th>
                        <th>Curriculum</th>
                        <th>City</th>
                        <th>Location</th>
                        <th class="bg-info text-white">Status</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="6"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Need Changing --}}
    <script>
        $(document).ready(function() {
            var options = {
                order: [[1, 'asc']],
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
                fixedColumns: {
                    left: window.matchMedia('(max-width: 767px)').matches ? 0 : 2,
                    right: 2
                },
                ajax: '',
                pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
                columns: [{
                        data: 'sch_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return '<input type="checkbox" class="editor-active cursor-pointer" data-id="' +
                                row.sch_id + '">'
                        }
                    },
                    {
                        data: 'sch_name',
                    },
                    {
                        data: 'sch_type_text',
                    },
                    {
                        data: 'curriculum',
                        name: 'curriculum'
                    },
                    {
                        data: 'sch_city',
                    },
                    {
                        data: 'sch_location',
                        type: 'html'
                    },
                    {
                        data: 'status',
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            const status = data == 1 ? "checked" : "";
                            const content = '<div class="form-check form-switch m-0 p-0">' +
                                '<input class="form-check-input status" style="margin-left:2em" type="checkbox" role="switch" id="status-' +
                                row.id + '" ' + status + '>' +
                                '</div>'
                            return content;
                        }
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '',
                        render: function(data, type, row, meta) {
                            return '<div class="d-flex gap-1 justify-content-center">' +
                                '<small data-bs-toggle="tooltip" data-bs-placement="top" ' +
                                'data-bs-custom-class="custom-tooltip" ' +
                                'data-bs-title="Delete" class="btn btn-sm btn-outline-danger cursor-pointer" onclick="confirmDelete(\'instance/school\', \'' +
                                row.sch_id + '\')">' +
                                '<i class="bi bi-trash"></i>' +
                                '</small>' +
                                '<small class="btn btn-sm btn-outline-warning cursor-pointer editSchool" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="More Detail"><i class="bi bi-eye"></i></small>' +
                                '</div>';
                        }
                    },
                ]
            };

            var table = initializeDataTable('#schoolTable', options, 'rt_school');

            $('#schoolTable tbody').on('click', '.editSchool ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.open("{{ url('instance/school') }}/" + data.sch_id.toLowerCase(), "_blank");
            });

            // Tooltip 
            $('#schoolTable tbody').on('mouseover', 'tr', function() {
                $('[data-bs-toggle="tooltip"]').tooltip({
                    trigger: 'hover',
                    html: true
                });
            });

            // Change Active Status 
            $('#schoolTable tbody').on('change', '.status ', function() {
                const data = table.row($(this).parents('tr')).data();
                const val = data.status == 1 ? 0 : 1;
                alert('Belom ada function')
                // const link = "{{ url('/') }}/client/student/" + data.id + "/status/" + val

                // axios.get(link)
                //     .then(function(response) {
                //         Swal.close()
                //         notification("success", response.data.message)
                //     })
                //     .catch(function(error) {
                //         Swal.close()
                //         notification("error", error.response.data.message)
                //     })
                // table.ajax.reload(null, false)
            });

            // Select All 
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
                    text: 'Are you sure to delete the school?',
                    showCancelButton: true,
                    confirmButtonText: "Yes",
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        showLoading();
                        var link = '{{ route('school.raw.bulk.destroy') }}';
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
                    text: "Please select the school first!",
                });
            }
        }
    </script>
@endsection
