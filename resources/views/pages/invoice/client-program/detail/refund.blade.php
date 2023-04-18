<div class="card mb-3">
    <div class="card-header">
        <h6 class="m-0 p-0">Refund</h6>
    </div>
    <div class="card-body">
        @if (isset($invoice->refund))
            <b style="font-size: 1.5em">Total refund : {{ $invoice->refund->total_refunded_str }}</b>
            <hr>
            <p style="font-size: 1em">
                with detail: <br>
                <ul style="font-size: 1em">
                    <li>Total Paid : {{ $invoice->refund->total_paid_str }}</li>
                    <li>Refund Amount : {{ $invoice->refund->refund_amount_str.' ('.$invoice->refund->percentage_refund.'%)' }}</li>
                    <li>Tax : {{ $invoice->refund->tax_amount_str.' ('.$invoice->refund->tax_percentage.'%)' }}</li>
                </ul>
            </p>
        @else
            {!! isset($clientProg->refund_notes) ? $clientProg->refund_notes : 'If the client wasn\'t going to continue the program. You can click the button below'!!}
        @endif
        <br>
        <div class="mt-3 d-flex justify-content-center">
            
            @if (isset($invoice->refund))
            <button class="btn btn-sm btn-outline-danger rounded mx-1" data-bs-toggle="modal"
                data-bs-target="#cancel_refund">
                <i class="bi bi-x me-1"></i>
                Cancel Refund
            </button>
            @else
            <button class="btn btn-sm btn-primary rounded mx-1" data-bs-toggle="modal" data-bs-target="#refund">
                <i class="bi bi-wallet me-1"></i>
                Refund
            </button>
            @endif
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
                <form action="{{ route('invoice.program.refund', ['client_program' => $clientProg->clientprog_id]) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="">Total Price <sup class="text-danger">*</sup></label>
                            <input type="number" name="total_payment" value="{{ $clientProg->invoice->inv_totalprice_idr }}" readonly
                                class="form-control form-control-sm rounded">
                            @error('total_payment')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Total Paid <sup class="text-danger">*</sup></label>
                            <input type="number" name="total_paid" value="{{ $clientProg->invoice->receipt()->sum('receipt_amount_idr') }}" readonly
                                class="form-control form-control-sm rounded">
                            @error('total_paid')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="">Percentage Refund <sup class="text-danger">*</sup></label>
                            <div class="input-group input-group-sm mb-3">
                                <input type="number" name="percentage_refund" value="0" id="percentage-refund" class="form-control rounded-start" aria-describedby="basic-addon2">
                                <span class="input-group-text" id="basic-addon2">%</span>
                            </div>
                            @error('percentage_refund')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="">Refund Nominal <sup class="text-danger">*</sup></label>
                            <input type="number" name="refund_amount" value="0" id="refund-nominal"
                                class="form-control form-control-sm rounded">
                            @error('refund_nominal')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="">Percentage Tax <sup class="text-danger">*</sup></label>
                            <div class="input-group input-group-sm mb-3">
                                <input type="number" name="tax_percentage" id="tax-percentage" value="0" class="form-control rounded-start" aria-describedby="basic-addon2">
                                <span class="input-group-text" id="basic-addon2">%</span>
                            </div>
                            @error('tax_percentage')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="">Tax Nominal <sup class="text-danger">*</sup></label>
                            <input type="number" name="tax_amount" id="tax-amount" value="0"
                                class="form-control form-control-sm rounded">
                            @error('tax_amount')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="">Total Refund <sup class="text-danger">*</sup></label>
                            <input type="number" name="total_refunded" value="0" readonly
                                class="form-control form-control-sm rounded">
                            @error('total_refunded')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <hr>
                        <div class="text-center d-flex justify-content-between">
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-primary">Save changes</button>
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
                <form action="{{ route('invoice.program.refund', ['client_program' => $clientProg->clientprog_id]) }}" method="POST">
                    @csrf
                    @method('delete')
                    <div class="text-center">
                        <p>
                            Are you sure you want to cancel the refund?
                        </p>
                        <hr>
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-sm btn-primary">Yes, Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("#percentage-refund").on('keyup', function() {
        calculate_refund_nominal($(this).val(), null)  
    })

    $("#refund-nominal").on('keyup', function() {
        calculate_refund_nominal(null, $(this).val())
    })

    function calculate_refund_nominal(percentage, nominal)
    {
        var total_paid = $("input[name=total_paid]").val()

        if (percentage && !nominal)
            $("#refund-nominal").val((total_paid*percentage)/100)
        else if(!percentage && nominal)
            $("#percentage-refund").val(Math.ceil((nominal/total_paid)*100))

    }

    $("#tax-percentage").on('keyup', function() {
        calculate_tax_nominal($(this).val(), null)
        calculate_total_refund()
    })

    $("#tax-amount").on('keyup', function() {
        calculate_tax_nominal(null, $(this).val())
        calculate_total_refund()
    })

    function calculate_tax_nominal(percentage, nominal)
    {
        var refund_nominal = $("#refund-nominal").val()
        if (percentage && !nominal)
            $("#tax-amount").val((refund_nominal*percentage)/100)
        else if (!percentage && nominal)
            $("#tax-percentage").val(Math.ceil((nominal/refund_nominal)*100))
    }

    function calculate_total_refund()
    {
        var refund_nominal = $("#refund-nominal").val()
        var tax_nominal = $("#tax-amount").val()
        $("input[name=total_refunded]").val(refund_nominal-tax_nominal)
    }
</script>
