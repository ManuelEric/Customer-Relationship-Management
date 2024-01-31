@extends('layout.main')

@section('title', 'Sales Tracking')

@section('content')
    @if (Request::get('start') && Request::get('end'))
        <div class="row">
            <div class="col">
                <div class="alert alert-success">
                    Sales tracking report between <u>{{ date('F, dS Y', strtotime(Request::get('start'))) }}</u> and
                    <u>{{ date('F, dS Y', strtotime(Request::get('end'))) }}</u>
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-3 position-sticky" style="top:15%;">
                <form action="" id="filterForm">
                    <div class="card-header">
                        <h6 class="p-0 m-0">Period</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Start Date</label>
                            <input type="date" name="start" class="form-control form-control-sm rounded"
                                value="{{ Request::get('start') }}">
                        </div>
                        <div class="mb-3">
                            <label>End Date</label>
                            <input type="date" name="end" class="form-control form-control-sm rounded"
                                value="{{ Request::get('end') }}">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-search me-1"></i>
                                Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-9">
            {{-- Program Status  --}}
            @include('pages.report.sales-tracking.component.program-status')

            {{-- Client Program  --}}
            @include('pages.report.sales-tracking.component.client-program')

            @if ( (!request()->get('start')) && (!request()->get('end')))
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Sales Target</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="sales-target-detail">
                            <thead>
                                <tr class="text-center text-white">
                                    <th rowspan="2" class="bg-secondary border-1 border-white">No</th>
                                    <th rowspan="2" class="bg-secondary border-1 border-white">ID</th>
                                    <th rowspan="2" class="bg-secondary border-1 border-white">Program Name</th>
                                    <th colspan="2" class="bg-secondary border-1 border-white">Target</th>
                                    <th colspan="2" class="bg-secondary border-1 border-white">Actual Sales</th>
                                    <th colspan="2" class="bg-secondary border-1 border-white">Sales Percentage</th>
                                </tr>
                                <tr class="text-center text-white">
                                    <td class="bg-secondary border-1 border-white">Students</td>
                                    <td class="bg-secondary border-1 border-white">Total Amount</td>
                                    <td class="bg-secondary border-1 border-white">Students</td>
                                    <td class="bg-secondary border-1 border-white">Total Amount</td>
                                    <td class="bg-secondary border-1 border-white">Students</td>
                                    <td class="bg-secondary border-1 border-white">Total Amount</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salesDetail as $detail)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $detail->prog_id }}</td>
                                        <td class="text-start">{{ $detail->program_name_sales }}</td>
                                        <td>{{ $detail->total_target_participant ??= 0 }}</td>
                                        <td>{{ number_format($detail->total_target, '2', ',', '.') }}</td>
                                        <td>{{ $detail->total_actual_participant }}</td>
                                        <td>{{ number_format($detail->total_actual_amount, '2', ',', '.') }}</td>
                                        <td>{{ $detail->total_target_participant != 0 ? round(($detail->total_actual_participant / $detail->total_target_participant) * 100, 2) : 0 }}%
                                        </td>
                                        <td>{{ $detail->total_target != 0 ? ($detail->total_actual_amount / $detail->total_target) * 100 : 0 }}%
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="text-center">
                                    <th colspan="3">Total</th>
                                    <td><b>{{ $salesDetail->sum('total_target_participant') ?? 0 }}</b></td>
                                    <td><b>{{ number_format($salesDetail->sum('total_target'), '2', ',', '.') }}</b>
                                    </td>
                                    <td><b>{{ $salesDetail->sum('total_actual_participant') ?? 0 }}</b></td>
                                    <td><b>{{ number_format($salesDetail->sum('total_actual_amount'), '2', ',', '.') }}</b>
                                    </td>
                                    <td><b>{{ $salesDetail->sum('total_target_participant') != 0 ? round(($salesDetail->sum('total_actual_participant') / $salesDetail->sum('total_target_participant')) * 100, 2) : 0 }}%</b>
                                    </td>
                                    <td><b>{{ $salesDetail->sum('total_target') != 0 ? ($salesDetail->sum('total_actual_amount') / $salesDetail->sum('total_target')) * 100 : 0 }}%</b>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
            
            {{-- Initial Assessment Progress  --}}
            @include('pages.report.sales-tracking.component.initial-assessment')

            {{-- Conversion Lead  --}}
            @include('pages.report.sales-tracking.component.conversion-lead')

            {{-- Average Conversion Time  --}}
            @include('pages.report.sales-tracking.component.average-conversion')
        </div>
    </div>


    {{-- Modal for Lead Detail  --}}
    <div class="modal modal-lg fade" tabindex="-1" id="leadModalDetail">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body overflow-auto" style="max-height: 400px">
                    <table class="table table-striped">
                    </table>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>

    <script>
        @php
            $privilage = $menus['Report']->where('submenu_name', 'Sales Tracking')->first();
        @endphp
        $(document).ready(function() {
            @if ($privilage['copy'] == 0)
                document.oncontextmenu = new Function("return false");

                $('body').bind('cut copy paste', function(event) {
                    event.preventDefault();
                });
            @endif
        });

        $(document).on('click', '.lead-source-item', function() {
            var _this = $(this);
            const requestParam = getParam(_this);
            var url = '{{ url('/api/v1/get/detail/lead-source') }}';
            requestParam['url'] = url;

            showDetailLead(requestParam)
        })

        $(document).on('click', '.conversion-lead-item', function() {
            var _this = $(this);
            const requestParam = getParam(_this);
            var url = '{{ url('/api/v1/get/detail/conversion-lead') }}';
            requestParam['url'] = url;

            showDetailLead(requestParam)
        })

        function showDetailLead(param) {
            showLoading();

            axios.get(param['url'], {
                    params: param
                })
                .then(function(response) {

                    const obj = response.data.data;

                    swal.close();
                    $("#leadModalDetail").modal('show');
                    $("#leadModalDetail .modal-title").html(obj.title)

                    $("#leadModalDetail table").html(obj.context);

                })
                .catch(function(error) {
                    // handle error
                    Swal.close()
                    notification(error.response.data.success, error.response.data.message)
                })
        }

        function getParam(_this) {
            var leadId = _this.data('lead');
            var leadName = _this.data('leadname');
            var startDate = _this.data('sdate');
            var endDate = _this.data('edate');
            return {
                leadId: leadId,
                leadName: leadName,
                startDate: startDate,
                endDate: endDate
            }

        }
    </script>
@endsection
