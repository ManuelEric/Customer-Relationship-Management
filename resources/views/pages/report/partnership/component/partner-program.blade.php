<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="p-0 m-0">Partner Program</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover nowrap align-middle w-100 table2excel" id="tblpartner_prog">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Partner Name</th>
                        <th>Program Name</th>
                        <th>Program Date</th>
                        <th>Participants</th>
                        <th>Amount</th>
                        <th>PIC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($partnerPrograms as $partnerProgram)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $partnerProgram->corp->corp_name }}</td>
                            <td>{{ $partnerProgram->program->program_name }}</td>
                            <td>{{ $partnerProgram->success_date }}</td>
                            <td>{{ $partnerProgram->participants }}</td>
                            <td>Rp. {{ number_format($partnerProgram->total_fee) }}</td>
                            <td>{{ $partnerProgram->user->first_name }} {{ $partnerProgram->user->last_name }}</td>
                        </tr>
                    @empty
                        <td colspan="7" class="text-center">Not partner program yet</td>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5">Total Amount</th>
                        <th colspan="2" class="text-center">Rp.
                            {{ number_format($partnerPrograms->sum('total_fee')) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
