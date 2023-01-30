@extends('layout.main')

@section('title', 'Partnership Report - Bigdata Platform')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Period</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label>Start Date</label>
                        <input type="date" name="" id="" class="form-control form-control-sm rounded">
                    </div>
                    <div class="mb-3">
                        <label>End Date</label>
                        <input type="date" name="" id="" class="form-control form-control-sm rounded">
                    </div>
                    <div class="text-center">
                        <button class="btn btn-sm btn-outline-primary">Submit</button>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Partnership Detail</h6>
                </div>
                <div class="card-body">
                    <div class="card mb-1 bg-danger text-white">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <strong class="">
                                Total School Visit
                            </strong>
                            <h5 class="text-end m-0 badge bg-white text-dark">
                                134
                            </h5>
                        </div>
                    </div>
                    <div class="card mb-1 bg-danger text-white">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <strong class="">
                                Total Partner Program
                            </strong>
                            <h5 class="text-end m-0 badge bg-white text-dark">
                                134
                            </h5>
                        </div>
                    </div>
                    <div class="card mb-1 bg-danger text-white">
                        <a href="#school"
                            class="card-body d-flex justify-content-between align-items-center text-decoration-none text-white">
                            <strong class="">
                                Total New School
                            </strong>
                            <h5 class="text-end m-0 badge bg-white text-dark">
                                20
                            </h5>
                        </a>
                    </div>
                    <div class="card mb-1 bg-danger text-white">
                        <a href="#partner"
                            class="card-body d-flex justify-content-between align-items-center text-decoration-none text-white">

                            <strong class="">
                                Total New Partner
                            </strong>
                            <h5 class="text-end m-0 badge bg-white text-dark">
                                13
                            </h5>
                        </a>
                    </div>
                    <div class="card mb-1 bg-danger text-white">
                        <a href="#university"
                            class="card-body d-flex justify-content-between align-items-center text-decoration-none text-white">

                            <strong class="">
                                Total New University
                            </strong>
                            <h5 class="text-end m-0 badge bg-white text-dark">
                                13
                            </h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">School Visit</h6>
                    <div class="">
                        <button class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark-excel me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="volunteerTable">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>School Name</th>
                                    <th>Program Name</th>
                                    <th>Program Date</th>
                                    <th>Participants</th>
                                    <th>Amount</th>
                                    <th>PIC</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td class="text-center">1</td>
                                <td>School Name</td>
                                <td>Program Name</td>
                                <td>Program Date</td>
                                <td>Participants</td>
                                <td>Amount</td>
                                <td>PIC</td>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5">Total Amount</th>
                                    <th colspan="2" class="text-center">Rp. 230.000.000</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Partner Program</h6>
                    <div class="">
                        <button class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark-excel me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="volunteerTable">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Partner Name</th>
                                    <th>Program Name</th>
                                    <th>Program Date</th>
                                    <th>Participants</th>
                                    <th>Amount</th>
                                    <th>PIC</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td class="text-center">1</td>
                                <td>Partner Name</td>
                                <td>Program Name</td>
                                <td>Program Date</td>
                                <td>Participants</td>
                                <td>Amount</td>
                                <td>PIC</td>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5">Total Amount</th>
                                    <th colspan="2" class="text-center">Rp. 230.000.000</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>

            <div class="card mb-3" id="school">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">New School</h6>
                    <div class="">
                        <button class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark-excel me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="volunteerTable">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>School Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Address</th>
                                    <th>Created at</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td class="text-center">1</td>
                                <td>School Name</td>
                                <td>Email</td>
                                <td>Phone Number</td>
                                <td>Address</td>
                                <td>Created at</td>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <div class="card mb-3" id="partner">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">New Partner</h6>
                    <div class="">
                        <button class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark-excel me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="volunteerTable">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Partner Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Address</th>
                                    <th>Created at</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td class="text-center">1</td>
                                <td>Partner Name</td>
                                <td>Email</td>
                                <td>Phone Number</td>
                                <td>Address</td>
                                <td>Created at</td>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <div class="card mb-3" id="university">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">New University</h6>
                    <div class="">
                        <button class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark-excel me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100" id="volunteerTable">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>University Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Address</th>
                                    <th>Created at</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td class="text-center">1</td>
                                <td>University Name</td>
                                <td>Email</td>
                                <td>Phone Number</td>
                                <td>Address</td>
                                <td>Created at</td>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function showHideDetail(d) {
            if ($('#' + d).hasClass('d-none')) {
                $('#' + d).removeClass('d-none')
            } else {
                $('#' + d).addClass('d-none')
            }
        }
    </script>
@endsection
