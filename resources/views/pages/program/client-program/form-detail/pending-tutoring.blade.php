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
            <div class="card-body pending-tutoring-container">
                <div class="row mb-2">

                    <!-- Detail for Test Preparation -->
                    <div class="col-md-12 mb-2 pending-tutoring-test-preparation-field d-none">
                        <small>Package <sup class="text-danger">*</sup></small>
                        <x-forms.program.detail.package program-type="test-preparation" text-index="0" :$disabled @if(isset($clientProgram)) :$clientProgram @endif />
                    </div>

                    <!-- Detail for Subject Tutoring -->
                    <div class="col-md-12 mb-2 pending-tutoring-subject-tutoring-field d-none">
                        <small>Package <sup class="text-danger">*</sup></small>
                        <x-forms.program.detail.package program-type="subject-tutoring" text-index="0" :$disabled @if(isset($clientProgram)) :$clientProgram @endif />
                    </div>
                    <div class="col-md-12 mb-2 pending-tutoring-subject-tutoring-field curriculum-box d-none">
                        <small>Curriculum <sup class="text-danger">*</sup></small>
                        <x-forms.program.detail.curriculum program-type="subject-tutoring" text-index="0" :$disabled @if(isset($clientProgram)) :$clientProgram @endif />
                    </div>

                    <!-- Detail for Competition -->
                    <div class="col-md-12 mb-2 pending-tutoring-competition-field d-none">
                        <small>Package <sup class="text-danger">*</sup></small>
                        <x-forms.program.detail.package program-type="competition" text-index="0" :$disabled @if(isset($clientProgram)) :$clientProgram @endif />
                    </div>

                    <!-- Detail for Skillset Tutoring -->
                    <div class="col-md-12 mb-2 pending-tutoring-skillset-tutoring-field d-none">
                        <small>Package <sup class="text-danger">*</sup></small>
                        <x-forms.program.detail.package program-type="skillset-tutoring" text-index="0" :$disabled @if(isset($clientProgram)) :$clientProgram @endif />
                    </div>

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
