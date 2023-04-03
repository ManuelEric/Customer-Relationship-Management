<div class="card rounded">
    <div class="card-header">
        <h6 class="m-0 p-0">Attachment</h6>
    </div>
    <div class="card-body">
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-3">
                <label for="">Curriculum Vitae</label>
            </div>
            <div class="col-9">
                @if (!isset($user->cv))
                    <input type="file" name="curriculum_vitae" id="" class="form-control form-control-sm rounded" value="{{ isset($user->cv) ? $user->cv : old('curriculum_vitae') }}">
                    @error('curriculum_vitae')
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
                            <input type="file" name="curriculum_vitae" id="" class="form-control form-control-sm rounded" value="{{ old('curriculum_vitae') }}">
                            <i class="bi bi-backspace ms-2 cursor-pointer text-danger rollback"></i>
                        </div>
                        @error('curriculum_vitae')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                @endif
            </div>
        </div>
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-3">
                <label for="">BCA Account</label>
            </div>
            <div class="col-9 d-flex">
                <div class="w-50 me-2">
                    <small>Account Name <sup class="text-danger">*</sup></small>
                    <input type="text" name="bankname" class="form-control form-control-sm rounded" value="{{ isset($user->bankname) ? $user->bankname : old('bankname') }}">
                    @error('bankname')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
                <div class="w-50">
                    <small>Account Number <sup class="text-danger">*</sup></small>
                    <input type="text" name="bankacc" class="form-control form-control-sm rounded" value="{{ isset($user->bankacc) ? $user->bankacc : old('bankacc') }}">
                    @error('bankacc')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-3">
                <label for="">KTP</label>
            </div>
            <div class="col-9 d-flex">
                <div class="w-50 me-2">
                    <small>NIK <sup class="text-danger">*</sup></small>
                    <input type="text" name="nik" class="form-control form-control-sm rounded" value="{{ isset($user->nik) ? $user->nik : old('nik') }}">
                    @error('nik')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
                <div class="w-50">
                    @if (!isset($user->idcard))
                        <small>Image <sup class="text-danger">*</sup></small>
                        <input type="file" name="idcard" class="form-control form-control-sm rounded" value="{{ old('idcard') }}">
                        @error('idcard')
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
                                <input type="file" name="idcard" class="form-control form-control-sm rounded" value="{{ old('idcard') }}">
                                <i class="bi bi-backspace ms-2 cursor-pointer text-danger rollback"></i>
                            </div>
                            @error('idcard')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-3">
                <label for="">NPWP</label>
            </div>
            <div class="col-9 d-flex">
                <div class="w-50 me-2">
                    <small>NPWP Number</small>
                    <input type="text" name="npwp" id="" class="form-control form-control-sm rounded" value="{{ isset($user->npwp) ? $user->npwp : old('npwp') }}">
                    @error('npwp')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
                <div class="w-50">
                    @if (!isset($user->tax))
                        <small>Image </small>
                        <input type="file" name="tax" id="" class="form-control form-control-sm rounded" value="{{ old('tax') }}">
                        @error('tax')
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
                                <input type="file" name="tax" id="" class="form-control form-control-sm rounded" value="{{ old('tax') }}">
                                <i class="bi bi-backspace ms-2 cursor-pointer text-danger rollback"></i>
                            </div>
                            @error('tax')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror   
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-3">
                <label for="">BPJS Kesehatan </label>
            </div>
            <div class="col-9">
                @if (!isset($user->health_insurance))
                    <input type="file" name="health_insurance" class="form-control form-control-sm rounded" value="{{ old('health_insurance') }}">
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
                            <input type="file" name="health_insurance" class="form-control form-control-sm rounded" value="{{ old('health_insurance') }}">
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
            <div class="col-3">
                <label for="">BPJS Ketenagakerjaan </label>
            </div>
            <div class="col-9">
                @if (!isset($user->empl_insurance))
                    <input type="file" name="empl_insurance" class="form-control form-control-sm rounded" value="{{ old('empl_insurance') }}">
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
                            <input type="file" name="empl_insurance" class="form-control form-control-sm rounded" value="{{ old('empl_insurance') }}">
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

