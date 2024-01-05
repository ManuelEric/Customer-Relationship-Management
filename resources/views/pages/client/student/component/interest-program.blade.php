<div class="card rounded mb-2">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="m-0 p-0">Interest Program</h5>
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addInterestProgram"><i
                    class="bi bi-plus"></i></button>
        </div>
    </div>
    <div class="card-body overflow-auto" style="max-height: 150px;">

        @forelse ($student->interestPrograms as $program)
            <div class="d-flex align-items-center justify-content-between w-100 ">
                <a href="{{ url('client/student/' . $student->id . '/program/create?p=' . $program->prog_id) }}"
                    class="text-decoration-none text-info" style="font-size:12px;">
                    <div>
                        <i class="bi bi-arrow-right"></i>
                        {{ $program->program_name }}
                    </div>
                </a>
                <div class="">
                    <i class="bi bi-clock-history"></i>
                    {{ date('d/m/Y H:i:s', strtotime($program->pivot->created_at)) }}
                    <button class="btn btn-sm btn-light text-danger ms-2 p-1"
                        onclick="confirmDelete('client/student/{{$student->id}}/interest_program/{{$program->pivot->id}}', '{{ $program->prog_id }}')">
                        <i class="bi bi-trash2"></i>
                    </button>
                </div>
            </div>
            <hr class="my-1">
        @empty
            There's no interest program yet
        @endforelse
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addInterestProgram" tabindex="-1" aria-labelledby="addInterestProgramLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addInterestProgramLabel">Interest Program</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('student.add.interest.program', ['student' => $student->id]) }}" method="post">
                    @csrf
                    <label for="">Program Name</label>
                    <select name="interest_program" id="" class="modal-select2 w-100">
                        <option data-placeholder="true"></option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->prog_id }}"
                                {{ old('interest_program') == $program->prog_id ? 'selected' : null }}>
                                {{ $program->program_name }}</option>
                        @endforeach
                    </select>
                    @error('interest_program')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                    <div class="mt-3 text-center">
                        <button class="btn btn-sm btn-primary">
                            <i class="bi bi-save"></i>
                            Save changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if ($errors->has('interest_program'))
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#addInterestProgram').modal('show');
        })
    </script>
@endpush
@endif
