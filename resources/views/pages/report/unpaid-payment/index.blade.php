@extends('layout.main')

@section('title', 'Finance Report - Bigdata Platform')

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

            <div class="card">
                <div class="card-header">
                    <h6 class="p-0 m-0">Detail</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <strong>Total</strong>
                        <div class="text-end">
                            Rp. 123.000.000
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong>Paid</strong>
                        <div class="text-end">
                            Rp. 123.000.000
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <strong>Remaining</strong>
                        <div class="text-end">
                            Rp. 123.000.000
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Invoice & Receipt Report</h6>
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
                                    <th>#</th>
                                    <th>Invoice ID</th>
                                    <th>Client/Partner/School Name</th>
                                    <th>Program Name</th>
                                    <th>Installment</th>
                                    <th>Status</th>
                                    <th>Receipt ID</th>
                                    <th>Paid Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>0001/INV-JEI/STP/II/23</td>
                                    <td>Client/Partner/School Name</td>
                                    <td>Program Name</td>
                                    <td>Installment</td>
                                    <td>Paid</td>
                                    <td>0001/REC-JEI/STP/II/23</td>
                                    <td>23 February 2023</td>
                                    <td>Rp. 23.000.000</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>0001/INV-JEI/STP/II/23</td>
                                    <td>Client/Partner/School Name</td>
                                    <td>Program Name</td>
                                    <td>Installment</td>
                                    <td>Not Yet Paid</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>Rp. 23.000.000</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-light text-white">
                                <tr>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
