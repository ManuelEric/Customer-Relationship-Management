@extends('layout.main')

@section('title', 'Student - Bigdata Platform')

@section('css')
    <link rel="stylesheet" href="{{ asset('library/dashboard/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">
@endsection

@section('content')
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

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex align-items-center justify-content-between mb-3 mt-md-0 mt-1">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Student
        </a>
        <div>
            <a href="{{ url('api/download/excel-template/student') }}" class="btn btn-sm btn-outline-info btn-download"><i
                    class="bi bi-download"></i> <span class="ms-1">Download Template</span></a>
            <a href="javascript:void(0)" class="btn btn-sm btn-outline-info btn-import" data-bs-toggle="modal"
                data-bs-target="#importData"><i class="bi bi-cloud-upload"></i> <span class="ms-1">Import</span></a>
            <a href="{{ url('client/student/create') }}" class="btn btn-sm btn-primary"><i
                    class="bi bi-plus-square me-1"></i> Add Student</a>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs flex-nowrap mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ Request::get('st') == 'new-leads' ? 'active' : '' }}" aria-current="page"
                        href="{{ url('client/student?st=new-leads') }}">New Leads</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::get('st') == 'potential' ? 'active' : '' }}"
                        href="{{ url('client/student?st=potential') }}">Potential</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::get('st') == 'mentee' ? 'active' : '' }}"
                        href="{{ url('client/student?st=mentee') }}">Mentee</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::get('st') == 'non-mentee' ? 'active' : '' }}"
                        href="{{ url('client/student?st=non-mentee') }}">Non-Mentee</a>
                </li>
            </ul>


            <style>
                #clientTable tr td.danger {
                    background: rgb(255, 151, 151)
                }
            </style>
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="clientTable">
                <thead class="bg-dark text-white">
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
                        <th>Last Update</th>
                        <th>Status</th>
                        <th>Program Suggest</th>
                        <th>Status Lead</th>
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
                                <input type="file" name="file" id="" class="form-control form-control-sm">
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

    {{-- Need Changing --}}
    <script>
        var widthView = $(window).width();
        $(document).ready(function() {
            var table = $('#clientTable').DataTable({
                order: [
                    // [20, 'desc'],
                    [1, 'asc']
                ],
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
                    left: (widthView < 768) ? 1 : 2,
                    right: 1
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
                        data: 'mail',
                    },
                    {
                        data: 'phone',
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
                        defaultContent: '-'
                    },
                    {
                        data: 'school_name',
                        name: 'school_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'graduation_year',
                        defaultContent: '-'
                    },
                    {
                        data: 'grade_now',
                        defaultContent: '-',
                        render: function(data, type, row, meta) {
                            return data > 12 ? 'Not high school' : data;
                        }
                    },
                    {
                        data: 'insta',
                        defaultContent: '-'
                    },
                    {
                        data: 'address',
                        defaultContent: '-'
                    },
                    {
                        data: 'lead_source',
                        defaultContent: '-'
                    },
                    {
                        data: 'st_levelinterest',
                        defaultContent: '-'
                    },
                    {
                        data: 'interest_prog',
                        defaultContent: '-'
                    },
                    {
                        data: 'st_abryear',
                        defaultContent: '-'
                    },
                    {
                        data: 'abr_country',
                        defaultContent: '-'
                    },
                    {
                        data: 'dream_uni',
                        defaultContent: '-'
                    },
                    {
                        data: 'dream_major',
                        defaultContent: '-'
                    },
                    {
                        data: 'updated_at',
                    },
                    {
                        data: 'st_statusact',
                        render: function(data, type, row, meta) {
                            return data == 1 ? "Active" : "Non-active";
                        }
                    },
                    {
                        data: 'program_suggest',
                        
                    },
                    {
                        data: 'status_lead',
                        
                    },
                    // {
                    //     data: 'total_score',
                    //     className: 'text-primary text-center',
                    // },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning editClient"><i class="bi bi-eye"></i></button>'
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

            // $('#clientTable tbody').on('click', '.deleteClient ', function() {
            //     var data = table.row($(this).parents('tr')).data();
            //     confirmDelete('asset', data.asset_id)
            // });
        });
    </script>
@endsection
