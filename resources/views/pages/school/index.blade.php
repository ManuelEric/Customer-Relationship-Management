@extends('layout.main')

@section('title', 'School - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> School
        </a>
        <a href="{{ url('instance/school/create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-square me-1"></i> Add
            School</a>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="schoolTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="text-dark">#</th>
                        <th class="bg-info text-white">School Name</th>
                        <th class="bg-info text-white">Type</th>
                        <th>Curriculum</th>
                        <th>City</th>
                        <th>Location</th>
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
            var table = $('#schoolTable').DataTable({
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
                        data: 'sch_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'sch_name',
                    },
                    {
                        data: 'sch_curriculum',
                    },
                    {
                        data: 'sch_type',
                    },
                    {
                        data: 'sch_city',
                    },
                    {
                        data: 'sch_location',
                        type: 'html'
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-warning editSchool"><i class="bi bi-eye"></i></button>'
                    }
                ]
            });

            realtimeData(table)

            $('#schoolTable tbody').on('click', '.editSchool ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('instance/school') }}/" + data.sch_id;
            });
        });
    </script>
@endsection
