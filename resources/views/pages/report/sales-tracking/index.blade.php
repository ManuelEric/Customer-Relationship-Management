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
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Program Status</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-3 text-center">
                            <a href="{{ url('program/client?start_date=' . Request::get('start') . '&end_date=' . Request::get('end') . '&program_status[]=' . encrypt('0')) }}"
                                class="text-decoration-none" target="_blank">
                                <div class="border p-2 shadow-sm rounded text-warning">
                                    <h3>{{ $countClientProgram['pending'] }}</h3>
                                    <h6 class="m-0 p-0">Pending</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 text-center">
                            <a href="{{ url('program/client?start_date=' . Request::get('start') . '&end_date=' . Request::get('end') . '&program_status[]=' . encrypt('2')) }}"
                                class="text-decoration-none" target="_blank">
                                <div class="border p-2 shadow-sm rounded text-danger">
                                    <h3>{{ $countClientProgram['failed'] }}</h3>
                                    <h6 class="m-0 p-0">Failed</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 text-center">
                            <a href="{{ url('program/client?start_date=' . Request::get('start') . '&end_date=' . Request::get('end') . '&program_status[]=' . encrypt('3')) }}"
                                class="text-decoration-none" target="_blank">
                                <div class="border p-2 shadow-sm rounded text-info">
                                    <h3>{{ $countClientProgram['refund'] }}</h3>
                                    <h6 class="m-0 p-0">Refund</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 text-center">
                            <a href="{{ url('program/client?start_date=' . Request::get('start') . '&end_date=' . Request::get('end') . '&program_status[]=' . encrypt('1')) }}"
                                class="text-decoration-none" target="_blank">
                                <div class="border p-2 shadow-sm rounded text-success">
                                    <h3>{{ $countClientProgram['success'] }}</h3>
                                    <h6 class="m-0 p-0">Success</h6>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Client Program</h6>
                </div>
                <div class="card-body">
                    @if ($countClientProgram['pending'] > 0)
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
                                @foreach ($clientProgramDetail['pending'] as $key => $val)
                                    @php
                                        $total = 0;
                                    @endphp
                                    <tr valign="middle">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $key }}</td>
                                        <td>
                                            @foreach ($val as $key2 => $detail)
                                                @foreach ($detail as $key3 => $join_prog)
                                                    @php
                                                        $total += count($join_prog);
                                                    @endphp
                                                    <table class="table table-hover table-bordered">
                                                        <tr>
                                                            <td style="width:92%">
                                                                <a class="text-dark text-decoration-none" href="{{ url('program/client?program_name[]=' . $key3 . '&start_date=' . Request::get('start') . '&end_date=' . Request::get('end') . '&program_status[]=' . encrypt('0')) }}"
                                                                    class="text-decoration-none" target="_blank">
                                                                    {{ $key . ' : ' . $key2 }}
                                                                </a>
                                                            </td>
                                                            <td class="text-center">{{ count($join_prog) }}</td>
                                                        </tr>
                                                    </table>
                                                @endforeach
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            <strong>{{ $total }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    @if ($countClientProgram['failed'] > 0)
                        <table class="table mb-3">
                            <thead>
                                <tr class="bg-danger text-center">
                                    <th colspan="4">Failed</th>
                                </tr>
                                <tr>
                                    <th>#</th>
                                    <th>Main Program</th>
                                    <th>Program Name</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clientProgramDetail['failed'] as $key => $val)
                                    @php
                                        $total = 0;
                                    @endphp
                                    <tr valign="middle">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $key }}</td>
                                        <td>
                                            @foreach ($val as $key2 => $detail)
                                                @foreach ($detail as $key3 => $join_prog)
                                                    @php
                                                        $total += count($join_prog);
                                                    @endphp
                                                    <table class="table table-hover table-bordered">
                                                        <tr>
                                                            <td style="width:92%">
                                                                <a class="text-dark text-decoration-none" href="{{ url('program/client?program_name[]=' . $key3 . '&start_date=' . Request::get('start') . '&end_date=' . Request::get('end') . '&program_status[]=' . encrypt('2')) }}"
                                                                    class="text-decoration-none" target="_blank">
                                                                    {{ $key . ' : ' . $key2 }}
                                                                </a>
                                                            </td>
                                                            <td class="text-center">{{ count($join_prog) }}</td>
                                                        </tr>
                                                    </table>
                                                @endforeach
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            <strong>{{ $total }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    @if ($countClientProgram['refund'] > 0)
                        <table class="table mb-3">
                            <thead>
                                <tr class="bg-info text-center">
                                    <th colspan="4">Refund</th>
                                </tr>
                                <tr>
                                    <th>#</th>
                                    <th>Main Program</th>
                                    <th>Program Name</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clientProgramDetail['refund'] as $key => $val)
                                    @php
                                        $total = 0;
                                    @endphp
                                    <tr valign="middle">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $key }}</td>
                                        <td>
                                            @foreach ($val as $key2 => $detail)
                                                @foreach ($detail as $key3 => $join_prog)
                                                    @php
                                                        $total += count($join_prog);
                                                    @endphp
                                                    <table class="table table-hover table-bordered">
                                                        <tr>
                                                            <td style="width:92%">
                                                                <a class="text-dark text-decoration-none" href="{{ url('program/client?program_name[]=' . $key3 . '&start_date=' . Request::get('start') . '&end_date=' . Request::get('end') . '&program_status[]=' . encrypt('3')) }}"
                                                                    class="text-decoration-none" target="_blank">
                                                                    {{ $key . ' : ' . $key2 }}
                                                                </a>
                                                            </td>
                                                            <td class="text-center">{{ count($join_prog) }}</td>
                                                        </tr>
                                                    </table>
                                                @endforeach
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            <strong>{{ $total }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    @if ($countClientProgram['success'] > 0)
                        <table class="table mb-3">
                            <thead>
                                <tr class="bg-success text-center">
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
                                @foreach ($clientProgramDetail['success'] as $key => $val)
                                    @php
                                        $total = 0;
                                    @endphp
                                    <tr valign="middle">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $key }}</td>
                                        <td>
                                            @foreach ($val as $key2 => $detail)
                                                @foreach ($detail as $key3 => $join_prog)
                                                    @php
                                                        $total += count($join_prog);
                                                    @endphp
                                                    <table class="table table-hover table-bordered">
                                                        <tr>
                                                            <td style="width:92%">
                                                                <a class="text-dark text-decoration-none" href="{{ url('program/client?program_name[]=' . $key3 . '&start_date=' . Request::get('start') . '&end_date=' . Request::get('end') . '&program_status[]=' . encrypt('1')) }}"
                                                                    class="text-decoration-none" target="_blank">
                                                                    {{ $key . ' : ' . $key2 }}
                                                                </a>
                                                            </td>
                                                            <td class="text-center">{{ count($join_prog) }}</td>
                                                        </tr>
                                                    </table>
                                                @endforeach
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            <strong>{{ $total }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Initial Assessment Progress</h6>
                </div>
                <div class="card-body">
                    @if (isset($initAssessmentProgress))
                        <table class="table mb-3">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Program Name</th>
                                    <th class="text-center">Initial Assessment<br>Making</th>
                                    <th class="text-center">Converted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($initAssessmentProgress as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $detail->program_name_st }}</td>
                                        <td class="text-center">{{ (int) $detail->initialMaking }} day</td>
                                        <td class="text-center">{{ (int) $detail->converted }} day</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        No Data
                    @endif
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Conversion Lead</h6>
                </div>
                <div class="card-body overflow-auto" style="max-height: 50vh;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-center">
                                <strong>Lead Source</strong>
                            </div>
                            <ul class="list-group list-group-flush">
                                @foreach ($leadSource as $detail)
                                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center lead-source-item cursor-pointer"
                                        data-leadname="{{ $detail->lead_source }}" data-lead="{{ $detail->lead_id }}"
                                        data-sdate="{{ $dateDetails['startDate'] }}"
                                        data-edate="{{ $dateDetails['endDate'] }}">
                                        <div class="">
                                            {{ $detail->lead_source }}
                                        </div>
                                        <span
                                            class="badge bg-primary rounded-pill">{{ $detail->lead_source_count }}</span>
                                    </a>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <strong>Conversion Lead</strong>
                            </div>
                            <ul class="list-group list-group-flush">
                                @foreach ($conversionLead as $detail)
                                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center conversion-lead-item cursor-pointer"
                                        data-leadname="{{ $detail->conversion_lead }}"
                                        data-lead="{{ $detail->lead_id }}" data-sdate="{{ $dateDetails['startDate'] }}"
                                        data-edate="{{ $dateDetails['endDate'] }}">
                                        <div class="">
                                            {{ $detail->conversion_lead }}
                                        </div>
                                        <span
                                            class="badge bg-primary rounded-pill">{{ $detail->conversion_lead_count }}</span>
                                    </a>
                                @endforeach
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
                            @foreach ($averageConversionSuccessful as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $detail->program_name_st }}</td>
                                    <td>{{ (int) $detail->average_time }} days</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
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
