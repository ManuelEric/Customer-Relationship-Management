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
                        <input type="date" name="initconsult_date"
                            class="form-control form-control-sm rounded">
                        @error('initconsult_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <small>Initial Assessment Sent <sup class="text-danger">*</sup></small>
                        <input type="date" name="assessmentsent_date"
                            class="form-control form-control-sm rounded">
                        @error('assessmentsent_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2 ">
                    <div class="col-md-12 mb-2">
                        <small>End Date <sup class="text-danger">*</sup></small>
                        <input type="date" name="prog_end_date"
                            class="form-control form-control-sm rounded">
                        @error('prog_end_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Total Universities <sup class="text-danger">*</sup></small>
                        <input type="number" name="total_uni"
                            class="form-control form-control-sm rounded">
                        @error('total_uni')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Total Dollar <sup class="text-danger">*</sup></small>
                        <input type="number" name="total_foreign_currency"
                            class="form-control form-control-sm rounded">
                        @error('total_foreign_currency')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                        <input type="hidden" name="foreign_currency" value="usd">
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Kurs Dollar-Rupiah <sup class="text-danger">*</sup></small>
                        <input type="number" name="foreign_currency_exchange"
                            class="form-control form-control-sm rounded">
                        @error('foreign_currency_exchange')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Total Rupiah <sup class="text-danger">*</sup></small>
                        <input type="number" name="total_idr" id=""
                            class="form-control form-control-sm rounded">
                        @error('total_idr')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Main Mentor <sup class="text-danger">*</sup></small>
                        <select name="main_mentor" id="" class="select w-100">
                            <option data-placeholder="true"></option>
                            @foreach ($mentors as $mentor)
                                <option value="{{ $mentor->id }}">{{ $mentor->first_name.' '.$mentor->last_name }}</option>
                            @endforeach
                        </select>
                        @error('main_mentor')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Backup Mentor</small>
                        <select name="backup_mentor" id="" class="select w-100">
                            <option data-placeholder="true"></option>
                            @foreach ($mentors as $mentor)
                                <option value="{{ $mentor->id }}">{{ $mentor->first_name.' '.$mentor->last_name }}</option>
                            @endforeach
                        </select>
                        @error('backup_mentor')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-12 mb-2">
                        <small>Installment Plan</small>
                        <textarea name="installment_notes"></textarea>
                        @error('installment_notes')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
