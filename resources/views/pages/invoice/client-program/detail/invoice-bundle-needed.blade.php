<table class="table table-bordered table-hover nowrap align-middle w-100" id="listOfBundleProgram">
    <thead class="bg-secondary text-white">
        <tr>
            <th class="bg-info text-white">#</th>
            <th class="bg-info text-white">Client Name</th>
            <th>Bundle</th>
            <th>Bundle Id</th>
            {{-- <th>PIC</th> --}}
            <th class="bg-info text-white">Action</th>
        </tr>
    </thead>
    <tfoot class="bg-light text-white">
        <tr>
            <td colspan="4"></td>
        </tr>
    </tfoot>
</table>


{{-- Need Changing --}}
<script>
    var widthView = $(window).width();
    $(document).ready(function() {
        var table = $('#listOfBundleProgram').DataTable({
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
            search: {
                return: true
            },
            processing: true,
            serverSide: true,
            ajax: '',
            columns: [{
                    data: 'uuid',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'fullname',
                },
                {
                    data: 'program_name',
                    searchable: false,
                },
                {
                    data: 'uuid',
                    render: function(data, type, row, meta) {
                        return data.substring(0, 3).toUpperCase();
                    }
                },
                {   
                    data: 'uuid',
                    className: 'text-center',
                    render: function(data, type, row) {
                        var link = "{{ url('invoice/client-program/create') }}?bundle=" + row.uuid

                        return '<a href="' + link + '" class="btn btn-sm btn-outline-warning">' +
                        '<i class="bi bi-plus"></i> Invoice</a>'
                    }
                }
            ]
        })

    });
</script>
