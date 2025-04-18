<div class="card mb-3">
    <div class="card-header">
        <h6 class="p-0 m-0">Client Program</h6>
    </div>
    <div class="card-body">
        @if ($sales_report_by_status['count']['pending'] == 0 && $sales_report_by_status['count']['failed'] == 0 && $sales_report_by_status['count']['success'] == 0 && $sales_report_by_status['count']['refund'] == 0)
            <div>No data</div>
        @endif
        @if ($sales_report_by_status['count']['pending'] > 0)
            <div class="table-responsive">
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
                        @foreach ($sales_report_by_status['data']['pending'] as $key => $val)
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
                                                        <a class="text-dark text-decoration-none" href="{{ url('program/client?program_name[]=' . $key3 . '&start_date=' . (Request::get('start') ?? date('Y-m-01')) . '&end_date=' . (Request::get('end') ?? date('Y-m-t')) . '&program_status[]=' . encrypt('0'). '&pic[]=' . Request::get('pic')) }}"
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
            </div>
        @endif

        @if ($sales_report_by_status['count']['failed'] > 0)
            <div class="table-responsive">
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
                        @foreach ($sales_report_by_status['data']['failed'] as $key => $val)
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
                                                        <a class="text-dark text-decoration-none" href="{{ url('program/client?program_name[]=' . $key3 . '&start_date=' . (Request::get('start') ?? date('Y-m-01')) . '&end_date=' . (Request::get('end') ?? date('Y-m-t')) . '&program_status[]=' . encrypt('2'). '&pic[]=' . Request::get('pic')) }}"
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
            </div>
        @endif

        @if ($sales_report_by_status['count']['refund'] > 0)
            <div class="table-responsive">
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
                        @foreach ($sales_report_by_status['data']['refund'] as $key => $val)
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
                                                        <a class="text-dark text-decoration-none" href="{{ url('program/client?program_name[]=' . $key3 . '&start_date=' . (Request::get('start') ?? date('Y-m-01')) . '&end_date=' . (Request::get('end') ?? date('Y-m-t')) . '&program_status[]=' . encrypt('3'). '&pic[]=' . Request::get('pic')) }}"
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
            </div>
        @endif

        @if ($sales_report_by_status['count']['success'] > 0)
            <div class="table-responsive">
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
                        @foreach ($sales_report_by_status['data']['success'] as $key => $val)
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
                                                        <a class="text-dark text-decoration-none" href="{{ url('program/client?program_name[]=' . $key3 . '&start_date=' . (Request::get('start') ?? date('Y-m-01')) . '&end_date=' . (Request::get('end') ?? date('Y-m-t')) . '&program_status[]=' . encrypt('1'). '&pic[]=' . Request::get('pic')) }}"
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
            </div>
        @endif
    </div>
</div>