<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-building me-2"></i>
                Programs
            </h6>
        </div>
        <div class="">
            <a href="{{ url('program/corporate/' . strtolower($corporate->corp_id) . '/detail/create') }}"
                class="btn btn-sm btn-outline-primary rounded mx-1">
                <i class="bi bi-plus"></i>
            </a>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-borderless table-hover"
            style="display: block; overflow-x: auto; white-space: nowrap;">
            <tr>
                <th>No.</th>
                <th>Program Name</th>
                <th class="text-center">ALL-in PIC</th>
                <th class="text-center">First Discuss</th>
                <th class="text-center">Status</th>
                <th class="text-end">Action</th>
            </tr>
            @forelse ($partnerPrograms as $partnerProgram)
                <tr>
                    <td>{{ $loop->iteration }}.</td>
                    <td>{{ $partnerProgram->program->prog_program }}</td>
                    <td class="text-center">{{ $partnerProgram->user->first_name }}
                        {{ $partnerProgram->user->last_name }}</td>
                    <td class="text-center">{{ $partnerProgram->first_discuss }}</td>
                    <td class="text-center">
                        @if ($partnerProgram->status == 0)
                            Pending
                        @elseif ($partnerProgram->status == 1)
                            Success
                        @elseif ($partnerProgram->status == 2)
                            Rejected
                        @elseif ($partnerProgram->status == 3)
                            Refund
                        @elseif ($partnerProgram->status == 4)
                            Accepted
                        @elseif ($partnerProgram->status == 5)
                            Cancel
                        @endif
                    </td>
                    <td class="text-end">
                        <a
                            href="{{ route('corporate_prog.detail.show', ['corp' => $corporate->corp_id, 'detail' => $partnerProgram->id]) }}">
                            <button type="button" class="btn btn-sm btn-outline-warning editCorporate"><i
                                    class="bi bi-eye"></i></button>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td>No program yet</td>
                </tr>
            @endforelse
        </table>
    </div>
</div>
