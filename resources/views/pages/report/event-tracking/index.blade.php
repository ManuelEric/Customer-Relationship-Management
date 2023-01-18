@extends('layout.main')

@section('title', 'Sales Tracking - Bigdata Platform')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Client Event</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label>Event Name</label>
                        <select name="" id="" class="select w-100"></select>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-search me-1"></i>
                            Submit
                        </button>
                    </div>
                </div>
            </div>

            {{-- Client  --}}
            <div class="card mb-3">
                <div class="card-header  d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Client</h6>
                    <span class="badge bg-primary">12</span>
                </div>
                <div class="card-body p-2 overflow-auto" style="max-height: 150px ">
                    <ul class="list-group">
                        <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                            <div class="">Parents</div>
                            <span class="badge badge-info">12</span>
                        </li>
                        <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                            <div class="">Students</div>
                            <span class="badge badge-info">12</span>
                        </li>
                        <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                            <div class="">Teacher & Consellor</div>
                            <span class="badge badge-info">12</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Conversion Lead  --}}
            <div class="card mb-3">
                <div class="card-header  d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Conversion Lead</h6>
                </div>
                <div class="card-body p-2 overflow-auto" style="max-height: 150px ">
                    <ul class="list-group">
                        @for ($i = 0; $i < 20; $i++)
                            <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                                <div class="">Whatshapp</div>
                                <span class="badge badge-warning">12</span>
                            </li>
                        @endfor
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Client Event</h6>
                    <div class="">
                        <button class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark-excel me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body overflow-auto" style="height: 500px">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>School Name</th>
                                    <th>Grade</th>
                                    <th>Conversion Lead</th>
                                    <th>Joined At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < 20; $i++)
                                    <tr>
                                        <td class="text-center">{{ $i }}</td>
                                        <td>Client Name</td>
                                        <td>Email</td>
                                        <td>Phone Number</td>
                                        <td>School Name</td>
                                        <td>Grade</td>
                                        <td>Conversion Lead</td>
                                        <td>Joined At</td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
