@extends('layout.receipt')
@section('receipt_id', $receipt->receipt_id)

@section('body')

    <table border="0" width="100%">
        <tr>
            <td width="60%">
                <table width="100%" style="padding:0px; margin-left:-10px;">
                    <tr>
                        <td width="15%" valign="top">From : </td>
                        <td width="85%"><b>{{ $companyDetail['name'] }}</b><br>
                            {{ $companyDetail['address'] }}<br>
                            {{ $companyDetail['address_dtl'] }} <br>
                            {{ $companyDetail['city'] }}
                            <br><br>
                        </td>
                    </tr>
                </table>
            </td>

            <td valign="top" width="45%">
                <table border=0>
                    <tr>
                        <td valign="top">
                            Received from :
                        </td>
                        <td>
                            {{ $receipt->invoiceProgram->bundling->first_detail->client_program->client->full_name }}
                            <br>
                            {{-- @if ($receipt->invoiceProgram->clientProg->client->state != null)
                                        {{ $receipt->invoiceProgram->clientProg->client->state }}
                                    @endif --}}
                            @if ($receipt->invoiceProgram->bundling->first_detail->client_program->client->address != null)
                                {{ html_entity_decode(strip_tags($receipt->invoiceProgram->bundling->first_detail->client_program->client->address)) }}
                            @endif
                            @if ($receipt->invoiceProgram->bundling->first_detail->client_program->client->city != null)
                                {{ $receipt->invoiceProgram->bundling->first_detail->client_program->client->city }}
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>
    <table>
        <tr>
            <td>
                Receipt No. {{ $receipt->receipt_id }}
            </td>
        </tr>
    </table>

    <table width="100%" class="table-detail" style="padding:8px 5px;">
        <tr align="center">
            <th width="35%">Payment Method</th>
            <th width="35%">Cheque No.</th>
            <th width="30%">Amount</th>
        </tr>
        <tr align="center">
            <td>{{ $receipt->receipt_method }}</td>
            <td>{{ $receipt->receipt_cheque }}</td>
            <td>{{ $receipt->receipt_amount_idr }}</td>
        </tr>
    </table>
    <br>

    <table width="100%" class="table-detail" style="padding:8px 5px;">
        <tr align="center">
            <th width="5%">No</th>
            <th width="55%">Description</th>
            <th width="20%">Price</th>
            <th width="20%">Total</th>
        </tr>
        <tr>
            <td valign="top" align="center">1</td>
            <td valign="top" style="padding-bottom:10px;">
                <div style="min-height:80px;">
                    <p>
                        <strong> {{ $receipt->invoiceProgram->bundling->first_detail->client_program->program->program_name }} </strong>
                    </p>
                    <p>
                        Bundle package with:
                    </p>
                    @for($i = 1; $i < $receipt->invoiceProgram->bundling->details->count(); $i++)
                        <p>
                            <strong> {{ $receipt->invoiceProgram->bundling->details[$i]->client_program->program->program_name }} </strong>
                        </p>
                    @endfor
                    @if ($receipt->invoiceProgram->inv_paymentmethod == 'Installment')
                        <p>
                            {{ $receipt->invoiceInstallment->invdtl_installment }} (
                            {{ $receipt->invoiceInstallment->invdtl_percentage }}% )
                        </p>
                    @endif
                </div>
                <p>
                    {!! $receipt->invoiceProgram->inv_notes !!}
                </p>
            </td>
            <td valign="top" align="center">
                <div style="min-height:80px;">
                    <p>
                        <strong>
                            @if ($receipt->invoiceProgram->inv_paymentmethod == 'Installment')
                                {{ $receipt->invoiceInstallment->invoicedtl_amountidr }}
                            @else
                                {{ $receipt->invoiceProgram->invoice_price_idr }}
                            @endif
                        </strong>
                    </p>
                </div>
            </td>
            <td valign="top" align="center">
                <div style="min-height:80px;">
                    <p>
                        <strong>
                            @if ($receipt->invoiceProgram->inv_paymentmethod == 'Installment')
                                {{ $receipt->invoiceInstallment->invoicedtl_amountidr }}
                            @else
                                {{ $receipt->invoiceProgram->invoice_price_idr }}
                            @endif
                        </strong>
                    </p>
                </div>
            </td>
        </tr>

        @if ($receipt->invoiceProgram->inv_earlybird_idr > 0)
            <tr>
                <td colspan="3" align="right"><b>Early Bird</b></td>
                <td valign="middle" align="center">
                    <b>
                        {{ $receipt->invoiceProgram->invoice_earlybird_idr }}
                    </b>
                </td>
            </tr>
        @endif
        @if ($receipt->invoiceProgram->inv_discount_idr > 0)
            <tr>
                <td colspan="3" align="right"><b>Discount</b></td>
                <td valign="middle" align="center">
                    <b>
                        {{ $receipt->invoiceProgram->invoice_discount_idr }}
                    </b>
                </td>
            </tr>
        @endif
        <tr>
            <td colspan="3" align="right"><b>Total</b></td>
            <td valign="middle" align="center">
                <b>
                    {{ $receipt->receipt_amount_idr }}
                </b>
            </td>
        </tr>
    </table>

    <p style="text-align: right; padding-right:5px">
        Updated On: {{ date('d/m/Y H:i:s', strtotime($receipt->updated_at)) }}
    </p>

    <table>
        <tr>
            <td>
                <b style="letter-spacing:0.7px;"><i>Total Amount : {{ $receipt->receipt_words_idr }}</i></b>
            </td>
        </tr>
    </table>

    <table border=0 width="100%">
        <tr>
            <td width="60%" valign="top">
            </td>
            <td width="40%" align="center" valign="top">
                {{-- PT. Jawara Edukasih Indonesia --}}
                Jakarta,
                {{ isset($receipt->receipt_date) ? date('d F Y', strtotime($receipt->receipt_date)) : date('d F Y', strtotime($receipt->created_at)) }}
                <br><br><br><br><br><br><br><br><br>
                @if (isset($director))
                    {{ $director }}
                @else
                    * Director name *
                @endif
                <br>
                Director
            </td>
        </tr>
    </table>
    <br><br>

    <table width="100%">
        <tr>
            <td align="center">
                Thank You for Your Business
            </td>
        </tr>
    </table>
    </div>
@endsection
