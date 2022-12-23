<div class="card rounded mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-person me-2"></i>
                Plan Follow-Up
            </h6>
        </div>
        <div class="">
            <button class="btn btn-sm btn-outline-primary rounded" id="open-followup" data-bs-toggle="modal" data-bs-target="#plan">
                <i class="bi bi-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="list-group">
            @forelse ($clientProgram->followUp as $plan)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="">
                        <div class="">{{ date('d M Y', strtotime($plan->followup_date)) }}</div>
                        <small>
                            {!! str_replace('<p>', '<p class="mt-2" style="font-size:10px">', $plan->notes) !!}
                        </small>
                    </div>
                    <div class="text-end">
                        {{-- <i class="bi bi-hourglass-split text-warning cursor-pointer" title="Update" data-bs-toggle="modal"
                            data-bs-target="#updatePlan"></i> --}}
                        <i class="bi bi-trash2 text-danger cursor-pointer" onclick="confirmDelete('client/student/{{ $student->id }}/program/{{ $clientProgram->clientprog_id }}/followup', {{ $plan->id }})"></i>
                    </div>
                </div>
            @empty
                There's no plan follow-up
            @endforelse
        </div>
    </div>
</div>

{{-- Add Follow Up  --}}
<div class="modal fade" id="plan" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <span>
                    Plan Follow-Up 
                </span>
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="modal-body w-100 text-start">
                <form action="{{ route('student.program.followup.store', ['student' => $student->id, 'program' => $clientProgram->clientprog_id]) }}" method="POST" id="formPosition">
                    @csrf
                    <div class="put"></div>
                    <div class="row g-2">
                        <div class="col-md-12 mb-2">
                            <label for="">
                                Follow-Up Date <sup class="text-danger">*</sup>
                            </label>
                            <input type="date" name="followup_date" value="{{ old('followup_date') }}"
                                class="form-control form-control-sm rounded">
                            @error('followup_date')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-12 mb-2">
                            <label for="">
                                Notes
                            </label>
                            <textarea name="notes" cols="30" rows="10">{{ old('notes') }}</textarea>
                            @error('notes')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="#" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-save2 me-1"></i>
                            Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Update Follow Up  --}}
<div class="modal fade" id="updatePlan" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <span>
                    Plan Follow-Up
                </span>
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="modal-body w-100 text-start">
                <form action="" method="POST" id="formPosition">
                    @csrf
                    <div class="put"></div>
                    <div class="row g-2">
                        <div class="col-md-12 mb-2">
                            <label for="">
                                Follow-Up Date <sup class="text-danger">*</sup>
                            </label>
                            <textarea name="" id=""></textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="#" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-save2 me-1"></i>
                            Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.modal-select').select2({
            dropdownParent: $('#speaker .modal-content'),
            placeholder: "Select value",
            allowClear: true
        });
    });
</script>
