@extends('layout.main')

@section('title', 'Raw Parents Data')

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
                    Parent
                </h5>
            </div>
            <div class="col-md-4">
                <div class="row g-1">
                    {{-- <div class="col-md-4 col-8">
                        <a href="{{ url('api/download/excel-template/parent') }}"
                            class="btn btn-sm btn-light text-info btn-download w-100"><i class="bi bi-download"></i> <span
                                class="ms-1">Template</span></a>
                    </div>
                    <div class="col-md-4 col-4">
                        <a href="javascript:void(0)" class="btn btn-sm btn-light text-info btn-import w-100"
                            data-bs-toggle="modal" data-bs-target="#importData"><i class="bi bi-cloud-upload"></i> <span
                                class="ms-1">Import</span></a>
                    </div> --}}
                    <div class="col-md-4 offset-8">
                        <a href="{{ url('client/parent/create') }}" class="btn btn-sm btn-info w-100"><i
                                class="bi bi-plus-square me-1"></i> Add
                            Parent</a>
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
            <x-client.parent.nav />

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
                            <th class="bg-info text-white">Parents Name</th>
                            <th class="bg-info text-white">Suggestion</th>
                            <th>Parents Email</th>
                            <th>Parents Phone</th>
                            <th>Child Name</th>
                            <th>School Name</th>
                            <th>Graduation Year</th>
                            <th>Country of Study Aboard</th>
                            <th>Joined Event</th>
                            <th>Interest Program</th>
                            <th>Joined Date</th>
                            <th class="bg-info text-white">Last Updated</th>
                            <th class="bg-info text-white">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importData" tabindex="-1" aria-labelledby="importDataLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('parent.import') }}" method="POST" enctype="multipart/form-data">
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
                                <input type="file" name="file" id="" class="form-control form-control-sm">
                            </div>
                            <small class="text-warning mt-3">
                                * Please clean the file first, before importing the csv file. <br>
                                You can download the csv template <a
                                    href="{{ url('api/download/excel-template/parent') }}">here</a>
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
                var childrens = '';
                var suggestion = d.suggestion;
                var arrSuggest = [];
                if (suggestion !== null && suggestion !== undefined) {
                    arrSuggest = suggestion.split(',');
                }

                if (arrSuggest.length > 0) {
                    similar +=
                        '<th colspan=6>Comparison with Similar Names:</th>' +
                        '</tr>' +
                        '<tr>' +
                        '<th>#</th><th>Name</th><th>Email</th><th>Phone Number</th><th>Child Name</th>' +
                        '</tr>';

                    clientSuggest.forEach(function(item, index) {
                        childrens = '';
                        if (item.childrens.length > 0) {
                            item.childrens.forEach(function(children, index) {
                                childrens += children.first_name + (children.last_name !== null ?
                                    ' ' + children.last_name : '');
                                (item.childrens.length !== index + 1 ? childrens += ', ' : '')
                            })
                        }

                        similar += '<tr onclick="comparison(' +
                            d.id + ',' + item.id + ')" class="cursor-pointer">' +
                            '<td><input type="radio" name="similar' + d.id +
                            '" class="form-check-input item-' + item.id + '" onclick="comparison(' +
                            d.id + ',' + item.id + ')" /></td>' +
                            '<td><i class="bi bi-person me-1"></i>' + item.first_name + ' ' + (item.last_name !== null ? item.last_name :
                                '') + '</td>' +
                            '<td>' + (item.mail !== null ? item.mail : '-') + '</td>' +
                            '<td>' + (item.phone !== null ? item.phone : '-') + '</td>' +
                            '<td>' +
                            (item.childrens.length > 0 ?
                                childrens :
                                '-') + '</td>' +
                            '</tr>'

                    })

                }

                similar += '</table>'
                // `d` is the original data object for the row
                return (similar);
            }

            var options = {
                order: [
                    // [20, 'desc'],
                    [13, 'desc']
                ],
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
                    left: (widthView < 768) ? 1 : 2,
                    right: 1
                },
                ajax: {
                    url: '',
                },
                rowCallback: function(row, data) {
                    if (data.suggestion) {
                        $('td:eq(0)', row).addClass('dt-control');
                    }
                },
                columns: [{
                        orderable: false,
                        data: null,
                        defaultContent: ''
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
                        data: 'mail',
                        defaultContent: '-'
                    },
                    {
                        data: 'phone',
                        defaultContent: '-'
                    },
                    {
                        data: 'second_client_name',
                        defaultContent: '-',
                        render: function (data, type, row, meta) {
                            if (row.second_client_statusact == 0)
                                return '<a target="_blank" href="{{ url('/') }}/client/student/' + row.second_client_id + '" class="text-decoration-none text-danger" title="You can\'t see this student in new leads tab cause he/she is inactive">' + data + '&nbsp;<i class="bi bi-info-circle"></i></a>';

                            return data;
                        }
                    },
                    {
                        data: 'second_school_name',
                        defaultContent: '-',
                        render: function(data, type, row, meta) {
                            if (data != null) {
                                if (row.is_verifiedschool == 'Y') {
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
                        data: 'second_client_graduation_year_real',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'second_client_interest_countries',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'second_client_joined_event',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'second_client_interest_prog',
                        className: 'text-center',
                        defaultContent: '-'
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
                            return '<div class="d-flex gap-1 justify-content-center">' +
                                '<small class="btn btn-sm btn-info px-1 pt-1 pb-0  cursor-pointer item-' +
                                row
                                .id +
                                '" data-bs-toggle="tooltip" data-bs-placement="top" ' +
                                'data-bs-custom-class="custom-tooltip" ' +
                                'data-bs-title="Convert to New Lead" onclick="newLeads(' +
                                row.id + ')">' +
                                '<i class="bi bi-send-check-fill text-secondary me-1"></i>' +
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
                    if (moment(data['created_at']).format('YYYY-MM-DD') == currentDate) {
                        $('td', row).addClass('table-success');
                    }
                }
            };

            var table = initializeDataTable('#rawTable', options, 'rt_client');

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
                                    roleName: 'parent'
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
                confirmDelete('client/parent/raw', data.id)
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

        function comparison(id, id2) {
            $('input.item-' + id2).prop('checked', true);
            window.open("{{ url('client/parent/raw/') }}" + '/' + id + '/comparison/' + id2, "_blank");
        }

        function newLeads(id) {
            $('input.item-' + id).prop('checked', true);
            window.open("{{ url('client/parent/raw/') }}" + '/' + id + '/new', "_blank");
        }
    </script>
@endpush
