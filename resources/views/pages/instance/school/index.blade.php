@extends('layout.main')

@section('title', 'Schools ')

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
            <a href="{{ url('instance/school/create') }}" class="btn btn-sm btn-info"><i class="bi bi-plus-square me-1"></i> Add
                School</a>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs flex-nowrap overflow-auto w-100 mb-3" style="overflow-y: hidden !important;">
                <li class="nav-item">
                    <a class="nav-link text-nowrap" aria-current="page" href="{{ url('instance/school/raw') }}">Raw
                        Data</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap active" aria-current="page" href="{{ url('instance/school') }}">School</a>
                </li>
            </ul>


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
                    left: window.matchMedia('(max-width: 767px)').matches ? 0 : 2,
                    right: 1
                },
                processing: true,
                serverSide: true,
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
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning editSchool"><i class="bi bi-eye"></i></button>'
                    }
                ]
            });

            realtimeData(table)

            $('#schoolTable tbody').on('click', '.editSchool ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('instance/school') }}/" + data.sch_id.toLowerCase();
            });
        });
    </script>
@endsection
