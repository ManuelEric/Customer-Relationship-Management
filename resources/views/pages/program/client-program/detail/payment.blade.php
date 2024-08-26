<div class="card rounded mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                Payments Information
            </h6>
        </div>
    </div>
    <div class="card-body">
        @if (isset($clientProgram->invoice))
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-2">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <strong>
                                Invoice ID : {{ $clientProgram->invoice->inv_id }} 
                            </strong>
                            <a href="{{ route('invoice.program.export', ['client_program' => $clientProgram->clientprog_id]) }}" title="See receipt" class="fs-6 text-end">
                                <i class="bi bi-printer cursor-pointer"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            @foreach ($clientProgram->invoice->invoiceDetail as $invdetail)
                            <div class="d-flex justify-content-between {{ $loop->iteration > 1 ? "mt-3 border-top pt-3" : null }}">
                                <div class="">
                                    <strong>
                                        Invoice : {{ $clientProgram->invoice->inv_id }} - {{ $invdetail->invdtl_id }} 
                                        @if ($invdetail->receipt)
                                        <i class="bi bi-arrow-right mx-3"></i>
                                        Already Paid (ref: {{ $invdetail->receipt->receipt_id }})
                                        @endif
                                    </strong> <br>
                                    <span>{{ $invdetail->invdtl_installment }}</span> <br>
                                    @if (isset($invdetail->invdtl_amount))
                                        {{ $invdetail->invoicedtl_amount }}
                                        ( {{ $invdetail->invoicedtl_amountidr }} )
                                    @else
                                        {{ $invdetail->invoicedtl_amountidr }}
                                    @endif
                                </div>
                                @if (isset($invdetail->receipt))
                                <div class="text-end fs-6">
                                    <a href="{{ route('receipt.client-program.show', ['receipt' => $invdetail->receipt->id]) }}" title="See receipt">
                                        <i class="bi bi-wallet cursor-pointer"></i>
                                    </a>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <strong>
                            Receipt
                        </strong>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="">
                                <strong>
                                    Receipt ID
                                </strong> <br>
                                Rp. 131.212.213
                            </div>
                            <div class="text-end fs-6">
                                <i class="bi bi-eye cursor-pointer"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
        @else
            No payment yet
        @endif
    </div>
</div>
