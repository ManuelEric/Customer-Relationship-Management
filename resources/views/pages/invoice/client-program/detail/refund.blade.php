<div class="card mb-3">
    <div class="card-header">
        <h6 class="m-0 p-0">Refund</h6>
    </div>
    <div class="card-body">
        Lorem ipsum dolor sit amet, consectetur adipisicing elit.
        <br>
        <div class="mt-3 d-flex justify-content-center">
            <button class="btn btn-sm btn-primary rounded mx-1" data-bs-toggle="modal" data-bs-target="#refund">
                <i class="bi bi-x me-1"></i>
                Refund
            </button>
            <button class="btn btn-sm btn-outline-danger rounded mx-1" data-bs-toggle="modal"
                data-bs-target="#cancel_refund">
                <i class="bi bi-x me-1"></i>
                Cancel Refund
            </button>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="refund">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Refund</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="">
                    @method('post')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="">Total Price</label>
                            <input type="number" name="" id=""
                                class="form-control form-control-sm rounded">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Total Paid</label>
                            <input type="number" name="" id=""
                                class="form-control form-control-sm rounded">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="">Percentage Refund</label>
                            <input type="number" name="" id=""
                                class="form-control form-control-sm rounded">
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="">Refund Nominal</label>
                            <input type="number" name="" id=""
                                class="form-control form-control-sm rounded">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="">Percentage Tax</label>
                            <input type="number" name="" id=""
                                class="form-control form-control-sm rounded">
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="">Tax Nominal</label>
                            <input type="number" name="" id=""
                                class="form-control form-control-sm rounded">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="">Total Refund</label>
                            <input type="number" name="" id=""
                                class="form-control form-control-sm rounded">
                        </div>
                        <hr>
                        <div class="text-center d-flex justify-content-between">
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-sm btn-primary">Save changes</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cancel_refund">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Refund</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="">
                    @method('post')
                    <div class="text-center">
                        <p>
                            Are you sure you want to cancel the refund?
                        </p>
                        <hr>
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-sm btn-primary">Yes, Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
