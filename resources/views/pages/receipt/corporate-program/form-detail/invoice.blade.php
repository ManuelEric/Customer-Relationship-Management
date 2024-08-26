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
                href="{{ route('invoice-corp.detail.show',  ['corp_prog' => $invoicePartner->partnerprog_id, 'detail' => $invoicePartner->invb2b_num]) }}">
                <i class="bi bi-eye"></i> View Invoice
            </a>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-hover">
            <tr>
                <td width="20%">Invoice ID :</td>
                <td>{{ $invoicePartner->invb2b_id }}</td>
            </tr>
            @if ($invoicePartner->currency != "idr")
                <tr>
                    <td>Curs Rate :</td>
                    <td>{{ $invoicePartner->rate }}</td>
                </tr>
            @endif
            <tr>
                <td>Price :</td>
                <td>
                    @if ($invoicePartner->invb2b_price != NULL)
                        {{ $invoicePartner->invoicePrice }}
                        ( {{ $invoicePartner->invoicePriceIdr }} )
                    @else
                        {{ $invoicePartner->invoicePriceIdr }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Participants :</td>
                <td>
                    {{ $invoicePartner->invb2b_participants }}
                </td>
            </tr>
            <tr>
                <td>Discount :</td>
                <td>
                    @if ($invoicePartner->invb2b_disc != NULL)
                        {{ $invoicePartner->invoiceDiscount }}
                        ( {{ $invoicePartner->invoiceDiscountIdr }} )
                    @else
                        {{ $invoicePartner->invoiceDiscountIdr }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Total Price :</td>
                <td>
                    @if ($invoicePartner->invb2b_totprice != NULL)
                        {{ $invoicePartner->invoiceTotalprice }}
                        ( {{ $invoicePartner->invoiceTotalpriceIdr }} )
                    @else
                        {{ $invoicePartner->invoiceTotalpriceIdr }}
                    @endif
                </td>
            </tr>
        </table>

        {{-- IF INSTALLMENT EXIST  --}}
        <div class="mt-3">
            @if($invoicePartner->inv_detail->count() > 0)
                Installment List
                <table class="table table-bordered table-hover" id="installment-list">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Due Date</th>
                            <th>Percentage</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($invoicePartner->inv_detail as $detail)
                            <tr style="cursor:pointer"
                                @if (isset($detail->receipt) && $detail->receipt->id == $receiptPartner->id)
                                    class="bg-success text-light detail" data-recid="{{ $detail->receipt->id }}"
                                @elseif (isset($detail->receipt))
                                    class="detail" data-recid="{{ $detail->receipt->id }}"
                                @endif
                                >
                                <td>{{ $loop->iteration }} </td>
                                <td>{{ $detail->invdtl_installment }}</td>
                                <td>{{ $detail->invdtl_duedate }}</td>
                                <td>{{ $detail->invdtl_percentage }}%</td>
                                <td>
                                    @if ($detail->invdtl_amount != NULL)
                                        {{ $detail->invoicedtl_amount }}
                                        ( {{ $detail->invoicedtl_amountidr }} )
                                    @else
                                        {{ $detail->invoicedtl_amountidr }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
