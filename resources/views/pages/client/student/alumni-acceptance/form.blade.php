@extends('layout.main')

@section('title', 'Alumni Acceptance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Alumni Acceptance</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Detail</li>
@endsection
@section('content')
    <div class="row mt-3">
        <div class="col-md-4 text-center">
            <div class="card">
                <div class="card-body">
                    <img src="{{ asset('img/asset.png') }}" alt="" class="w-75">
                    {{-- View of Edit  --}}
                    <div class="text-center">
                        <h4>Alumni Name</h4>
                        <h6>School Name | Graduation Year</h6>
                    </div>
                    {{-- End of view  --}}
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    {{-- View of Create  --}}
                    <form action="">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label>Alumni Name</label>
                                <select name="" id="" class="select w-100"></select>
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex align-items-center justify-content-end">
                                    <button type="button" class="btn btn-info btn-sm" id="add_univ">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <div id="univ_list">
                                    <div class="row align-items-end g-2 mb-3">
                                        <div class="col-md-4">
                                            <label>University Name</label>
                                            <select name="uni_id[]" id="" class="select w-100"></select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Major</label>
                                            <select  name="major[]" id="" class="select w-100"></select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Status</label>
                                            <select name="status[]" id="" class="select w-100">
                                                <option value="Waitlisted">Waitlisted</option>
                                                <option value="Accepted">Accepted</option>
                                                <option value="Denied">Denied</option>
                                                <option value="Choosed">Choosed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 text-center">
                            <button class="btn btn-sm btn-primary">
                                <i class="bi bi-safe"></i>
                                Save changes
                            </button>
                        </div>
                    </form>
                    {{-- End of view  --}}


                    {{-- View of Edit  --}}
                    <table class="table">
                        <thead>
                            <tr>
                                <th>University Name</th>
                                <th>Major</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td>University Name</td>
                                    <td>Major</td>
                                    <td>Status</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-danger"
                                            onclick="confirmDelete('client/alumni-acceptance/1/view', {{ $i }})">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                    {{-- End of view  --}}
                </div>
            </div>
        </div>
    </div>

    <script>
        var index = 1
        $('#add_univ').click(function() {
            const id = index++
            $('#univ_list').append(
                '<div class="row align-items-end g-2 mb-3" id="univ_' + id + '">' +
                '<div class="col-md-4">' +
                '<label>University Name</label>' +
                '<select name="uni_id[]" id="" class="select w-100"></select>' +
                '</div>' +
                '<div class="col-md-4">' +
                '<label>Major</label>' +
                '<select  name="major[]" id="" class="select w-100"></select>' +
                '</div>' +
                '<div class="col-md-3">' +
                '<label>Status</label>' +
                '<select name="status[]" id="" class="select w-100">' +
                '<option value="Waitlisted">Waitlisted</option>' +
                '<option value="Accepted">Accepted</option>' +
                '<option value="Denied">Denied</option>' +
                '<option value="Choosed">Choosed</option>' +
                '</select>' +
                '</div>' +
                '<div class="col-md-1 text-end">' +
                '<button type="button" class="btn btn-sm btn-danger" onclick="deleteRow(' + id + ')">' +
                '<i class="bi bi-x"></i>' +
                '</button>' +
                '</div>' +
                '</div>'
            )

            $('.select').select2({
                placeholder: "Select value",
                allowClear: true
            });
        })

        function deleteRow(id) {
            $('#univ_list #univ_' + id).remove();
        }
    </script>
@endsection
