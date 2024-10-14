<div class="card rounded my-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-person me-2"></i>
                Installment List
            </h6>
        </div>
    </div>
    <div class="card-body">
        <div class="list-group">
            @forelse ($invoice->invoiceDetail as $detail)
                <div class="list-group-item">
                    <div class="">
                        <div class="ps-1 fs-6">
                            {{ $detail->invdtl_installment }}
                        </div>
                        <table class="table">
                            <tr>
                                <td>Invoice ID:</td>
                                <td class="text-end">{{ $invoice->inv_id }}</td>
                            </tr>
                            <tr>
                                <td> Due Date:</td>
                                <td class="text-end">{{ $detail->invdtl_duedate }}</td>
                            </tr>
                        </table>
                        <div class="ps-1 mt-1">
                            @if ($detail->invdtl_currency != NULL)
                            {{ strtoupper($detail->invdtl_currency) }} {{ $detail->invdtl_amount }} |  
                            @endif
                            {{ $detail->invoicedtl_amountidr }}
                        </div>
                    </div>
                    <div class="mt-2 text-end">
                        @if ($detail->receipt)
                            <a href="{{ isset($invoice->bundling) ? route('receipt.client-program.show', ['receipt' => $detail->receipt->id, 'b' => true]) : route('receipt.client-program.show', ['receipt' => $detail->receipt->id]) }}">
                                <button class="btn btn-sm btn-outline-warning py-1" style="font-size: 11px">
                                    <i class="bi bi-eye"></i> View
                                </button>
                            </a>
                        @else
                            @if (!isset($invoice->refund) && $clientProg->status != 3)
                            <button class="btn btn-sm btn-outline-primary py-1" style="font-size: 11px" data-bs-toggle="modal"
                            data-bs-target="#addReceipt" onclick="checkReceipt();setIdentifier('Installment', '{{ $detail->invdtl_id }}');setDefault('{{ $detail->invdtl_amount }}', '{{ $detail->invdtl_amountidr }}')">
                                <i class="bi bi-plus"></i> Receipt
                            </button>
                            @endif
                        @endif
                    </div>
                </div>

                @empty
                <small>No installment details</small>

            @endforelse
        </div>
    </div>
</div>
