@extends('layout.main')

@section('title', 'Recycle - Teacher ')

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
                    Teachers
                </h5>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card rounded">
        <div class="card-body">

            <table class="table table-bordered table-hover nowrap align-middle w-100" id="clientTable">
                <thead class="bg-secondary text-white">
                    <tr class="text-center" role="row">
                        <th class="bg-info text-white">No</th>
                        <th class="bg-info text-white">Teacher Name</th>
                        <th>Teacher Email</th>
                        <th>Teacher Number</th>
                        <th>From</th>
                        <th class="bg-info text-white">Status</th>
                        <th class="bg-info text-white">#</th>
                    </tr>
                </thead>
                {{-- <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="7"></td>
                    </tr>
                </tfoot> --}}
            </table>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    var widthView = $(window).width();
    $(document).ready(function() {
        var table = $('#clientTable').DataTable({
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
            processing: true,
            serverSide: true,
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
                        return data
                    }
                },
                {
                    data: 'mail',
                    defaultContent: '-'
                },
                {
                    data: 'phone',
                    className: 'text-center',
                    defaultContent: '-'
                },
                {
                    data: 'school_name',
                    name: 'school_name',
                    className: 'text-center',
                    defaultContent: '-'
                },
                {
                    data: 'st_statusact',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return data == 1 ?
                            "<div class='badge badge-outline-success'>Active</div>" :
                            "<div class='badge badge-outline-danger'>NonActive</div>";
                    }
                },
                {
                    data: '',
                    className: 'text-center',
                    defaultContent: '<button type="button" class="btn btn-sm btn-outline-success editClient"><i class="bi bi-arrow-counterclockwise"></i></button>'
                }
            ],
        });

        @php
            $privilage = $menus['Client']->where('submenu_name', 'Teacher/Counselor')->first();
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
            window.location.href = "{{ url('client/teacher-counselor') }}/" + data.id;
        });

    });
</script>
@endpush
