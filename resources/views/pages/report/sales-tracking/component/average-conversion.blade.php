<div class="card mb-3">
    <div class="card-header">
        <h6 class="p-0 m-0">
            Average Conversion Time to Successful Programs</h6>
    </div>
    <div class="card-body">
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
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $detail->program_name_st }}</td>
                            <td class="text-end">{{ (int) $detail->average_time }} days</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
