<div class="card rounded mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-building me-2"></i>
                Partner
            </h6>
        </div>
        <div class="">
            <button class="btn btn-sm btn-outline-primary rounded mx-1" data-bs-toggle="modal" data-bs-target="#partner">
                <i class="bi bi-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        No data
    </div>
</div>

<div class="modal fade" id="partner" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <span>
                    Partner
                </span>
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="modal-body w-100 text-start">
                <form action="" method="POST" id="formPosition">
                    @csrf
                    <div class="put"></div>
                    <div class="row g-2">
                        <div class="col-md-12">
                            <div class="mb-2">
                                <label for="">
                                    Partner Name <sup class="text-danger">*</sup>
                                </label>
                                <select name="" class="modal-select w-100">
                                    <option data-placeholder="true"></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="#" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-save2 me-1"></i>
                            Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.modal-select').select2({
            dropdownParent: $('#partner .modal-content'),
            placeholder: "Select value",
            allowClear: true
        });
    });
</script>
