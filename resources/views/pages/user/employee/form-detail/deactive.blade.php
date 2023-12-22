

<!-- Modal -->
<div class="modal fade" id="modalDeactive" tabindex="-1" aria-labelledby="modalDeactiveLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalDeactiveLabel">Deactive Employee</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    @csrf
                    <div class="mb-2">
                        <label for="">Deactive Date</label>
                        <input class="form-control form-control-sm rounded" type="date" name="deativated_at">
                        @error('deativated_at')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    {{-- <div class="mb-2">
                        <label for="">Change PIC</label>
                        <select name="pic_id" id="" class="modal-select w-100">
                            <option data-placeholder="true"></option>
                            @foreach ($salesTeams as $salesTeam)
                                <option value="{{ $salesTeam->id }}"
                                    {{ old('pic_id') == $salesTeam->id ? 'selected' : null }}>
                                    {{ $salesTeam->full_name }}</option>
                            @endforeach
                        </select>
                        @error('pic_id')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div> --}}
                    <div class="mt-3 text-center">
                        <button class="btn btn-sm btn-primary">
                            <i class="bi bi-save"></i>
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if ($errors->has('deativated_at') || $errors->has('pic_id'))
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#modalDeactive').modal('show');
        })
    </script>
@endpush
@endif
