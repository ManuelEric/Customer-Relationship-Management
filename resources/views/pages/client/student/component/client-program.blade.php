<div class="card rounded">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h5 class="m-0 p-0">Programs</h5>
        </div>
        <div class="">
            <a href="{{ route('student.program.create', ['student' => $student->id]) }}"
                class="btn btn-sm btn-primary">Add Program</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
            <thead class="bg-secondary text-white">
                <tr class="text-center" role="row">
                    <th class="bg-info text-white">No</th>
                    <th class="bg-info text-white">Program Name</th>
                    <th>Conversion Lead</th>
                    <th>First Discuss</th>
                    <th>PIC</th>
                    <th>Program Status</th>
                    <th>Running Status</th>
                    <th>#</th>
                </tr>
            </thead>
            <tfoot class="bg-light text-white">
                <tr>
                    <td colspan="8"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts')
    <script>
        var url = "{{ url('client/student') . '/' . $student->id . '/program' }}"
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
                    left: 0,
                    right: 0
                },
                processing: true,
                serverSide: true,
                ajax: '{{ url('api/client/' . $student->id . '/programs') }}',
                columns: [{
                        data: 'prog_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'program_name',
                    },
                    {
                        data: 'conversion_lead',
                        className: 'text-center',
                    },
                    {
                        data: 'first_discuss_date',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return data ? moment(data).format("MMMM Do YYYY") : '-'
                        }
                    },
                    {
                        data: 'pic_name',
                        className: 'text-center',
                    },
                    {
                        data: 'program_status',
                        className: 'text-center',
                    },
                    {
                        data: 'prog_running_status',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            switch (parseInt(data)) {
                                case 0:
                                    return "Not yet"
                                    break;

                                case 1:
                                    return "Ongoing"
                                    break;

                                case 2:
                                    return "Done"
                                    break;
                            }
                        }

                    },
                    {
                        data: 'clientprog_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return '<a href="' + url + '/' + data +
                                '" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="More Detail" target="_blank"><i class="bi bi-info-circle"></i></a>'
                        }
                    }
                ]
            });

            // Tooltip 
            $('#programTable tbody').on('mouseover', 'tr', function() {
                $('[data-bs-toggle="tooltip"]').tooltip({
                    trigger: 'hover',
                    html: true
                });
            });
        });
    </script>
@endpush
