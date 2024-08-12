@extends('layout.main')

@section('title', 'Recycle - Parent ')
@push('styles')
    <style>
        .btn-download span,
        .btn-import span {
            display: none;
        }

        .btn-download:hover>span,
        .btn-import:hover>span {
            display: inline-block;
        }
    </style>
@endpush

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <h5 class="text-white m-0">
                    <i class="bi bi-tag me-1"></i>
                    Parents
                </h5>
            </div>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="clientTable">
                <thead class="bg-secondary text-white">
                    <tr class="text-center" role="row">
                        <th class="bg-info text-white">No</th>
                        <th class="bg-info text-white">Parents Name</th>
                        <th>Parents Email</th>
                        <th>Parents Number</th>
                        <th>Birthday</th>
                        <th>Childs Name</th>
                        <th class="bg-info text-white">#</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="7"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    var widthView = $(window).width();
    $(document).ready(function() {
        var options = {
            buttons: [
                'pageLength', {
                    extend: 'excel',
                    text: 'Export to Excel',
                }
            ],
            fixedColumns: {
                left: (widthView < 768) ? 1 : 2,
                right: 1
            },
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
                        return data;
                    }
                },
                {
                    data: 'mail',
                    defaultContent: '-'
                },
                {
                    data: 'phone',
                    defaultContent: '-'
                },
                {
                    data: 'dob',
                    defaultContent: '-'
                },
                {
                    data: 'children_name',
                    name: 'children_name',
                    defaultContent: '-',
                    orderable: true,
                    searchable: true,
                },
                {
                    data: '',
                    className: 'text-center',
                    defaultContent: '<button type="button" class="btn btn-sm btn-outline-success editClient"><i class="bi bi-arrow-counterclockwise"></i></button>'
                }
            ],
        };

        var table = initializeDataTable('#clientTable', options, 'rt_client');

        @php
            $privilage = $menus['Client']->where('submenu_name', 'Parents')->first();
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

        $('#clientTable tbody').on('click', '.editClient ', function() {
            var data = table.row($(this).parents('tr')).data();
            window.location.href = "{{ url('client/parent') }}/" + data.id
        });

        $('#clientTable tbody').on('click', '.deleteClient ', function() {
            var data = table.row($(this).parents('tr')).data();
            confirmDelete('asset', data.asset_id)
        });
    });
</script>
@endpush
