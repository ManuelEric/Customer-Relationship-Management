@extends('layout.main')

@section('title', 'Sales Tracking - Bigdata Platform')

@section('content')
    @if (Request::get('start') && Request::get('end'))
    <div class="row">
        <div class="col">
            <div class="alert alert-success">
                Sales tracking report between <u>{{ date('d F Y', strtotime(Request::get('start'))) }}</u> and <u>{{ date('d F Y', strtotime(Request::get('end'))) }}</u>
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-3">
                <form action="" id="filterForm">
                    <div class="card-header">
                        <h6 class="p-0 m-0">Period</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Start Date</label>
                            <input type="date" name="start" class="form-control form-control-sm rounded">
                        </div>
                        <div class="mb-3">
                            <label>End Date</label>
                            <input type="date" name="end" class="form-control form-control-sm rounded">
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
                            <div class="border p-2 shadow-sm rounded text-warning">
                                <h3>{{ $countClientProgram['pending'] }}</h3>
                                <h6 class="m-0 p-0">Pending</h6>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border p-2 shadow-sm rounded text-danger">
                                <h3>{{ $countClientProgram['failed'] }}</h3>
                                <h6 class="m-0 p-0">Failed</h6>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border p-2 shadow-sm rounded text-info">
                                <h3>{{ $countClientProgram['refund'] }}</h3>
                                <h6 class="m-0 p-0">Refund</h6>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border p-2 shadow-sm rounded text-success">
                                <h3>{{ $countClientProgram['success'] }}</h3>
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
                                        @php
                                            $total += count($detail);
                                        @endphp
                                        <table class="table table-hover table-bordered">
                                            <tr>
                                                <td style="width:92%">{{ $key }}: {{ $key2 }}</td>
                                                <td class="text-center">{{ count($detail) }}</td>
                                            </tr>
                                        </table>
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
                                        @php
                                            $total += count($detail);
                                        @endphp
                                        <table class="table table-hover table-bordered">
                                            <tr>
                                                <td style="width:92%">{{ $key }}: {{ $key2 }}</td>
                                                <td class="text-center">{{ count($detail) }}</td>
                                            </tr>
                                        </table>
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
                                        @php
                                            $total += count($detail);
                                        @endphp
                                        <table class="table table-hover table-bordered">
                                            <tr>
                                                <td style="width:92%">{{ $key }}: {{ $key2 }}</td>
                                                <td class="text-center">{{ count($detail) }}</td>
                                            </tr>
                                        </table>
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
                                        @php
                                            $total += count($detail);
                                        @endphp
                                        <table class="table table-hover table-bordered">
                                            <tr>
                                                <td style="width:92%">{{ $key }}: {{ $key2 }}</td>
                                                <td class="text-center">{{ count($detail) }}</td>
                                            </tr>
                                        </table>
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
                <div class="card-body overflow-auto" style="height: 50vh;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-center">
                                <strong>Lead Source</strong>
                            </div>
                            <ul class="list-group">
                                @foreach ($leadSource as $detail)
                                <li class="list-group-item d-flex justify-content-between align-items-center lead-source-item" data-lead="{{ $detail->lead_id }}" data-sdate="{{ $dateDetails['startDate'] }}" data-edate="{{ $dateDetails['endDate'] }}">
                                    <div class="">
                                        {{ $detail->lead_source }}
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ $detail->lead_source_count }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <strong>Conversion Lead</strong>
                            </div>
                            <ul class="list-group">
                                @foreach ($conversionLead as $detail)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="">
                                        {{ $detail->conversion_lead }}
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ $detail->conversion_lead_count }}</span>
                                </li>
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
    <div class="modal" tabindex="-1" id="leadModalDetail">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Modal body text goes here.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        @php            
            $privilage = $menus['Report']->where('submenu_name', 'Sales Tracking')->first();
        @endphp
        $(document).ready(function() {
            @if($privilage['copy'] == 0)
                document.oncontextmenu = new Function("return false"); 
                    
                $('body').bind('cut copy paste', function(event) {
                    event.preventDefault();
                });
            @endif
        });

        $(document).on('click', '.lead-source-item', function() {
            var _this = $(this);
            const requestParam = getParam(_this);
            var url = '{{ url("/api/v1/get/detail/lead-source") }}';
            requestParam['url'] = url;

            showDetailLead(requestParam)
        })

        function showDetailLead(param)
        {
            axios.get(param['url'], {params: param})
                .then(function(response) {
                    
                    console.log(response)
                })
                .catch(function(error) {
                    // handle error
                    Swal.close()
                    notification(error.response.data.success, error.response.data.message)
                })
        }

        function getParam(_this)
        {
            var leadId = _this.data('lead');
            var startDate = _this.data('sdate');
            var endDate = _this.data('edate');
            return {
                leadId: leadId,
                startDate: startDate,
                endDate: endDate
            }

        }
    </script>
@endsection
