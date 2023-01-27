<div class="card mb-3">
    <div class="card-header">
        <h6 class="m-0 p-0">Refund </h6>
    </div>
    <div class="card-body">
        @if (isset($partnerProgram->invoiceB2b->refund))
            <b style="font-size: 1.5em">Total refund : {{ $partnerProgram->invoiceB2b->refund->total_refunded_str }}</b>
            <hr>
            <p style="font-size: 1em">
                with detail: <br>
                <ul style="font-size: 1em">
                    <li>Total Paid : {{ $partnerProgram->invoiceB2b->refund->total_paid_str }}</li>
                    <li>Refund Amount : {{ $partnerProgram->invoiceB2b->refund->refund_amount_str.' ('.$partnerProgram->invoiceB2b->refund->percentage_refund.'%)' }}</li>
                    <li>Tax : {{ $partnerProgram->invoiceB2b->refund->tax_amount_str.' ('.$partnerProgram->invoiceB2b->refund->tax_percentage.'%)' }}</li>
                </ul>
            </p>
        @else
            <p>
                Reason : {{ $partnerProgram->reason->reason_name }}
            </p>
            <br>
            {!! isset($partnerProgram->invoiceB2b->sch_prog->refund_notes) ? $partnerProgram->invoiceB2b->sch_prog->refund_notes : ''!!}
        @endif
        <br>
    </div>

</div>

