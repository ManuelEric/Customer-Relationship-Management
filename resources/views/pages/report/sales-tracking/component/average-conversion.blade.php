<div class="card mb-3">
    <div class="card-header">
        <h6 class="p-0 m-0">
            Average Conversion Time to Successful Programs</h6>
    </div>
    <div class="card-body">
        @if (count($averageConversionSuccessful) > 0)
        <div class="table-responsive">
            <table class="table mb-3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Program Name</th>
                        <th class="text-end">Average Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($averageConversionSuccessful as $detail)
                        @php
                            $average_time = (int) $detail->average_time == 0 ? 'less than a day' : (int) $detail->average_time. ' days';
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $detail->program_name_st }}</td>
                            <td class="text-end">{{ $average_time }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="my-1">No Data</div>
        @endif
    </div>
</div>
