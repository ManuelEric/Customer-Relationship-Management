@extends('layout.main')

@section('title', 'Sales Tracking - Bigdata Platform')

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
        </div>
        <div class="col-md-9">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Program Status</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-3 text-center">
                            <div class="border p-2 shadow-sm rounded text-warning">
                                <h3>10</h3>
                                <h6 class="m-0 p-0">Pending</h6>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border p-2 shadow-sm rounded text-danger">
                                <h3>10</h3>
                                <h6 class="m-0 p-0">Failed</h6>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border p-2 shadow-sm rounded text-info">
                                <h3>10</h3>
                                <h6 class="m-0 p-0">Refund</h6>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border p-2 shadow-sm rounded text-success">
                                <h3>10</h3>
                                <h6 class="m-0 p-0">Success</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Client Program</h6>
                </div>
                <div class="card-body">
                    <table class="table mb-3">
                        <thead>
                            <tr class="bg-warning text-center">
                                <th colspan="4">Pending</th>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>Main Program</th>
                                <th>Program Name</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr valign="middle">
                                <td>1</td>
                                <td>Admissions Mentoring</td>
                                <td>
                                    <table class="table table-hover table-bordered">
                                        <tr>
                                            <td>Admissions Mentoring: Ultimate Package</td>
                                            <td class="text-center">12</td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="text-center">
                                    <strong>12</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table mb-3">
                        <thead>
                            <tr class="bg-success text-white text-center">
                                <th colspan="4">Success</th>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>Main Program</th>
                                <th>Program Name</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr valign="middle">
                                <td>1</td>
                                <td>Admissions Mentoring</td>
                                <td>
                                    <table class="table table-hover table-bordered">
                                        <tr>
                                            <td>Admissions Mentoring: Ultimate Package</td>
                                            <td class="text-center">12</td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="text-center">
                                    <strong>12</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Initial Assessment Progress</h6>
                </div>
                <div class="card-body">
                    <table class="table mb-3">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Program Name</th>
                                <th>Initial Assessment Making</th>
                                <th>Converted</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#</td>
                                <td>Program Name</td>
                                <td>Initial Assessment Making</td>
                                <td>Converted</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Conversion Lead</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-center">
                                <strong>Lead Source</strong>
                            </div>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="">
                                        Referral
                                    </div>
                                    <span class="badge bg-primary rounded-pill">14</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <strong>Conversion Lead</strong>
                            </div>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="">
                                        Referral
                                    </div>
                                    <span class="badge bg-primary rounded-pill">14</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">
                        Average Conversion Time to Successful Programs</h6>
                </div>
                <div class="card-body">
                    <table class="table mb-3">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Program Name</th>
                                <th>Average Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#</td>
                                <td>Program Name</td>
                                <td>Average Time</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection
