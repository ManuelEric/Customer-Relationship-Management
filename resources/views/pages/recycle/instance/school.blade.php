@extends('layout.main')

@section('title', 'Recycle - Schools')

@section('content')
    
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                School
            </h5>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">

            <table class="table table-bordered table-hover nowrap align-middle w-100" id="schoolTable">
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
                    }
                ],
                fixedColumns: {
                    left: window.matchMedia('(max-width: 767px)').matches ? 0 : 2,
                    right: 1
                },
                ajax: '',
                pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
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
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning restore"><i class="bi bi-arrow-counterclockwise"></i></button>'
                    }
                ]
            };

            var table = initializeDataTable('#schoolTable', options, 'rt_school');

            $('#schoolTable tbody').on('click', '.restore ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmRestore('restore/instance/school', data.sch_id)
            });
        });
    </script>
@endsection
