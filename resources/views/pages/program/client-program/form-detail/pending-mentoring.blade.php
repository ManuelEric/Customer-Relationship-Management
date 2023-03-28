<div class="row mb-3">
    <div class="col-md-3">
        <label for="">
            Program Detail <sup class="text-danger">*</sup>
        </label>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                Admissions Program
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <small>Initial Consult Date <sup class="text-danger">*</sup></small>
                        <input type="date" name="pend_initconsult_date" {{ $disabled }}
                            class="form-control form-control-sm rounded" value="{{ isset($clientProgram->initconsult_date) ? $clientProgram->initconsult_date : old('pend_initconsult_date') }}">
                        @error('pend_initconsult_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <small>Initial Assessment Sent</small>
                        <input type="date" name="pend_assessmentsent_date" {{ $disabled }} 
                            class="form-control form-control-sm rounded" value="{{ isset($clientProgram->assessmentsent_date) ? $clientProgram->assessmentsent_date : old('pend_assessmentsent_date') }}">
                        @error('pend_assessmentsent_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
