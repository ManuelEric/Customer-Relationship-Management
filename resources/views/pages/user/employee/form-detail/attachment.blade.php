<div class="card rounded">
    <div class="card-header">
        <h6 class="m-0 p-0">Attachment</h6>
    </div>
    <div class="card-body">
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="">Curriculum Vitae</label>
            </div>
            <div class="col-md-9">
                @if (!isset($user->cv))
                    <input type="file" name="curriculum_vitae" id=""
                        class="form-control form-control-sm rounded"
                        value="{{ isset($user->cv) ? $user->cv : old('curriculum_vitae') }}">
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
                            <input type="file" name="curriculum_vitae" id=""
                                class="form-control form-control-sm rounded" value="{{ old('curriculum_vitae') }}">
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
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="">Bank Account</label>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <small>Bank Name <sup class="text-danger">*</sup></small>
                        <select name="bank_name" id="bank_name" class="select w-100">
                            <option value=""></option>
                        </select>
                        @error('bank_name')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <small>Account Name <sup class="text-danger">*</sup></small>
                        <input type="text" name="account_name" class="form-control form-control-sm rounded"
                            value="{{ isset($user->account_name) ? $user->account_name : old('account_name') }}">
                        @error('account_name')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <small>Account Number <sup class="text-danger">*</sup></small>
                        <input type="text" name="account_no" class="form-control form-control-sm rounded"
                            value="{{ isset($user->account_no) ? $user->account_no : old('account_no') }}">
                        @error('account_no')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
            </div>
        </div>
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="">KTP</label>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <small>NIK <sup class="text-danger">*</sup></small>
                        <input type="text" name="nik" class="form-control form-control-sm rounded"
                            value="{{ isset($user->nik) ? $user->nik : old('nik') }}">
                        @error('nik')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        @if (!isset($user->idcard))
                            <small>Image <sup class="text-danger">*</sup></small>
                            <input type="file" name="idcard" class="form-control form-control-sm rounded"
                                value="{{ old('idcard') }}">
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
                                    <input type="file" name="idcard" class="form-control form-control-sm rounded"
                                        value="{{ old('idcard') }}">
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
        </div>
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="">NPWP</label>
            </div>
            <div class="col-md-9">
                <div class="row ">
                    <div class="col-md-6 mb-2">
                        <small>NPWP Number</small>
                        <input type="text" name="npwp" placeholder="12.345.678.9-123.456" id="" class="form-control form-control-sm rounded"
                            value="{{ isset($user->npwp) ? $user->npwp : old('npwp') }}">
                        @error('npwp')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        @if (!isset($user->tax))
                            <small>Image </small>
                            <input type="file" name="tax" id=""
                                class="form-control form-control-sm rounded" value="{{ old('tax') }}">
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
                                    <input type="file" name="tax" id=""
                                        class="form-control form-control-sm rounded" value="{{ old('tax') }}">
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
        </div>
        <div class="row align-items-center border py-2 mx-1 mb-1">
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="">BPJS Kesehatan </label>
            </div>
            <div class="col-md-9">
                @if (!isset($user->health_insurance))
                    <input type="file" name="health_insurance" class="form-control form-control-sm rounded"
                        value="{{ old('health_insurance') }}">
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
                <label for="">BPJS Ketenagakerjaan </label>
            </div>
            <div class="col-md-9">
                @if (!isset($user->empl_insurance))
                    <input type="file" name="empl_insurance" class="form-control form-control-sm rounded"
                        value="{{ old('empl_insurance') }}">
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
<script>

    $(document).ready(function() {
        axios.get('https://bios.kemenkeu.go.id/api/ws/ref/bank')
                .then(function (response){
    
                    let obj = response.data;
                    
                    var html = '<option data-placeholder="true"></option>'
    
                    for (var key in obj.data){
                        var selected = '';
        
                        if('{{ !empty(old("bank_name")) }}' && '{{ old("bank_name") }}' === obj.data[key].uraian)
                            selected = "selected";

                        @if (isset($user) && isset($user->bank_name))
                            if('{{ $user->bank_name }}' === obj.data[key].uraian){
                                selected = "selected";
                            }    
                        @endif
                            
                        html += "<option value='" + obj.data[key].uraian + "' " + selected +">" + obj.data[key].uraian + "</option>"
                            
                    }
    
                    $("#bank_name").html(html)
                       
                    swal.close();
                }).catch(function (error) {
                    console.log(error)
                    swal.close();
                })
    });
</script>