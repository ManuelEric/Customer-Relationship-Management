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
                href="{{ route('invoice-ref.detail.show',  ['referral' => $invoiceRef->ref_id, 'detail' => $invoiceRef->invb2b_num]) }}">
                <i class="bi bi-eye"></i> View Invoice
            </a>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-hover">
            <tr>
                <td width="20%">Invoice ID :</td>
                <td>{{ $invoiceRef->invb2b_id }}</td>
            </tr>
            @if ($invoiceRef->currency != "idr")
                <tr>
                    <td>Curs Rate :</td>
                    <td>{{ $invoiceRef->rate }}</td>
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
                    @if ($invoiceRef->invb2b_totprice != NULL)
                        {{ $invoiceRef->invoiceTotalprice }}
                        ( {{ $invoiceRef->invoiceTotalpriceIdr }} )
                    @else
                        {{ $invoiceRef->invoiceTotalpriceIdr }}
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>
