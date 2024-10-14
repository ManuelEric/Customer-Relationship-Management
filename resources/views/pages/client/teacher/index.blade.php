@extends('layout.main')

@section('title', 'List of Teacher')

@push('styles')
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
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <h5 class="text-white m-0">
                    <i class="bi bi-tag me-1"></i>
                    Teachers
                </h5>
            </div>
            <div class="col-md-4">
                <div class="row g-1">
                    {{-- <div class="col-md-4 col-7">
                        <a href="{{ url('api/download/excel-template/teacher') }}"
                            class="btn btn-sm btn-light text-info btn-download w-100"><i class="bi bi-download"></i> <span
                                class="ms-1">Template</span></a>
                    </div>
                    <div class="col-md-4 col-5">
                        <a href="javascript:void(0)" class="btn btn-sm btn-light text-info btn-import w-100"
                            data-bs-toggle="modal" data-bs-target="#importData"><i class="bi bi-cloud-upload"></i> <span
                                class="ms-1">Import</span></a>
                    </div> --}}
                    <div class="col-md-4 offset-lg-8">
                        <a href="{{ url('client/teacher-counselor/create') }}" class="btn btn-sm btn-info w-100"><i
                                class="bi bi-plus-square me-1"></i> Add
                            Teacher</a>
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
            <x-client.teacher.nav />

            <table class="table table-bordered table-hover nowrap align-middle w-100" id="clientTable">
                <thead class="bg-secondary text-white">
                    <tr class="text-center" role="row">
                        <th class="bg-info text-white">No</th>
                        <th class="bg-info text-white">Teacher Name</th>
                        <th>Teacher Email</th>
                        <th>Teacher Number</th>
                        <th>From</th>
                        <th class="bg-info text-white">Status</th>
                        <th class="bg-info text-white">#</th>
                    </tr>
                </thead>
                {{-- <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="7"></td>
                    </tr>
                </tfoot> --}}
            </table>
        </div>
    </div>

    <div class="modal fade" id="importData" tabindex="-1" aria-labelledby="importDataLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('teacher-counselor.import') }}" method="POST" enctype="multipart/form-data">
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

            var options = {
                order: [[1, 'asc']],
                buttons: [
                    'pageLength', {
                        extend: 'excel',
                        text: 'Export to Excel',
                    }
                ],
                fixedColumns: {
                    left: (widthView < 768) ? 1 : 2,
                    right: 1
                },
                ajax: '',
                pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
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
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'school_name',
                        name: 'school_name',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'st_statusact',
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
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning editClient" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="More Detail"><i class="bi bi-eye"></i></button>'
                    }
                ],
                createdRow: function(row, data, index) {
                    let currentDate = new Date().toJSON().slice(0, 10);
                    if (moment(data['created_at']).format('YYYY-MM-DD') == currentDate) {
                        $('td', row).addClass('table-success');
                    }
                }
            };

            var table = initializeDataTable('#clientTable', options, 'rt_client');

            @php
                $privilage = $menus['Client']->where('submenu_name', 'Teacher/Counselor')->first();
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

            // Change Active Status 
            $('#clientTable tbody').on('change', '.status ', function() {
                const data = table.row($(this).parents('tr')).data();
                const val = data.st_statusact == 1 ? 0 : 1;
                const link = "{{ url('/') }}/client/student/" + data.id + "/status/" + val

                axios.get(link)
                    .then(function(response) {
                        Swal.close()
                        notification("success", response.data.message)
                        table.ajax.reload(null, false)
                    })
                    .catch(function(error) {
                        Swal.close()
                        notification("error", error.response.data.message)
                    })
            });

            // View More 
            $('#clientTable tbody').on('click', '.editClient ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.open("{{ url('client/teacher-counselor') }}/" + data.id, "_blank");
            });

        });
    </script>
@endpush
