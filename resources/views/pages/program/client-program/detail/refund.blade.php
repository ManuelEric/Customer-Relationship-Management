<div class="card mb-3">
    <div class="card-header">
        <h6 class="m-0 p-0">Refund</h6>
    </div>
    <div class="card-body">
        <p style="font-size: 1em">
            Detail refund: <br>
            <ul style="font-size: 1em">
                <li>Total Paid : {{ $clientProgram->invoice->refund->total_paid_str }}</li>
                <li>Refund Amount : {{ $clientProgram->invoice->refund->refund_amount_str.' ('.$clientProgram->invoice->refund->percentage_refund.'%)' }}</li>
                <li>Tax : {{ $clientProgram->invoice->refund->tax_amount_str.' ('.$clientProgram->invoice->refund->tax_percentage.'%)' }}</li>
            </ul>
        </p>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <h6 class="m-0 p-0">Total Refund</h6>
        <h6 class="m-0 p-0">{{ $clientProgram->invoice->refund->total_refunded_str }}</h6>
    </div>
</div>
