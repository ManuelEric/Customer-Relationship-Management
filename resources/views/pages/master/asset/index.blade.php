@extends('layout.main')

@section('title', 'Asset')

@section('content')

    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Assets
            </h5>
            <a href="{{ url('master/asset/create') }}" class="btn btn-sm btn-info"><i class="bi bi-plus-square me-1"></i>
                Add
                Asset</a>
        </div>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="assetTable">
                <thead class="bg-secondary text-white">
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

    <script>
        $(document).ready(function() {

            var options = {
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
                        className: 'text-center',
                        render: function(data, type, row) {
                            let achieve_date = row.asset_dateachieved ? moment(row
                                .asset_dateachieved).format("MMMM Do YYYY") : '-'
                            return achieve_date
                        }
                    },
                    {
                        data: 'asset_amount',
                        className: 'text-center',
                    },
                    {
                        data: 'asset_unit',
                        className: 'text-center',
                    },
                    {
                        data: 'asset_condition',
                        className: 'text-center',
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
                        className: 'text-center',
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
                ],
                pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
            };

            var table = initializeDataTable('#assetTable', options, 'rt_asset');

            @php
                $privilage = $menus['Master']->where('submenu_name', 'Assets')->first();
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
