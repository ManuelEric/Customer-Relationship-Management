<div class="card mb-3">
    <div class="card-header">
        <h6 class="p-0 m-0">Actual Sales</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="sales-target-detail">
                <thead>
                    <tr class="text-center text-white">
                        <th rowspan="2" class="bg-secondary border-1 border-white">No</th>
                        <th rowspan="2" class="bg-secondary border-1 border-white">ID</th>
                        <th rowspan="2" class="bg-secondary border-1 border-white">Program Name</th>
                        {{-- <th colspan="2" class="bg-secondary border-1 border-white">Target</th> --}}
                        <th colspan="2" class="bg-secondary border-1 border-white">Actual Sales</th>
                        {{-- <th colspan="2" class="bg-secondary border-1 border-white">Sales Percentage</th> --}}
                    </tr>
                    <tr class="text-center text-white">
                        {{-- <td class="bg-secondary border-1 border-white">Students</td>
                        <td class="bg-secondary border-1 border-white">Total Amount</td> --}}
                        <td class="bg-secondary border-1 border-white">Students</td>
                        <td class="bg-secondary border-1 border-white">Total Amount</td>
                        {{-- <td class="bg-secondary border-1 border-white">Students</td>
                        <td class="bg-secondary border-1 border-white">Total Amount</td> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($salesDetail as $detail)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $detail->prog_id }}</td>
                            <td class="text-start">{{ $detail->program_name_sales }}</td>
                            {{-- <td>{{ $detail->total_target_participant ??= 0 }}</td> --}}
                            {{-- <td>{{ number_format($detail->total_target, '2', ',', '.') }}</td> --}}
                            <td>{{ $detail->total_actual_participant }}</td>
                            <td>{{ number_format($detail->total_actual_amount, '2', ',', '.') }}</td>
                            {{-- <td>{{ $detail->total_target_participant != 0 ? round(($detail->total_actual_participant / $detail->total_target_participant) * 100, 2) : 0 }}%
                            </td> --}}
                            {{-- <td>{{ $detail->total_target != 0 ? ($detail->total_actual_amount / $detail->total_target) * 100 : 0 }}%
                            </td> --}}
                        </tr>
                    @endforeach
                    @if(count($salesDetail) > 0)
                        <tr class="text-center">
                            <th colspan="3">Total</th>
                            {{-- <td><b>{{ $salesDetail->sum('total_target_participant') ?? 0 }}</b></td>
                            <td><b>{{ number_format($salesDetail->sum('total_target'), '2', ',', '.') }}</b>
                            </td> --}}
                            <td><b>{{ $salesDetail->sum('total_actual_participant') ?? 0 }}</b></td>
                            <td><b>{{ number_format($salesDetail->sum('total_actual_amount'), '2', ',', '.') }}</b>
                            </td>
                            {{-- <td><b>{{ $salesDetail->sum('total_target_participant') != 0 ? round(($salesDetail->sum('total_actual_participant') / $salesDetail->sum('total_target_participant')) * 100, 2) : 0 }}%</b>
                            </td>
                            <td><b>{{ $salesDetail->sum('total_target') != 0 ? ($salesDetail->sum('total_actual_amount') / $salesDetail->sum('total_target')) * 100 : 0 }}%</b>
                            </td> --}}
                        </tr>
                    @else
                        <tr class="text-center">
                            <td colspan="5">No Data</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>