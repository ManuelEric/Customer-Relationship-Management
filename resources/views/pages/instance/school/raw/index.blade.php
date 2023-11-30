@extends('layout.main')

@section('title', 'Raw Data School')

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

        td.dt-control {
            background: url('http://www.datatables.net/examples/resources/details_open.png') no-repeat center center;
            cursor: pointer;
        }

        tr.shown td.dt-control {
            background: url('http://www.datatables.net/examples/resources/details_close.png') no-repeat center center;
        }
    </style>
@endpush

@section('content')

    @if ($duplicates_schools_string)
    <div class="alert alert-warning">
            
        <p><i class="bi bi-exclamation-triangle"></i>
            Please review the school data and make any necessary updates. There appear to be a few duplicate entries.<br><br>
            Such as : <b>{{ $duplicates_schools_string }}</b>
        </p>
    </div>
    @endif

    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                School
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
                    <a class="nav-link text-nowrap active" aria-current="page" href="{{ url('instance/school/raw') }}">Raw
                        Data</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap" aria-current="page" href="{{ url('instance/school') }}">School</a>
                </li>
            </ul>


            <style>
                #clientTable tr td.danger {
                    background: rgb(255, 151, 151)
                }
            </style>
            <div class="table-responsive">
                <table class="table table-bordered table-hover nowrap align-middle w-100" id="rawTable">
                    <thead class="bg-secondary text-white">
                        <tr>
                            <th class="bg-info text-white">#</th>
                            <th class="bg-info text-white">School Name</th>
                            <th>Type</th>
                            <th>Curriculum</th>
                            <th>City</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th class="bg-info text-white">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Convert to New School  --}}
    <div class="modal fade" id="newSchool" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
        aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="#" method="POST" id="form-convert">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="sch_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">Convert to New School</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="">School Name</label>
                            <input type="text" class="form-control form-control-sm" name="sch_name">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="">Type</label>
                                <select name="sch_type" id="typeSelect" class="w-100">
                                    <option value=""></option>
                                    <option value="International">
                                        International</option>
                                    <option value="National">
                                        National
                                    </option>
                                    <option value="National_plus">
                                        National+</option>
                                    <option value="National_private">
                                        National Private</option>
                                    <option value="Home_schooling">
                                        Home Schooling</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="">Target</label>
                                <select name="sch_target" id="targetSelect" class="w-100">
                                    <option value=""></option>
                                    <option value="7">
                                        Up Market
                                    </option>
                                    <option value="5">
                                        Mid Market
                                    </option>
                                    <option value="3">
                                        Low Market
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="">Address</label>
                            <textarea name="sch_location" id="locationText" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-sm btn-primary">Convert</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- End Convert to School  --}}

    {{-- Convert to Alias  --}}
    <div class="modal fade" id="aliasSchool" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <form action="#" method="POST" id="form-alias">
                    @csrf
                    <input type="hidden" name="is_convert" value="true">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">Convert to School Alias</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="">Alias Name</label>
                            <input type="text" name="alias" class="form-control form-control-sm">
                        </div>
                        <div class="">
                            <label for="">School Name</label>
                            <input type="hidden" name="raw_sch_id" id="school_id">
                            <select class="w-100" name="school" id="schoolSelect">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-sm btn-primary">Convert</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- End Convert to Alias --}}
@endsection

@push('scripts')
    <script>
        $("#targetSelect").select2({
            placeholder: "Select value",
            allowClear: true,
            dropdownParent: $('#newSchool .modal-content')
        });

        $("#typeSelect").select2({
            placeholder: "Select value",
            allowClear: true,
            dropdownParent: $('#newSchool .modal-content')
        });

        $("#schoolSelect").select2({
            placeholder: "Select value",
            allowClear: true,
            dropdownParent: $('#aliasSchool .modal-content')
        });

        var widthView = $(window).width();
        $(document).ready(function() {

            // Formatting function for row details - modify as you need
            function format(d) {
                var similar = '<table class="table w-auto table-hover">'
                similar +=
                    '<th colspan=6>Comparison with Similar Names:</th>' +
                    '</tr>' +
                    '<tr>' +
                    '<th>#</th><th>Name</th><th>Email</th><th>Phone Number</th><th>Child Name</th>' +
                    '</tr>';

                for (let i = 1; i <= 3; i++) {
                    similar += '<tr onclick="comparison(' +
                        1 + ',' + 2 + ')" class="cursor-pointer">' +
                        '<td><input type="radio" name="similar' + 1 +
                        '" class="form-check-input item-' + 2 + '" onclick="comparison(' +
                        1 + ',' + 2 + ')" /></td>' +
                        '<td>' + 'Name' + '</td>' +
                        '<td>' + 'Email' + '</td>' +
                        '<td>' + 'Phone Number' + '</td>' +
                        '<td>' + 'Child Name' + '</td>' +
                        '</tr>'
                };

                similar +=
                    '<tr>' +
                    '<th colspan=6>Convert without Comparison</th>' +
                    '</tr>' +
                    '<tr class="cursor-pointer" onclick="newLeads(' +
                    1 + ')">' +
                    '<td><input type="radio" name="similar' + 1 +
                    '" class="form-check-input item-' + 1 + '" onclick="newLeads(' +
                    1 + ')" /></td>' +
                    '<td colspan=5>New Student</td>' +
                    '</tr>' +
                    '</table>'
                // `d` is the original data object for the row
                return (similar);
            }

            var table = $('#rawTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        text: '<i class="bi bi-check-square me-1"></i> Select All',
                        action: function(e, dt, node, config) {
                            selectAll(true);
                        }
                    },
                    {
                        text: '<i class="bi bi-x-square  me-1"></i> Select None',
                        action: function(e, dt, node, config) {
                            selectAll(false);
                        }
                    },
                    {
                        text: '<i class="bi bi-trash-fill me-1"></i> Delete',
                        action: function(e, dt, node, config) {
                            multipleDelete();
                        }
                    }
                ],
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
                ],
                scrollX: true,
                fixedColumns: {
                    left: window.matchMedia('(max-width: 767px)').matches ? 0 : 2,
                    // right: 1
                },
                processing: true,
                serverSide: true,
                ajax: '',
                pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
                columns: [{
                        data: 'sch_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return '<input type="checkbox" class="editor-active cursor-pointer" data-id="'+ row.sch_id +'">'
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
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return data == 1 ? 'Active' : 'Inactive';
                        }
                    },
                    {
                        data: 'sch_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return '<div class="btn-group">' +
                                        '<p class="dropdown-toggle cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false"> Action </p>' +
                                        '<ul class="dropdown-menu" style="font-size: 12px">' +
                                            '<li>' +
                                                '<div class="dropdown-item cursor-pointer" data-id="'+ row.sch_id +'" data-school="'+ row.sch_name +'" data-type="'+row.sch_type_text+'" data-target="'+ row.sch_score +'" data-address="'+ row.sch_location +'" onclick="convertNewSchool(this)">' +
                                                    '<i class="bi bi-plus-circle-dotted me-1"></i> Convert to New School' +
                                                '</div>' +
                                            '</li>' +
                                            '<li>' +
                                                '<div class="dropdown-item cursor-pointer" data-id="'+ row.sch_id +'" data-name="'+ row.sch_name +'" onclick="convertAliasSchool(this)">' +
                                                    '<i class="bi bi-bookmark-plus me-1"></i> Convert to Alias' +
                                                '</div>' +
                                            '</li>' +
                                            '<li>' +
                                                '<div class="dropdown-item text-danger cursor-pointer" onclick="confirmDelete(\'instance/school/raw\', \''+ row.sch_id + '\')">' +
                                                    '<i class="bi bi-trash me-1"></i> Delete' +
                                                '</div>' +
                                            '</li>' +
                                        '</ul>' +
                                    '</div>';
                        }
                    }
                ]
            });

            // Add a click event listener to each row in the parent DataTable
            table.on('click', 'td.dt-control', function(e) {
                let tr = e.target.closest('tr');
                let row = table.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                }
            });

        });

        function selectAll(condition) {
            if (condition) {
                $('input.editor-active').each(function() {
                    $(this).prop('checked', true)
                })
            } else {
                $('input.editor-active').each(function() {
                    $(this).prop('checked', false)
                })
            }
        }

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
                    confirmButtonText: "Yup",
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        showLoading();
                        var link = '{{ route("school.raw.bulk.destroy") }}';
                        axios.post(link, {
                            choosen : selected
                        })
                        .then(function (response) {
                            swal.close();
                            notification('success', response.data.message);
                            $("#rawTable").DataTable().ajax.reload()
                        })
                        .catch(function (error) {
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

        function convertNewSchool(item) {
            const id = $(item).data('id');
            const school = $(item).data('school');
            const type = $(item).data('type');
            const target = $(item).data('target');
            const address = $(item).data('address');

            // fill the form
            $("input[name=sch_id]").val(id);
            $("input[name=sch_name]").val(school);
            $("#typeSelect").val(type).trigger('change');
            $("#targetSelect").val(target).trigger('change');
            if (address)
                tinymce.get('locationText').setContent(address)

            // set url from javascript
            var url = '{{ url('/') }}/instance/school/raw/' + id;
            $("#form-convert").prop('action', url);

            $('#newSchool').modal('show')
            
        }

        function convertAliasSchool(item) {
            const id = $(item).data('id')
            const alias = $(item).data('name');

            $("input[name=alias]").val(alias);
            $("#school_id").val(id);

            var url = '{{ url('/') }}/instance/school/' + id + '/alias';
            $("#form-alias").prop('action', url);

            syncSchool();
            $('#aliasSchool').modal('show')
        }

        function syncSchool() {
            showLoading();
            axios.get("{{ url('api/instance/school') }}")
                .then(function(response) {
                    const data = response.data.data
                    $('#schoolSelect').html('')
                    $('#schoolSelect').append('<option value=""></option>')
                    data.forEach(element => {
                        $('#schoolSelect').append(
                            '<option data-id="' + element.sch_id + '" value="' + element.sch_id + '">' +
                            element.sch_name + '</option>'
                        )
                    });
                    swal.close()
                })
                .catch(function(error) {
                    swal.close()
                    notification('error', error.message);
                })
        }
    </script>
@endpush
