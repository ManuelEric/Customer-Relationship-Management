<div class="card mb-3">
    <div class="card-header">
        <h6 class="p-0 m-0">Admissions Mentoring Progress</h6>
    </div>
    <div class="card-body">
        @if (count($initAssessmentProgress) > 0)
            <div class="table-responsive">
                <table class="table table-bordered mb-3">
                    <thead>
                        <tr class="bg-secondary border-1 border-white text-white">
                            <th class="text-center">No</th>
                            <th>Program Name</th>
                            <th class="text-center">IC</th>
                            <th class="text-center">Success</th>
                            <th class="text-center">Conversion (%)</th>
                            <th class="text-center">IA Making</th>
                            <th class="text-center">Converted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $IC_total = $success_total = $conversion_total = $initialMaking_total = $converted_total = 0;
                        @endphp
                        @foreach ($initAssessmentProgress as $detail)
                            @php
                                $initialMaking = (int) $detail->initialMaking == 0 ? "less than a day" : (int) $detail->initialMaking . " day";
                                $converted = (int) $detail->converted == 0 ? "less than a day" : (int) $detail->converted . " day";
                                $IC = $detail->IC;
                                $success = $detail->success;
                                $conversion = round(($success / $IC) * 100);

                                $IC_total += $IC;
                                $success_total += $success;
                                $conversion_total += $conversion;
                                $initialMaking_total += $detail->initialMaking;
                                $converted_total += $detail->converted;

                            @endphp

                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $detail->program_name_st }}</td>
                                <td class="text-center">{{ $IC }}</td>
                                <td class="text-center">{{ $success }}</td>
                                <td class="text-center">{{ $conversion }}</td>
                                <td class="text-center">{{ $initialMaking }}</td>
                                <td class="text-center">{{ $converted }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $benchmark = (count($initAssessmentProgress))*100;
                            $avg_conversion = round(($conversion_total/$benchmark)*100);
                            $avg_initMaking = $initialMaking_total == 0 ? 0 : round(($initialMaking_total/$benchmark)*100) . ' days';
                            $avg_converted = $converted_total == 0 ? 0 : round(($converted_total/$benchmark)*100) . ' days';
                        @endphp
                        <tr class="fw-bold">
                            <td colspan="2">Total</td>
                            <td class="text-center">{{ $IC_total }}</td>
                            <td class="text-center">{{ $success_total }}</td>
                            <td class="text-center bg-secondary" colspan="3"></td>
                        </tr>
                        <tr class="fw-bold">
                            <td colspan="2">Average</td>
                            <td class="text-center bg-secondary" colspan="2"></td>
                            <td class="text-center">{{ $avg_conversion }}</td>
                            <td class="text-center">{{ $avg_initMaking }}</td>
                            <td class="text-center">{{ $avg_converted }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            No Data
        @endif
    </div>
</div>
