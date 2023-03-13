@extends('layout.main')

@section('title', 'Program - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Program
        </a>
        <a href="{{ url('master/program/create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-square me-1"></i> Add
            Program</a>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Program ID</th>
                        <th>Main Program</th>
                        <th>Sub Program</th>
                        <th>Program Name</th>
                        <th>Type</th>
                        <th>Payment Category</th>
                        <th>Need Tutor/Mentor</th>
                        <th>Scope</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="9"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Need Changing --}}
    <script>
        $(document).ready(function() {
            var table = $('#programTable').DataTable({
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
                    left: 2,
                    right: 1
                },
                processing: true,
                serverSide: true,
                ajax: '',
                columns: [{
                        data: 'prog_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'prog_id',
                    },
                    {
                        data: 'prog_main',
                    },
                    {
                        data: 'prog_sub',
                    },
                    {
                        data: 'prog_program',
                    },
                    {
                        data: 'prog_type',
                    },
                    {
                        data: 'prog_payment',
                    },
                    {
                        data: 'prog_mentor',
                    },
                    {
                        data: 'prog_scope',
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning editProgram"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteProgram"><i class="bi bi-trash2"></i></button>'
                    }
                ]
            });

            @php            
                $privilage = $menus['Master']->where('submenu_name', 'Program')->first();
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

            realtimeData(table)

            $('#programTable tbody').on('click', '.editProgram ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('master/program') }}/" + data.prog_id + '/edit';
            });

            $('#programTable tbody').on('click', '.deleteProgram ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/program', data.prog_id)
            });
        });
    </script>
@endsection
