<div class="row mb-3">
    <div class="col-md-3">
        <label for="">
            Program Detail
        </label>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                Academic Tutoring Program
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-12 mb-2">
                        <small>Trial Date</small>
                        <input type="date" name="pend_trial_date" {{ $disabled }} value="{{ isset($clientProgram->trial_date) ? $clientProgram->trial_date : old('trial_date') }}"
                            class="form-control form-control-sm rounded">
                        @error('pend_trial_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
