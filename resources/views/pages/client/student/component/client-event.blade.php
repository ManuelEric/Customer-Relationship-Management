<div class="card rounded">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h5 class="m-0 py-2">Events</h5>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover nowrap align-middle w-100" id="eventTable">
            <thead class="bg-secondary text-white">
                <tr class="text-center" role="row">
                    <th class="bg-info text-white">No</th>
                    <th class="bg-info text-white">Event Name</th>
                    <th>Event Start Date</th>
                    <th>Joined Date</th>
                </tr>
            </thead>
            <tfoot class="bg-light text-white">
                <tr>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        var table_event = $('#eventTable').DataTable({
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
                right: 0
            },
            processing: true,
            serverSide: true,
            ajax: '{{ url('api/client/' . $student->id . '/events') }}',
            columns: [{
                    data: 'clientevent_id',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'event_name',
                    name: 'tbl_events.event_title'
                },
                {
                    data: 'event_startdate',
                    name: 'tbl_events.event_startdate',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return moment(data).format('DD MMMM YYYY HH:mm:ss')
                    }
                },
                {
                    data: 'joined_date',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return moment(data).format('DD MMMM YYYY')
                    }
                },
            ]
        });
    });
</script>
@endpush