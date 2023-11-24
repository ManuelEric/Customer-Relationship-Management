@extends('layout.main')

@section('title', 'Student')

@push('styles')
    <link rel="stylesheet" href="{{ asset('library/dashboard/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">
    <style>
        .btn-download span,
        .btn-import span {
            display: none;
        }

        .btn-download:hover>span,
        .btn-import:hover>span {
            display: inline-block;
        }

        td.dt-control {
            background: url('http://www.datatables.net/examples/resources/details_open.png') no-repeat center center;
            cursor: pointer;
        }

        tr.shown td.dt-control {
            background: url('http://www.datatables.net/examples/resources/details_close.png') no-repeat center center;
        }
    </style>
@endpush

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="row align-items-center justify-content-between g-3">
            <div class="col-md-6">
                <h5 class="text-white m-0">
                    <i class="bi bi-tag me-1"></i>
                    Teacher
                </h5>
            </div>
            <div class="col-md-6">
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
            <ul class="nav nav-tabs flex-nowrap overflow-auto w-100 mb-3" style="overflow-y: hidden !important;">
                <li class="nav-item">
                    <a class="nav-link text-nowrap active" aria-current="page" href="{{ url('client/parent/raw') }}">Raw
                        Data</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap" aria-current="page" href="{{ url('client/parent') }}">Parents</a>
                </li>
            </ul>


            <style>
                #clientTable tr td.danger {
                    background: rgb(255, 151, 151)
                }
            </style>
            <div class="table-responsive">
                <table class="table table-bordered table-hover nowrap align-middle w-100" id="rawTable">
                    <thead class="bg-secondary text-white">
                        <tr class="text-center" role="row">
                            <th class="bg-info text-white">#</th>
                            <th class="bg-info text-white">No</th>
                            <th class="bg-info text-white">Teacher Name</th>
                            <th class="bg-info text-white">Suggestion</th>
                            <th>Teacher Email</th>
                            <th>Teacher Number</th>
                            <th>From</th>
                            <th>Last Updated</th>
                            <th class="bg-info text-white">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 10; $i++)
                            <tr>
                                <td class="dt-control"></td>
                                <td>{{ $i }}</td>
                                <td>Lorem</td>
                                <td>
                                    <div class="badge badge-warning">
                                        2 Similar Name
                                    </div>
                                </td>
                                <td>Lorem</td>
                                <td>Lorem</td>
                                <td>Lorem</td>
                                <td>Lorem</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-danger py-1 px-2" onclick="confirmDelete('raw-data', 1)">
                                        <i class="bi bi-eraser"></i>
                                    </button>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        var widthView = $(window).width();
        $(document).ready(function() {

            // Formatting function for row details - modify as you need
            function format(d) {
                var similar = '<table class="table w-auto table-hover">'
                similar +=
                    '<th colspan=6>Comparison with Similar Names:</th>' +
                    '</tr>' +
                    '<tr>' +
                    '<th>#</th><th>Name</th><th>Email</th><th>Phone Number</th><th>Child Name</th>' +
                    '</tr>';

                for (let i = 1; i <= 3; i++) {
                    similar += '<tr onclick="comparison(' +
                        1 + ',' + 2 + ')" class="cursor-pointer">' +
                        '<td><input type="radio" name="similar' + 1 +
                        '" class="form-check-input item-' + 2 + '" onclick="comparison(' +
                        1 + ',' + 2 + ')" /></td>' +
                        '<td>' + 'Name' + '</td>' +
                        '<td>' + 'Email' + '</td>' +
                        '<td>' + 'Phone Number' + '</td>' +
                        '<td>' + 'Child Name' + '</td>' +
                        '</tr>'
                };

                similar +=
                    '<tr>' +
                    '<th colspan=6>Convert without Comparison</th>' +
                    '</tr>' +
                    '<tr class="cursor-pointer" onclick="newLeads(' +
                    1 + ')">' +
                    '<td><input type="radio" name="similar' + 1 +
                    '" class="form-check-input item-' + 1 + '" onclick="newLeads(' +
                    1 + ')" /></td>' +
                    '<td colspan=5>New Student</td>' +
                    '</tr>' +
                    '</table>'
                // `d` is the original data object for the row
                return (similar);
            }

            var table = $('#rawTable').DataTable();

            // Add a click event listener to each row in the parent DataTable
            table.on('click', 'td.dt-control', function(e) {
                let tr = e.target.closest('tr');
                let row = table.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                }
            });

        });

        function comparison(id, id2) {
            $('input.item-' + id2).prop('checked', true);
            window.open("{{ url('client/teacher-counselor/raw/') }}" + '/' + id + '/comparison/' + id2, "_blank");
        }

        function newLeads(id) {
            $('input.item-' + id).prop('checked', true);
            window.open("{{ url('client/teacher-counselor/raw/') }}" + '/' + id + '/new', "_blank");
        }
    </script>
@endpush
