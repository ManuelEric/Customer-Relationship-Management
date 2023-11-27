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
                    <tbody>
                        @for ($i = 0; $i < 10; $i++)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="editor-active cursor-pointer"
                                        data-id="{{ $i }}">
                                </td>
                                <td>Lorem</td>
                                <td>Lorem</td>
                                <td>Lorem</td>
                                <td>Lorem</td>
                                <td>Lorem</td>
                                <td>Lorem</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <p class="dropdown-toggle cursor-pointer" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            Action
                                        </p>
                                        <ul class="dropdown-menu" style="font-size: 12px">
                                            <li>
                                                <div class="dropdown-item cursor-pointer" data-id="id" data-school="name"
                                                    data-type="type" data-curriculum="2" onclick="convertNewSchool(this)">
                                                    <i class="bi bi-plus-circle-dotted me-1"></i> Convert to New School
                                                </div>
                                            </li>
                                            <li>
                                                <div class="dropdown-item cursor-pointer" data-id="id" data-name="name"
                                                    onclick="convertAliasSchool(this)">
                                                    <i class="bi bi-bookmark-plus me-1"></i> Convert to Alias
                                                </div>
                                            </li>
                                            <li>
                                                <div class="dropdown-item text-danger cursor-pointer"
                                                    onclick="confirmDelete('raw-school', 1)">
                                                    <i class="bi bi-trash me-1"></i> Delete
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Convert to New School  --}}
    <div class="modal fade" id="newSchool" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
        aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">Convert to New School</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="">School Name</label>
                            <input type="text" class="form-control form-control-sm">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="">Type</label>
                                <select name="" id="typeSelect" class="w-100">
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
                                <select name="" id="targetSelect" class="w-100">
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
                            <textarea name="" id="" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-sm btn-primary">Convert</button>
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
                <form action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">Convert to School Alias</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="">Alias Name</label>
                            <input type="text" name="" id="school_name" class="form-control form-control-sm">
                        </div>
                        <div class="">
                            <label for="">School Name</label>
                            <input type="hidden" name="" id="school_id">
                            <select class="w-100" name="school" id="schoolSelect">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-sm btn-primary">Convert</button>
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
                        // function for multiple delete 
                        Swal.fire("Saved!", "", "success");
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
            const school = $(item).data('school')

            $('#newSchool').modal('show')
            console.log(school);
        }

        function convertAliasSchool(item) {
            const id = $(item).data('id')

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
                            '<option data-id="' + element.sch_id + '" value="' + element.sch_name + '">' +
                            element.sch_name + '</option>'
                        )
                    });
                    swal.close()
                })
                .catch(function(error) {
                    swal.close()
                    console.log(error);
                })
        }
    </script>
@endpush
