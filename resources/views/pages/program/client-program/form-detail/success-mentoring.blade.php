<div class="row mb-3 ">
    <div class="col-md-3">
        <label for="">
            Program Detail <sup class="text-danger">*</sup>
        </label>
    </div>
    <div class="col-md-9">
        <div class="card ">
            <div class="card-header">
                Admissions Program
            </div>
            <div class="card-body">
                <div class="row mb-2 ">
                    <div class="col-md-6">
                        <small>Initial Consult Date <sup class="text-danger">*</sup></small>
                        <input type="date" name="initconsult_date" {{ $disabled }} value="{{ isset($clientProgram->initconsult_date) ? $clientProgram->initconsult_date : old('initconsult_date') }}"
                            class="form-control form-control-sm rounded">
                        @error('initconsult_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <small>Initial Assessment Sent <sup class="text-danger">*</sup></small>
                        <input type="date" name="assessmentsent_date" {{ $disabled }} value="{{ isset($clientProgram->assessmentsent_date) ? $clientProgram->assessmentsent_date : old('assessmentsent_date') }}"
                            class="form-control form-control-sm rounded">
                        @error('assessmentsent_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2 ">
                    <div class="col-md-12 mb-2">
                        <small>End Date <sup class="text-danger">*</sup></small>
                        <input type="date" name="mentoring_prog_end_date" {{ $disabled }} value="{{ isset($clientProgram->prog_end_date) ? $clientProgram->prog_end_date : old('prog_end_date') }}"
                            class="form-control form-control-sm rounded">
                        @error('prog_end_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Total Universities <sup class="text-danger">*</sup></small>
                        <input type="number" name="total_uni" {{ $disabled }} value="{{ isset($clientProgram->total_uni) ? $clientProgram->total_uni : old('total_uni') }}"
                            class="form-control form-control-sm rounded">
                        @error('total_uni')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Total Dollar <sup class="text-danger">*</sup></small>
                        <input type="number" name="total_foreign_currency" {{ $disabled }} value="{{ isset($clientProgram->total_foreign_currency) ? $clientProgram->total_foreign_currency : old('total_foreign_currency') }}"
                            class="form-control form-control-sm rounded">
                        @error('total_foreign_currency')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                        <input type="hidden" name="foreign_currency" value="usd">
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Kurs Dollar-Rupiah <sup class="text-danger">*</sup></small>
                        <input type="number" name="foreign_currency_exchange" {{ $disabled }} value="{{ isset($clientProgram->foreign_currency_exchange) ? $clientProgram->foreign_currency_exchange : old('foreign_currency_exchange') }}"
                            class="form-control form-control-sm rounded">
                        @error('foreign_currency_exchange')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Total Rupiah <sup class="text-danger">*</sup></small>
                        <input type="number" name="total_idr" id="" {{ $disabled }} value="{{ isset($clientProgram->total_idr) ? $clientProgram->total_idr : old('total_idr') }}"
                            class="form-control form-control-sm rounded">
                        @error('total_idr')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Main Mentor <sup class="text-danger">*</sup></small>
                        <select name="main_mentor" id="" class="select w-100" {{ $disabled }}>
                            <option data-placeholder="true"></option>
                            @foreach ($mentors as $mentor)
                                <option value="{{ $mentor->id }}"
                                    @if (old('main_mentor') == $mentor->id)
                                        {{ "selected" }}
                                    @elseif (isset($clientProgram->clientMentor) && $clientProgram->clientMentor()->orderBy('tbl_client_mentor.id', 'asc')->count() > 0)
                                        @if ($clientProgram->clientMentor()->orderBy('tbl_client_mentor.id', 'asc')->first()->id == $mentor->id)
                                            {{ "selected" }}
                                        @endif
                                    @endif
                                    >{{ $mentor->first_name.' '.$mentor->last_name }}</option>
                            @endforeach
                        </select>
                        @error('main_mentor')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Backup Mentor</small>
                        <select name="backup_mentor" id="" class="select w-100" {{ $disabled }}>
                            <option data-placeholder="true"></option>
                            @foreach ($mentors as $mentor)
                                <option value="{{ $mentor->id }}"
                                    @if (old('backup_mentor') == $mentor->id)
                                        {{ "selected" }}
                                    @elseif (isset($clientProgram->clientMentor) && $clientProgram->clientMentor()->orderBy('tbl_client_mentor.id', 'desc')->count() > 0)
                                        @if ($clientProgram->clientMentor()->orderBy('tbl_client_mentor.id', 'desc')->first()->id == $mentor->id)
                                            {{ "selected" }}
                                        @endif
                                    @endif
                                    >{{ $mentor->first_name.' '.$mentor->last_name }}</option>
                            @endforeach
                        </select>
                        @error('backup_mentor')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-12 mb-2">
                        <small>Installment Plan</small>
                        <textarea name="installment_notes" {{ $disabled }}>
                            @if (old('installment_notes'))
                                {{ old('installment_notes') }}
                            @elseif (isset($clientProgram->installment_notes))
                                {{ $clientProgram->installment_notes }}
                            @endif
                        </textarea>
                        @error('installment_notes')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
