@extends('layout.main')

@section('title', 'Asset - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Asset
        </a>
        <a href="{{ url('master/asset/create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-square me-1"></i> Add
            Asset</a>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="assetTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Asset ID</th>
                        <th>Asset Name</th>
                        <th>Merk/Type</th>
                        <th>Achieved Date</th>
                        <th>Amont</th>
                        <th>Unit</th>
                        <th>Condition</th>
                        <th>Notes</th>
                        <th>Last Update</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="10"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    {{ json_encode($menus['Master']) }}
    @php
        exit;
    @endphp
    <script>
        $(document).ready(function() {
            var table = $('#assetTable').DataTable({
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
                        data: 'asset_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'asset_id',
                    },
                    {
                        data: 'asset_name',
                    },
                    {
                        data: 'asset_merktype',
                    },
                    {
                        data: 'asset_dateachieved',
                        render: function(data, type, row) {
                            let achieve_date = row.asset_dateachieved ? moment(row
                                .asset_dateachieved).format("MMMM Do YYYY") : '-'
                            return achieve_date
                        }
                    },
                    {
                        data: 'asset_amount',
                    },
                    {
                        data: 'asset_unit',
                    },
                    {
                        data: 'asset_condition',
                    },
                    {
                        data: 'asset_notes',
                        render: function(data, type, row) {
                            let notes = row.asset_notes
                            return notes
                        }
                    },
                    {
                        data: 'updated_at',
                        render: function(data, type, row) {
                            let lastupdate = row.asset_lastupdatedate !=
                                '0000-00-00' ? moment(row
                                    .asset_lastupdatedate).format("MMMM Do YYYY") : '-'
                            return lastupdate
                        }
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning ms-1 showDetail"><i class="bi bi-eye"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteAsset"><i class="bi bi-trash2"></i></button>'
                    }
                ]
                
                
            });

            @php         
                $privilage = $menus['Master']->where('submenu_name', 'Assets')->first();
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

            $('#assetTable tbody').on('click', '.editAsset ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('master/asset') }}/" + data.asset_id.toLowerCase() + '/edit';
            });

            $('#assetTable tbody').on('click', '.showDetail', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('master/asset') }}/" + data.asset_id.toLowerCase();
            });

            $('#assetTable tbody').on('click', '.deleteAsset ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/asset', data.asset_id)
            });
        });

    </script>
@endsection
