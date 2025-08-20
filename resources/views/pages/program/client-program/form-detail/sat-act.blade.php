@php
    $clientProgram = isset($clientProgram) ? $clientProgram : null;
@endphp
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
                    <!-- Detail for Test Preparation -->
                    <div class="col-md-12 mb-2">
                        <small>Package <sup class="text-danger">*</sup></small>
                        <x-forms.program.detail.package program-type="test-preparation" text-index="1"
                            :disabled=$disabled :client-program=$clientProgram />
                    </div>

                    <div class="col-md-12 mb-2">
                        <small>Test Date <sup class="text-danger">*</sup></small>
                        <input type="date" name="test_date" {{ $disabled }}
                            value="{{ isset($clientProgram->test_date) ? $clientProgram->test_date : old('test_date') }}"
                            class="form-control form-control-sm rounded">
                        @error('test_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2" v-if="status==1">
                        <small>First Class Date <sup class="text-danger">*</sup></small>
                        <input type="date" name="first_class" {{ $disabled }}
                            value="{{ isset($clientProgram->first_class) ? $clientProgram->first_class : old('first_class') }}"
                            class="form-control form-control-sm rounded">
                        @error('first_class')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2" v-if="status==1">
                        <small>Last Class Date <sup class="text-danger">*</sup></small>
                        <input type="date" name="last_class" {{ $disabled }}
                            value="{{ isset($clientProgram->last_class) ? $clientProgram->last_class : old('last_class') }}"
                            class="form-control form-control-sm rounded">
                        @error('last_class')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2" v-if="status==1">
                        <small>Diagnostic Score <sup class="text-danger">*</sup></small>
                        <input type="number" name="diag_score" {{ $disabled }}
                            value="{{ isset($clientProgram->diag_score) ? $clientProgram->diag_score : old('diag_score') }}"
                            class="form-control form-control-sm rounded">
                        @error('diag_score')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2" v-if="status==1">
                        <small>Test Score <sup class="text-danger">*</sup></small>
                        <input type="number" name="test_score" {{ $disabled }}
                            value="{{ isset($clientProgram->test_score) ? $clientProgram->test_score : old('test_score') }}"
                            class="form-control form-control-sm rounded">
                        @error('test_score')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
