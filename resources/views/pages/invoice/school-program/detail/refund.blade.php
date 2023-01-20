<div class="card mb-3">
    <div class="card-header">
        <h6 class="m-0 p-0">Refund</h6>
    </div>
    <div class="card-body">
        @if(isset($invoiceSch))
            @if($invoiceSch->sch_prog->status == 3)
                {!! $invoiceSch->sch_prog->refund_notes !!}
            @endif
        @endif
        <br>
         @if(isset($invoiceSch))
            <div class="mt-3 d-flex justify-content-center">
                @if($invoiceSch->sch_prog->status == 3 && $invoiceSch->invb2b_status == 1)
                    <button class="btn btn-sm btn-primary rounded mx-1" data-bs-toggle="modal" data-bs-target="#refund">
                        <i class="bi bi-x me-1"></i>
                        Refund
                    </button>
                @endif
            @if($invoiceSch->sch_prog->status == 3 && $invoiceSch->invb2b_status == 2)
                <button class="btn btn-sm btn-outline-danger rounded mx-1" data-bs-toggle="modal"
                    data-bs-target="#cancel_refund">
                    <i class="bi bi-x me-1"></i>
                    Cancel Refund
                </button>
            @endif
            </div>
        @endif
    </div>
    {{-- @if(isset($invoiceSch)) --}}
        @if($invoiceSch->invb2b_status == 2)
            <div class="card-footer d-flex justify-content-between">
                <h6 class="m-0 p-0">Total Refund</h6>
                <h6 class="m-0 p-0">{{ number_format($invoiceSch->refund->total_refunded) }}</h6>
            </div>
        @endif
    {{-- @endif --}}
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
                <form action="{{ isset($invoiceSch) ? route('invoice-sch.refund', ['invoice' => $invoiceSch->invb2b_num]) : '' }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="">Total Price </label>
                            <input type="number" name="" id="" 
                            value="{{ $invoiceSch->invb2b_totpriceidr }}" readonly
                                class="form-control form-control-sm rounded">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Total Paid</label>
                            @php
                                $totalInstallment = 0;
                            @endphp
                            <input type="number" name="total_payment" id="total_paid"
                                @if(count($invoiceSch->inv_detail)>0)
                                    @foreach ($invoiceSch->inv_detail as $installment)
                                        @php
                                            $clearNumberFormat = filter_var($installment->receipt->receipt_amount_idr, FILTER_SANITIZE_NUMBER_INT);
                                            $clearDecimal = substr($clearNumberFormat, 0, -2);
                                            $totalInstallment += $clearDecimal;
                                        @endphp
                                    @endforeach
                                    value="{{ $totalInstallment }}"
                                @else
                                        @php
                                            $clearNumberFormat = filter_var($invoiceSch->receipt->receipt_amount_idr, FILTER_SANITIZE_NUMBER_INT);
                                            $receiptAmountIdr = substr($clearNumberFormat, 0, -2);
                                        @endphp
                                    value="{{ $receiptAmountIdr }}"
                                @endif
                                readonly
                                class="form-control form-control-sm rounded">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="">Percentage Refund</label>
                            <input type="number" name="percentage_payment" id="percentage_refund"
                                class="form-control form-control-sm rounded percentage">
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="">Refund Nominal</label>
                            <input type="number" name="refunded_amount" id="refund_nominal"
                                class="form-control form-control-sm rounded">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="">Percentage Tax</label>
                            <input type="number" name="refunded_tax_percentage" id="percentage_tax"
                                class="form-control form-control-sm rounded">
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="">Tax Nominal</label>
                            <input type="number" name="refunded_tax_amount" id="tax_nominal"
                                class="form-control form-control-sm rounded">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="">Total Refund</label>
                            <input type="number" name="total_refunded" id="total_refund"
                                class="form-control form-control-sm rounded">
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
                <form action="{{ isset($invoiceSch->refund) ? route('invoice-sch.refund.destroy', ['invoice' => $invoiceSch->invb2b_num, 'refund' => $invoiceSch->refund->id]) : '' }}" method="POST">
                    @method('delete')
                    @csrf
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

<script>
    $("#percentage_refund").on('keyup', function() {
        var val = $(this).val()

        if (isNaN(val))
            val = 0

        if (val <= 100) {
            let percent = $('#percentage_refund').val()
            let tot_paid = $('#total_paid').val()
            let total = (val / 100) * tot_paid
            let percent_tax = $('#percentage_tax').val()
            let nominal_refund = $('#refund_nominal').val()
            let total_tax = (percent_tax / 100) * nominal_refund
            let total_refund = nominal_refund - total_tax

            $('#tax_nominal').val(total_tax)
            $('#total_refund').val(total_refund)

            $('#refund_nominal').val(total)
        } else if(val == 0){
            $('#tax_nominal').val(null)
            $('#total_refund').val(null)
            $('#refund_nominal').val(null)
        } else {
            $(this).val()
            $('#refund_nominal').val(null)
            notification('error', 'Percentage is more than 100')
        }


     })

    $("#percentage_tax").on('keyup', function() {
        var val = $(this).val()
        let percent = $('#percentage_refund').val()


        if (isNaN(val))
            val = 0

        if (val <= 100) {
            let percent_tax = $('#percentage_tax').val()
            let nominal_refund = $('#refund_nominal').val()
            let total_tax = (val / 100) * nominal_refund
            let total_refund = nominal_refund - total_tax

            $('#tax_nominal').val(total_tax)
            $('#total_refund').val(total_refund)
        } else if(percent == 0 || percent == ''){
            $('#tax_nominal').val(null)
            $('#total_refund').val(null)
        }else {
           $(this).val()
            $('#tax_nominal').val(null)
            $('#total_refund').val(null)
            notification('error', 'Percentage is more than 100')
        }
     })
</script>