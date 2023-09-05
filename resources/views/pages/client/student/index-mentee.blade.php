@extends('layout.main')

@section('title', 'Alumni ')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="row align-items-center justify-content-between">
            <div class="col-md-6">
                <h5 class="text-white m-0">
                    <i class="bi bi-tag me-1"></i>
                    Alumni
                </h5>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <div class="dropdown">
                    <button href="#" class="btn btn-sm btn-light text-dark dropdown-toggle" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" id="filter">
                        <i class="bi bi-funnel me-2"></i> Filter
                    </button>
                    <form action="" class="dropdown-menu dropdown-menu-end pt-0 shadow" style="width: 400px;"
                        id="advanced-filter">
                        <div class="dropdown-header bg-info text-dark py-2 d-flex justify-content-between">
                            Advanced Filter
                            <i class="bi bi-search"></i>
                        </div>
                        <div class="row p-3">
                            <div class="col-md-12 mb-2">
                                <label for="">School Name</label>
                                <select name="school_name[]" class="select form-select form-select-sm w-100" multiple
                                    id="school-name">

                                </select>
                            </div>

                            <div class="col-md-12 mb-2">
                                <label for="">Graduation Year</label>
                                <select name="graduation_year[]" class="select form-select form-select-sm w-100" multiple
                                    id="graduation-year">

                                </select>
                            </div>

                            <div class="col-md-12 mt-3">
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
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs flex-nowrap mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ Request::get('st') == 'mentee' ? 'active' : null }}"
                        href="{{ url('client/alumni?st=mentee') }}">Mentee</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::get('st') == 'non-mentee' ? 'active' : null }}"
                        href="{{ url('client/alumni?st=non-mentee') }}">Non Mentee</a>
                </li>
            </ul>
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="clientTable">
                <thead class="bg-secondary text-white">
                    <tr class="text-center" role="row">
                        <th class="bg-info text-white">No</th>
                        <th class="bg-info text-white">Name</th>
                        {{--  <th>Mail</th>  --}}
                        {{--  <th>Phone</th>  --}}
                        <th>Parents Name</th>
                        {{--  <th>Parents Phone</th>  --}}
                        <th>School</th>
                        <th>Graduation Year</th>
                        {{-- <th>Grade</th> --}}
                        {{--  <th>Instagram</th>  --}}
                        {{--  <th>Location</th>  --}}
                        {{--  <th>Lead</th>  --}}
                        {{--  <th>Level of Interest</th>  --}}
                        {{--  <th>Interested Program</th>  --}}
                        {{-- <th>Success Program</th>
                        <th>Mentor/Tutor</th> --}}
                        {{--  <th>Year of Study Abroad</th>  --}}
                        {{--  <th>Country of Study Abroad</th>  --}}
                        {{--  <th>University Destination</th>  --}}
                        {{--  <th>Interest Major</th>  --}}
                        {{--  <th>Last Update</th>  --}}
                        {{--  <th>Status</th>  --}}
                        {{-- <th class="bg-info text-white">Score</th> --}}
                        <th class="bg-info text-white"># Action</th>
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
        $('#cancel').click(function() {
            $(this).parents('.dropdown').find('button.dropdown-toggle').dropdown('toggle')
        });

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
                    /*
                    {
                        data: 'mail',
                    },
                    
                    {
                        data: 'phone',
                    },
                    */
                    {
                        data: 'parent_name',
                        name: 'parent_name',
                        defaultContent: '-',
                        orderable: true,
                        searchable: true,
                    },
                    /*
                    {
                        data: 'parent_phone',
                        name: 'parent_phone',
                        defaultContent: '-'
                    },
                    */
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
                    /*
                    {
                        data: 'st_grade',
                        defaultContent: '-'
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
                    */
                    // {
                    //     data: 'total_score',
                    //     className: 'text-primary',
                    // },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning editClient"><i class="bi bi-eye"></i></button>'
                    }
                ],
                createdRow: function(row, data, index) {
                    // temporary condition
                    // will change soon
                    if (data['st_statusact'] == 0) {
                        $('td', row).addClass('text-danger');
                        $('td:nth-last-child(1) .deleteUser', row).addClass('d-none');
                        // $('td:nth-last-child(2)', row).addClass('bg-danger rounded text-white my-2');
                    }
                }
            });

            @php
                $privilage = $menus['Client']->where('submenu_name', 'Alumnis')->first();
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
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                const type = urlParams.get('st');


                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('client/alumni/') }}/" + type + "/" + data.id;
            });
        });
    </script>
@endsection
