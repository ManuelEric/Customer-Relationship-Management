<div class="card mb-3">
    <div class="card-header">
        <h6 class="p-0 m-0">Initial Assessment Progress</h6>
    </div>
    <div class="card-body">
        @if (isset($initAssessmentProgress))
            <div class="table-responsive">
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
            </div>
        @else
            No Data
        @endif
    </div>
</div>
