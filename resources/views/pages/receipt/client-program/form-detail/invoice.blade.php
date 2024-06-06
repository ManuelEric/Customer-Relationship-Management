<div class="card rounded mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-person me-2"></i>
                Invoice Detail
            </h6>
        </div>
        <div class="">
            <a href="{{ Request::get('b') ? route('invoice.program.show_bundle', ['bundle' => $receipt->invoiceProgram->bundling_id]) : route('invoice.program.show', ['client_program' => $receipt->invoiceProgram->clientprog->clientprog_id]) }}">
                <button class="btn btn-sm btn-outline-warning py-1">
                    <i class="bi bi-eye"></i> View Invoice
                </button>
            </a>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-hover">
            <tr>
                <td width="20%">Invoice ID :</td>
                <td>{{ $receipt->invoiceProgram->inv_id }}</td>
            </tr>
            @if ($receipt->invoiceProgram->inv_category == "other")
            <tr>
                <td>Curs Rate :</td>
                <td>{{ $receipt->invoiceProgram->rate }}</td>
            </tr>
            @endif
            <tr>
                <td>Price :</td>
                <td>
                    @if ($receipt->invoiceProgram->inv_price != NULL)
                        {{ $receipt->invoiceProgram->invoicePrice }}
                        ( {{ $receipt->invoiceProgram->invoice_price_idr }} )
                    @else
                        {{ $receipt->invoiceProgram->invoice_price_idr }}
                    @endif
                </td>
            </tr>
            @if ($receipt->invoiceProgram->session != 0)
            <tr>
                <td>Session :</td>
                <td>
                    {{ $receipt->invoiceProgram->session }}
                </td>
            </tr>
            @endif
            @if ($receipt->invoiceProgram->duration != 0)
            <tr>
                <td>Duration :</td>
                <td>
                    {{ $receipt->invoiceProgram->duration }}
                </td>
            </tr>
            @endif
            <tr>
                <td>Early Bird :</td>
                <td>
                    @if ($receipt->invoiceProgram->inv_earlybird != NULL)
                        {{ $receipt->invoiceProgram->invoice_earlybird }}
                        ( {{ $receipt->invoiceProgram->invoice_earlybird_idr }} )
                    @else
                        {{ $receipt->invoiceProgram->invoice_earlybird_idr }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Discount Bird :</td>
                <td>
                    @if ($receipt->invoiceProgram->inv_discount != NULL)
                        {{ $receipt->invoiceProgram->invoice_discount }}
                        ( {{ $receipt->invoiceProgram->invoice_discount_idr }} )
                    @else
                        {{ $receipt->invoiceProgram->invoice_discount_idr }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Total Price :</td>
                <td>
                    @if ($receipt->invoiceProgram->inv_totalprice != NULL)
                        {{ $receipt->invoiceProgram->invoice_totalprice }}
                        ( {{ $receipt->invoiceProgram->invoice_totalprice_idr }} )
                    @else
                        {{ $receipt->invoiceProgram->invoice_totalprice_idr }}
                    @endif
                </td>
            </tr>
        </table>

        {{-- IF INSTALLMENT EXIST  --}}
        <div class="mt-3">
            @if ($receipt->invoiceProgram->invoiceDetail->count() > 0)
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
                        @foreach ($receipt->invoiceProgram->invoiceDetail as $detail)
                            <tr style="cursor:pointer"
                                @if (isset($detail->receipt) && $detail->receipt->id == $receipt->id)
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
