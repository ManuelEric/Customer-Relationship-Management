<div class="card mb-3">
    <div class="card-header">
        <h6 class="m-0 p-0">Refund</h6>
    </div>
    <div class="card-body">
        @if (isset($invoiceSch->refund))
            <b style="font-size: 1.5em">Total refund : {{ $invoiceSch->refund->total_refunded_str }}</b>
            <hr>
            <p style="font-size: 1em">
                with detail: <br>
                <ul style="font-size: 1em">
                    <li>Total Paid : {{ $invoiceSch->refund->total_paid_str }}</li>
                    <li>Refund Amount : {{ $invoiceSch->refund->refund_amount_str.' ('.$invoiceSch->refund->percentage_refund.'%)' }}</li>
                    <li>Tax : {{ $invoiceSch->refund->tax_amount_str.' ('.$invoiceSch->refund->tax_percentage.'%)' }}</li>
                </ul>
            </p>
        @else
            <p>
                Reason : {{ $invoiceSch->sch_prog->reason->reason_name }}
            </p>
            {!! isset($invoiceSch->sch_prog->refund_notes) ? $invoiceSch->sch_prog->refund_notes : 'If the client wasn\'t going to continue the program. You can click the button below'!!}
        @endif
        <br>
        <div class="mt-2 d-flex justify-content-center">
            
            @if (isset($invoiceSch->refund))
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
    {{-- @if(isset($invoiceSch)) --}}
        {{-- @if($invoiceSch->invb2b_status == 2)
            <div class="card-footer d-flex justify-content-between">
                <h6 class="m-0 p-0">Total Refund</h6>
                <h6 class="m-0 p-0">{{ $invoiceSch->refund->total_refunded_str }}</h6>
            </div> 
        @endif --}}
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
                            <input type="number" name="total_payment" id="" 
                            value="{{ $invoiceSch->invb2b_totpriceidr }}" readonly
                                class="form-control form-control-sm rounded">
                                @error('total_payment')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Total Paid</label>
                            @php
                                $totalInstallment = 0;
                            @endphp
                            <input type="number" name="total_paid" id="total_paid"
                                value="{{ $invoiceSch->receipt()->sum('receipt_amount_idr') }}"
                                readonly
                                class="form-control form-control-sm rounded">
                                @error('total_paid')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="">Percentage Refund</label>
                            <input type="number" step="any" min="0" max="100"  name="percentage_refund" id="percentage_refund"
                                class="form-control form-control-sm rounded percentage">
                            @error('percentage_refund')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="">Refund Nominal</label>
                            <input type="number" name="refund_amount" id="refund_nominal" oninput="inputRefund();"
                                class="form-control form-control-sm rounded">
                            @error('refund_amount')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="">Percentage Tax</label>
                            <input type="number" step="any" min="0" max="100" name="tax_percentage" id="percentage_tax"
                                class="form-control form-control-sm rounded">
                            @error('tax_percentage')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="">Tax Nominal</label>
                            <input type="number" name="tax_amount" id="tax_nominal"
                                class="form-control form-control-sm rounded">
                            @error('tax_amount')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="">Total Refund</label>
                            <input type="number" name="total_refunded" id="total_refund"
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

@if( $errors->has('total_price') | 
        $errors->has('total_paid') | 
        $errors->has('percentage_payment') | 
        $errors->has('refunded_amount') | 
        $errors->has('refunded_tax_percentage') |
        $errors->has('refunded_tax_amount') |
        $errors->has('total_refunded'))
        
        <script>
            $(document).ready(function(){
                $('#refund').modal('show'); 
            })
        </script>
@endif

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
            $('#percentage_refund').val(null)
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

    $("#refund_nominal").on('keyup', function() {
        var val = $(this).val()
        let max = $('#total_paid').val()
        let percent = $('#percentage_tax').val()
        
        if (isNaN(val))
        val = 0
        
        if (parseInt(val) <= parseInt(max)) {
            // $("#percentage_refund").val(null)
            let percent_tax = $('#percentage_tax').val()
            // let nominal_refund = $('#refund_nominal').val()
            let total_tax = (percent_tax / 100) * val
            let total_refund = total_tax > 0 ? val - total_tax : val
            
            $('#tax_nominal').val(total_tax)
            $('#total_refund').val(total_refund)
            
            
        } else if(val == 0 || val == ''){
            $('#tax_nominal').val(null)
            $('#total_refund').val(null)
        }else {
            $(this).val()
            $('#tax_nominal').val(null)
            $('#total_refund').val(null)
            $('#refund_nominal').val(null)
            notification('error', 'Refund nominal is more than total paid')
        }
        
          
     })


    function inputRefund(){
        $("#percentage_refund").val(null)
    }
</script>