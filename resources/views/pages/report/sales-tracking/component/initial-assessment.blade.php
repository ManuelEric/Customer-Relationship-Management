<div class="card mb-3">
    <div class="card-header">
        <h6 class="p-0 m-0">Admissions Mentoring Progress</h6>
    </div>
    <div class="card-body">
        @if (isset($initAssessmentProgress))
            <div class="table-responsive">
                <table class="table table-bordered mb-3">
                    <thead>
                        <tr class="bg-secondary border-1 border-white text-white">
                            <th class="text-center">No</th>
                            <th>Program Name</th>
                            <th class="text-center">IC</th>
                            <th class="text-center">Success</th>
                            <th class="text-center">Conversion %</th>
                            <th class="text-center">IA Making</th>
                            <th class="text-center">Converted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($initAssessmentProgress as $detail)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $detail->program_name_st }}</td>
                                <td class="text-center">Lorem</td>
                                <td class="text-center">Lorem</td>
                                <td class="text-center">Lorem</td>
                                <td class="text-center">{{ (int) $detail->initialMaking }} day</td>
                                <td class="text-center">{{ (int) $detail->converted }} day</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <th colspan="2">
                            Total
                        </th>
                        <th class="text-center">lorem</th>
                        <th class="text-center">lorem</th>
                        <th class="text-center">lorem</th>
                        <th class="text-center">lorem</th>
                        <th class="text-center">lorem</th>
                    </tfoot>
                </table>
            </div>
        @else
            No Data
        @endif
    </div>
</div>
