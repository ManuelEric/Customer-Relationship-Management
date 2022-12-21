<div class="row mb-3">
    <div class="col-md-3">
        <label for="">
            Program Detail <sup class="text-danger">*</sup>
        </label>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                SAT/ACT Program
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <small>Test Date <sup class="text-danger">*</sup></small>
                        <input type="date" name="test_date"
                            class="form-control form-control-sm rounded">
                        @error('test_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Last Class Date <sup class="text-danger">*</sup></small>
                        <input type="date" name="last_class"
                            class="form-control form-control-sm rounded">
                        @error('last_class')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Diagnostic Score <sup class="text-danger">*</sup></small>
                        <input type="number" name="diag_score" 
                            class="form-control form-control-sm rounded">
                        @error('diag_score')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Test Score <sup class="text-danger">*</sup></small>
                        <input type="number" name="test_score" 
                            class="form-control form-control-sm rounded">
                        @error('test_score')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6 mb-2">
                        <small>Tutor 1 <sup class="text-danger">*</sup></small>
                        <select name="tutor_1" class="select w-100">
                            <option data-placeholder="true"></option>
                            @foreach ($tutors as $tutor)
                                <option value="{{ $tutor->id }}">{{ $tutor->first_name.' '.$tutor->last_name.' - '.json_encode($tutor->roles()->where('role_name', 'Tutor')->pluck('tutor_subject')->toArray()) }}</option>
                            @endforeach
                        </select>
                        @error('tutor_1')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Tutor 2</small>
                        <select name="tutor_2" class="select w-100">
                            <option data-placeholder="true"></option>
                            @foreach ($tutors as $tutor)
                                <option value="{{ $tutor->id }}">{{ $tutor->first_name.' '.$tutor->last_name.' - '.json_encode($tutor->roles()->where('role_name', 'Tutor')->pluck('tutor_subject')->toArray()) }}</option>
                            @endforeach
                        </select>
                        @error('tutor_2')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
