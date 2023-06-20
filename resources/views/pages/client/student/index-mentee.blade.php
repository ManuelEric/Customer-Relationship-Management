@extends('layout.main')

@section('title', 'Mentee - Bigdata Platform')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Mentee
        </a>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs flex-nowrap mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ Request::get('st') == "mentee" ? "active" : null }}"
                        href="{{ url('client/alumni?st=mentee') }}">Mentee</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::get('st') == "non-mentee" ? 'active' : null }}"
                        href="{{ url('client/alumni?st=non-mentee') }}">Non Mentee</a>
                </li>
            </ul>
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="clientTable">
                <thead class="bg-dark text-white">
                    <tr class="text-center" role="row">
                        <th class="bg-info text-white">No</th>
                        <th class="bg-info text-white">Name</th>
                        {{--  <th>Mail</th>  --}}
                        {{--  <th>Phone</th>  --}}
                        <th>Parents Name</th>
                        {{--  <th>Parents Phone</th>  --}}
                        <th>School</th>
                        {{--  <th>Graduation Year</th>  --}}
                        <th>Grade</th>
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
                        orderable:true,
                        searchable:true,
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
                    /*
                    {
                        data: 'graduation_year',
                        defaultContent: '-'
                    },
                    */
                    {
                        data: 'st_grade',
                        defaultContent: '-'
                    },
                    /*
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

            @if($privilage['copy'] == 0)
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
                window.location.href = "{{ url('client/mentee') }}/" + data.id ;
            });
        });
    </script>
@endsection
