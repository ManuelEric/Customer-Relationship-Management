<div class="card mb-3">
    <div class="card-header">
        <h6 class="m-0 p-0">Refund </h6>
    </div>
    <div class="card-body">
        @if (isset($schoolProgram->invoiceB2b->refund))
            <b style="font-size: 1.5em">Total refund : {{ $schoolProgram->invoiceB2b->refund->total_refunded_str }}</b>
            <hr>
            <p style="font-size: 1em">
                with detail: <br>
                <ul style="font-size: 1em">
                    <li>Total Paid : {{ $schoolProgram->invoiceB2b->refund->total_paid_str }}</li>
                    <li>Refund Amount : {{ $schoolProgram->invoiceB2b->refund->refund_amount_str.' ('.$schoolProgram->invoiceB2b->refund->percentage_refund.'%)' }}</li>
                    <li>Tax : {{ $schoolProgram->invoiceB2b->refund->tax_amount_str.' ('.$schoolProgram->invoiceB2b->refund->tax_percentage.'%)' }}</li>
                </ul>
            </p>
        @else
            <p>
                Reason : {{ $schoolProgram->reason->reason_name }}
            </p>
            <br>
            {!! isset($schoolProgram->invoiceB2b->sch_prog->refund_notes) ? $schoolProgram->invoiceB2b->sch_prog->refund_notes : ''!!}
        @endif
        <br>
    </div>

</div>

