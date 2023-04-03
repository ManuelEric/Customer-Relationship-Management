<div class="card rounded">
    <div class="card-header">
        <h6 class="m-0 p-0">Attachment</h6>
    </div>
    <div class="card-body">
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-3">
                <label for="">Curriculum Vitae <sup class="text-danger">*</sup></label>
            </div>
            <div class="col-9">
                <input type="file" name="volunt_cv" id="" class="form-control form-control-sm rounded">
                @error('volunt_cv')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
        </div>
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-3">
                <label for="">BCA Account <sup class="text-danger">*</sup></label>
            </div>
            <div class="col-9 d-flex">
                <div class="w-50 me-2">
                    <small>Account Name <sup class="text-danger">*</sup></small>
                    <input type="text" name="volunt_bank_accname" id="" class="form-control form-control-sm rounded" value="{{ isset($volunteer->volunt_bank_accname) ? $volunteer->volunt_bank_accname : old('volunt_bank_accname') }}">
                    @error('volunt_bank_accname')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
                <div class="w-50">
                    <small>Account Number <sup class="text-danger">*</sup></small>
                    <input type="text" name="volunt_bank_accnumber" id="" class="form-control form-control-sm rounded" value="{{ isset($volunteer->volunt_bank_accnumber) ? $volunteer->volunt_bank_accnumber : old('volunt_bank_accnumber') }}">
                    @error('volunt_bank_accnumber')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-3">
                <label for="">KTP <sup class="text-danger">*</sup></label>
            </div>
            <div class="col-9 d-flex">
                <div class="w-50 me-2">
                    <small>NIK <sup class="text-danger">*</sup></small>
                    <input type="number" name="volunt_nik" id="" class="form-control form-control-sm rounded" value="{{ isset($volunteer->volunt_nik) ? $volunteer->volunt_nik : old('volunt_nik') }}">
                    @error('volunt_nik')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
                <div class="w-50">
                    <small>Image <sup class="text-danger">*</sup></small>
                    <input type="file" name="volunt_idcard" id="" class="form-control form-control-sm rounded">
                    @error('volunt_idcard')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
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
                    <input type="number" name="volunt_npwp_number" id="" class="form-control form-control-sm rounded" value="{{ isset($volunteer->volunt_npwp_number) ? $volunteer->volunt_npwp_number : old('volunt_npwp_number') }}">
                    @error('volunt_npwp_number')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
                <div class="w-50">
                    <small>Image</small>
                    <input type="file" name="volunt_npwp" id="" class="form-control form-control-sm rounded">
                    @error('volunt_npwp')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-3">
                <label for="">BPJS Kesehatan</label>
            </div>
            <div class="col-9">
                <input type="file" name="volunt_bpjs_kesehatan" id="" class="form-control form-control-sm rounded">
                @error('volunt_bpjs_kesehatan')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
        </div>
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-3">
                <label for="">BPJS Ketenagakerjaan</label>
            </div>
            <div class="col-9">
                <input type="file" name="volunt_bpjs_ketenagakerjaan" id="" class="form-control form-control-sm rounded">
                @error('volunt_bpjs_ketenagakerjaan')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>
</div>
