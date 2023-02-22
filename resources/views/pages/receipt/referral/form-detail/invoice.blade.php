<div class="card rounded mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-person me-2"></i>
                Invoice Detail
            </h6>
        </div>
        <div class="">
            <a class="btn btn-sm btn-outline-warning py-1"
                href="{{ route('invoice-ref.detail.show',  ['referral' => $receiptRef->invoiceB2b->ref_id, 'detail' => $receiptRef->invoiceB2b->invb2b_num]) }}">
                <i class="bi bi-eye"></i> View Invoice
            </a>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-hover">
            <tr>
                <td width="20%">Invoice ID :</td>
                <td>{{ $receiptRef->invoiceB2b->invb2b_id }}</td>
            </tr>
            @if ($receiptRef->invoiceB2b->currency != "idr")
                <tr>
                    <td>Curs Rate :</td>
                    <td>{{ $receiptRef->invoiceB2b->rate }}</td>
                </tr>
            @endif
            {{-- <tr>
                <td>Participants :</td>
                <td>
                    150
                </td>
            </tr> --}}
             <tr>
                <td>Total Price :</td>
                <td>
                    @if ($receiptRef->invoiceB2b->invb2b_totprice != NULL)
                        {{ $receiptRef->invoiceB2b->invoiceTotalprice }}
                        ( {{ $receiptRef->invoiceB2b->invoiceTotalpriceIdr }} )
                    @else
                        {{ $receiptRef->invoiceB2b->invoiceTotalpriceIdr }}
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>
