<div class="card rounded">
    <div class="card-header">
        <h6 class="m-0 p-0">Attachment</h6>
    </div>
    <div class="card-body">
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="">Curriculum Vitae <sup class="text-danger">*</sup></label>
            </div>
            <div class="col-md-9">
                @if (!isset($volunteer->volunt_cv))
                    <input type="file" name="volunt_cv" id="" class="form-control form-control-sm rounded">
                    @error('volunt_cv')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                @else
                    <div class="curriculum-vitae-container">
                        <button type="button" class="btn btn-sm btn-info download">
                            <i class="bi bi-download"></i>
                            Download
                        </button>
                        <button type="button" class="btn btn-sm btn-danger remove">
                            <i class="bi bi-trash"></i>
                        </button>
                        <div class="upload-file d-flex justify-content-center align-items-center d-none">
                            <input type="file" name="volunt_cv" id=""
                                class="form-control form-control-sm rounded" value="{{ old('volunt_cv') }}">
                            <i class="bi bi-backspace ms-2 cursor-pointer text-danger rollback"></i>
                        </div>
                        @error('volunt_cv')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                @endif
            </div>
        </div>
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="">BCA Account <sup class="text-danger">*</sup></label>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6 mb-1 mb-md-0">
                        <small>Account Name <sup class="text-danger">*</sup></small>
                        <input type="text" name="volunt_bank_accname" id=""
                            class="form-control form-control-sm rounded"
                            value="{{ isset($volunteer->volunt_bank_accname) ? $volunteer->volunt_bank_accname : old('volunt_bank_accname') }}">
                        @error('volunt_bank_accname')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <small>Account Number <sup class="text-danger">*</sup></small>
                        <input type="text" name="volunt_bank_accnumber" id=""
                            class="form-control form-control-sm rounded"
                            value="{{ isset($volunteer->volunt_bank_accnumber) ? $volunteer->volunt_bank_accnumber : old('volunt_bank_accnumber') }}">
                        @error('volunt_bank_accnumber')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
            </div>
        </div>
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="">KTP <sup class="text-danger">*</sup></label>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6 mb-1 mb-md-0">
                        <small>NIK <sup class="text-danger">*</sup></small>
                        <input type="number" name="volunt_nik" id=""
                            class="form-control form-control-sm rounded"
                            value="{{ isset($volunteer->volunt_nik) ? $volunteer->volunt_nik : old('volunt_nik') }}">
                        @error('volunt_nik')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        @if (!isset($volunteer->volunt_idcard))
                            <small>Image <sup class="text-danger">*</sup></small>
                            <input type="file" name="volunt_idcard" id=""
                                class="form-control form-control-sm rounded">
                            @error('volunt_idcard')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        @else
                            <small>Image <sup class="text-danger">*</sup></small>
                            <div class="ktp-container">
                                <button type="button" class="btn btn-sm btn-info download">
                                    <i class="bi bi-download"></i>
                                    Download
                                </button>
                                <button type="button" class="btn btn-sm btn-danger remove">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <div class="upload-file d-flex justify-content-center align-items-center d-none">
                                    <input type="file" name="volunt_idcard"
                                        class="form-control form-control-sm rounded"
                                        value="{{ old('volunt_idcard') }}">
                                    <i class="bi bi-backspace ms-2 cursor-pointer text-danger rollback"></i>
                                </div>
                                @error('volunt_idcard')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="">NPWP</label>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6 mb-1 mb-md-0">
                        <small>NPWP Number</small>
                        <input type="text" name="volunt_npwp_number" id=""
                            class="form-control form-control-sm rounded"
                            value="{{ isset($volunteer->volunt_npwp_number) ? $volunteer->volunt_npwp_number : old('volunt_npwp_number') }}">
                        @error('volunt_npwp_number')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        @if (!isset($volunteer->volunt_npwp))
                            <small>Image</small>
                            <input type="file" name="volunt_npwp" id=""
                                class="form-control form-control-sm rounded">
                            @error('volunt_npwp')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        @else
                            <small>Image </small>
                            <div class="tax-container">
                                <button type="button" class="btn btn-sm btn-info download">
                                    <i class="bi bi-download"></i>
                                    Download
                                </button>
                                <button type="button" class="btn btn-sm btn-danger remove">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <div class="upload-file d-flex justify-content-center align-items-center d-none">
                                    <input type="file" name="volunt_npwp" id=""
                                        class="form-control form-control-sm rounded"
                                        value="{{ old('volunt_npwp') }}">
                                    <i class="bi bi-backspace ms-2 cursor-pointer text-danger rollback"></i>
                                </div>
                                @error('volunt_npwp')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="">BPJS Kesehatan</label>
            </div>
            <div class="col-md-9">
                @if (!isset($volunteer->health_insurance))
                    <input type="file" name="health_insurance" id=""
                        class="form-control form-control-sm rounded">
                    @error('health_insurance')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                @else
                    <div class="hi-container">
                        <button type="button" class="btn btn-sm btn-info download">
                            <i class="bi bi-download"></i>
                            Download
                        </button>
                        <button type="button" class="btn btn-sm btn-danger remove">
                            <i class="bi bi-trash"></i>
                        </button>
                        <div class="upload-file d-flex justify-content-center align-items-center d-none">
                            <input type="file" name="health_insurance"
                                class="form-control form-control-sm rounded" value="{{ old('health_insurance') }}">
                            <i class="bi bi-backspace ms-2 cursor-pointer text-danger rollback"></i>
                        </div>
                        @error('health_insurance')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                @endif
            </div>
        </div>
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="">BPJS Ketenagakerjaan</label>
            </div>
            <div class="col-md-9">
                @if (!isset($volunteer->empl_insurance))
                    <input type="file" name="empl_insurance" id=""
                        class="form-control form-control-sm rounded">
                    @error('empl_insurance')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                @else
                    <div class="ei-container">
                        <button type="button" class="btn btn-sm btn-info download">
                            <i class="bi bi-download"></i>
                            Download
                        </button>
                        <button type="button" class="btn btn-sm btn-danger remove">
                            <i class="bi bi-trash"></i>
                        </button>
                        <div class="upload-file d-flex justify-content-center align-items-center d-none">
                            <input type="file" name="empl_insurance" class="form-control form-control-sm rounded"
                                value="{{ old('empl_insurance') }}">
                            <i class="bi bi-backspace ms-2 cursor-pointer text-danger rollback"></i>
                        </div>
                        @error('empl_insurance')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
