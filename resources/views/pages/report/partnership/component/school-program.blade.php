<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="p-0 m-0">School Program</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover nowrap align-middle w-100 table2excel" id="tblsch_prog">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>School Name</th>
                        <th>Program Name</th>
                        <th>Program Date</th>
                        <th>Participants</th>
                        <th>Amount</th>
                        <th>PIC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($school_programs as $schoolProgram)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $schoolProgram->school->sch_name }}</td>
                            <td>{{ $schoolProgram->program->program_name }}</td>
                            <td>{{ $schoolProgram->success_date }}</td>
                            <td>{{ $schoolProgram->participants }}</td>
                            <td>Rp. {{ number_format($schoolProgram->total_fee) }}</td>
                            <td>{{ $schoolProgram->user->first_name }} {{ $schoolProgram->user->last_name }}</td>
                        </tr>
                    @empty
                        <td colspan="7" class="text-center">Not school program yet</td>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5">Total Amount</th>
                        <th colspan="2" class="text-center">Rp.
                            {{ number_format($school_programs->sum('total_fee')) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>
