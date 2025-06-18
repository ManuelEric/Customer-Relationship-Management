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
                Academic Tutoring Program
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-12 mb-2">
                        <small>Package <sup class="text-danger">*</sup></small>
                        <div v-if="main_prog==4">
                            <x-forms.program.detail.package program-type="test-preparation" text-index="1"
                                :disabled=$disabled :client-program=$clientProgram />
                        </div>
                        <div v-if="main_prog==7">
                            <x-forms.program.detail.package program-type="subject-tutoring" text-index="1"
                                :disabled=$disabled :client-program=$clientProgram />
                        </div>
                        <div v-if="main_prog==8">
                            <x-forms.program.detail.package program-type="competition" text-index="1"
                                :disabled=$disabled :client-program=$clientProgram />
                        </div>
                        <div v-if="main_prog==9">
                            <x-forms.program.detail.package program-type="skillset-tutoring" text-index="1"
                                :disabled=$disabled :client-program=$clientProgram />
                        </div>
                    </div>

                    <div class="col-md-12 mb-2" v-if="main_prog==7">
                        <small>Curriculum <sup class="text-danger">*</sup></small>
                        <x-forms.program.detail.curriculum program-type="subject-tutoring" text-index="1"
                            :disabled=$disabled :client-program=$clientProgram />
                    </div>

                    <div class="col-md-12 mb-2">
                        <small>Trial Date</small>
                        <input type="date" name="trial_date" {{ $disabled }}
                            value="{{ isset($clientProgram->trial_date) ? $clientProgram->trial_date : old('trial_date') }}"
                            class="form-control form-control-sm rounded">
                        @error('trial_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2" v-if="status==1">
                        <small>Start Date <sup class="text-danger">*</sup></small>
                        <input type="date" name="prog_start_date" {{ $disabled }}
                            value="{{ isset($clientProgram->prog_start_date) ? $clientProgram->prog_start_date : old('prog_start_date') }}"
                            class="form-control form-control-sm rounded">
                        @error('prog_start_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2" v-if="status==1">
                        <small>End Date <sup class="text-danger">*</sup></small>
                        <input type="date" name="prog_end_date" {{ $disabled }}
                            value="{{ isset($clientProgram->prog_end_date) ? $clientProgram->prog_end_date : old('prog_end_date') }}"
                            class="form-control form-control-sm rounded">
                        @error('prog_end_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-12 mb-2" v-if="status==1">
                        <small>Timesheet Link <sup class="text-danger">*</sup></small>
                        <input type="url" name="timesheet_link" {{ $disabled }}
                            value="{{ isset($clientProgram->timesheet_link) ? $clientProgram->timesheet_link : old('timesheet_link') }}"
                            class="form-control form-control-sm rounded">
                        @error('timesheet_link')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
