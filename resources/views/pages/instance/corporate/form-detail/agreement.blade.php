<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-building me-2"></i>
                Agreement
            </h6>
        </div>
        <div class="">
            <button class="btn btn-sm btn-outline-primary rounded mx-1" data-bs-toggle="modal"
                data-bs-target="#agreementForm">
                <i class="bi bi-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="list-group">
            @forelse ($partnerAgreements as $partnerAgreement)
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <strong>{{ $partnerAgreement->agreement_name }}</strong> <i class="bi bi-trash2 text-danger"
                                onclick="confirmDelete('{{'instance/corporate/' . strtolower($corporate->corp_id) . '/agreement' }}', '{{ $partnerAgreement->id }}')"></i>
                            <br>
                            @switch($partnerAgreement->agreement_type)
                                @case('0')
                                    Referral Mutual Agreement
                                    @break
                                @case('1')
                                    Partnership Agreement
                                    @break
                                @case('2')
                                    Speaker Agreement
                                    @break
                                @case('3')
                                    University Agent
                                    @break
                                    
                            @endswitch
                            @if(isset($partnerAgreement->partnerPic))
                                <br>
                                {{ $partnerAgreement->partnerPic->pic_name }} (Partner PIC)
                            @endif
                            @if(isset($partnerAgreement->user))
                                <br>
                                {{ $partnerAgreement->user->full_name }} (Internal PIC)
                            @endif
                            <br>
                            {{ date('d/m/Y', strtotime($partnerAgreement->start_date)) }} - {{ date('d/m/Y', strtotime($partnerAgreement->end_date)) }}
                        </div>
                        <div class="">
                            <a href="{{Storage::url('/')}}attachment/partner_agreement/{{ strtolower($corporate->corp_id) }}/{{ $partnerAgreement->attachment }}" download="{{ $partnerAgreement->attachment }}" class="btn btn-sm btn-outline-success" target="_blank">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="d-flex align-items-center">
                    No Agreement yet
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="modal modal-md fade" id="agreementForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0 p-0">
                    <i class="bi bi-plus me-2"></i>
                    Agreement
                </h4>
            </div>
            <div class="modal-body">
                <form action="{{ route('corporate.agreement.store', ['corporate' => strtolower($corporate->corp_id)]) }}"
                    id="detailForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label for="">Agreement Name <sup class="text-danger">*</sup></label>
                            <input type="text" name="agreement_name" id="pic_name" value="{{ old('agreement_name') }}"
                                class="form-control form-control-sm rounded">
                            @error('agreement_name')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Agreement Type <sup class="text-danger">*</sup></label>
                            <select name="agreement_type" class="agreement-select w-100">
                                <option data-placeholder="true"></option>
                                <option value="0" {{ old('agreement_type') == '0' ? 'selected' : '' }}>Referral Mutual agreement</option>
                                <option value="1" {{ old('agreement_type') == '1' ? 'selected' : '' }}>Partnership Agreement</option>
                                <option value="2" {{ old('agreement_type') == '2' ? 'selected' : '' }}>Speaker Agreement</option>
                                <option value="3" {{ old('agreement_type') == '3' ? 'selected' : '' }}>University Agent</option>
                            </select>
                            @error('agreement_type')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">Agreement Start Date <sup class="text-danger">*</sup></label>
                            <input type="date" name="start_date" id="" value="{{ old('start_date') }}"
                            class="form-control form-control-sm rounded">
                            @error('start_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">Agreement End Date <sup class="text-danger">*</sup></label>
                            <input type="date" name="end_date" id="" value="{{ old('end_date') }}"
                            class="form-control form-control-sm rounded">
                            @error('end_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Partner PIC <sup class="text-danger">*</sup></label>
                            <select name="corp_pic" class="agreement-select w-100">
                                <option data-placeholder="true"></option>
                                @foreach ($pics->where('is_pic', 1) as $pic)
                                    <option value="{{ $pic->id }}" {{ $pic->id == old('corp_pic') ? 'selected' : '' }}>{{ $pic->pic_name }}</option>
                                @endforeach
                            </select>
                            @error('corp_pic')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">ALL In PIC <sup class="text-danger">*</sup></label>
                            <select name="empl_id" class="agreement-select w-100">
                                <option data-placeholder="true"></option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $employee->id == old('empl_id') ? 'selected' : '' }}>{{ $employee->first_name }}  {{ $employee->last_name }}</option>
                                @endforeach
                            </select>
                            @error('empl_id')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Agreement File <sup class="text-danger">*</sup></label>
                            <input type="file" name="attachment" value="{{ old('attachment') }}" class="form-control form-control-sm rounded">
                            @error('attachment')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                    data-bs-dismiss="modal">
                                    <i class="bi bi-x me-1"></i>
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-sm btn-primary rounded-3">
                                    <i class="bi bi-save2"></i>
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.agreement-select').select2({
            dropdownParent: $('#agreementForm .modal-content'),
            placeholder: "Select value",
            allowClear: true
        });
    });
</script>

@if(
    $errors->has('agreement_name') | 
    $errors->has('agreement_type') | 
    $errors->has('start_date') | 
    $errors->has('end_date') | 
    $errors->has('corp_pic') | 
    $errors->has('empl_id') | 
    $errors->has('attachment')  
    )
            
    <script>
        $(document).ready(function(){
            $('#agreementForm').modal('show'); 
        })

    </script>

@endif