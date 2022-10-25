{{-- Create  --}}
<!-- Modal -->
<div class="modal fade" id="majorFormUpdate" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between">
                <span>
                    Major
                </span>
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="modal-body w-100">
                <form action="" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id">
                    <div class="row g-2">
                        <div class="col-md-12">
                            <div class="mb-2">
                                <label for="">
                                    Major Name <sup class="text-danger">*</sup>
                                </label>
                                <input type="text" name="name" class="form-control form-control-sm rounded"
                                    required value="">
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