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
                        <th colspan="2" class="bg-secondary border-1 border-white">Actual Sales</th>
                    </tr>
                    <tr class="text-center text-white">
                        <td class="bg-secondary border-1 border-white">Students</td>
                        <td class="bg-secondary border-1 border-white">Total Amount</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($actual_sales as $detail)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $detail->prog_id }}</td>
                            <td class="text-start">{{ $detail->program_name_sales }}</td>
                            <td>{{ $detail->total_actual_participant }}</td>
                            <td>{{ number_format($detail->total_actual_amount, '2', ',', '.') }}</td>
                        </tr>
                    @endforeach
                    @if(count($actual_sales) > 0)
                        <tr class="text-center">
                            <th colspan="3">Total</th>
                            <td><b>{{ $actual_sales->sum('total_actual_participant') ?? 0 }}</b></td>
                            <td><b>{{ number_format($actual_sales->sum('total_actual_amount'), '2', ',', '.') }}</b>
                            </td>
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