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
            @for ($i = 0; $i < 3; $i++)
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <strong>Agreement Name</strong> <i class="bi bi-trash2 text-danger"
                                onclick="confirmDelete('1')"></i>
                            <br>
                            Agreement Type | Start Date - End Date
                        </div>
                        <div class="">
                            <a href="#" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endfor
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
                <form action="{{ route('corporate.detail.store', ['corporate' => $corporate->corp_id]) }}"
                    id="detailForm" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label for="">Agreement Name</label>
                            <input type="text" name="pic_name" id="pic_name"
                                class="form-control form-control-sm rounded">
                            @error('name')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Agreement Type</label>
                            <select name="" class="agreement-select w-100"></select>
                            @error('name')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">Agreement Start Date</label>
                            <input type="date" name="" id=""
                                class="form-control form-control-sm rounded">
                            @error('name')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">Agreement End Date</label>
                            <input type="date" name="" id=""
                                class="form-control form-control-sm rounded">
                            @error('name')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Agreement File</label>
                            <input type="file" name="" class="form-control form-control-sm rounded">
                            @error('name')
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
