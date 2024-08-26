@extends('layout.receipt')
@section('receipt_id', $receiptSch->receipt_id)

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
                            {{ $invoiceSch->sch_prog->school->sch_name }}
                            <br>
                            @if (isset($invoiceSch->sch_prog->school->sch_location))
                                {{ html_entity_decode(strip_tags($invoiceSch->sch_prog->school->sch_location)) }}
                            @else
                                {{ $invoiceSch->sch_prog->school->sch_city }}
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
                Receipt No. {{ $receiptSch->receipt_id }}
            </td>
        </tr>
    </table>

    <table width="100%" class="table-detail" style="padding:8px 5px;">
        <tr align="center">
            <th width="35%">Payment Method</th>
            @if ($receiptSch->receipt_method == 'Cheque')
                <th width="35%">Cheque No.</th>
            @endif
            <th width="30%">Amount</th>
        </tr>
        <tr align="center">
            <td>{{ $receiptSch->receipt_method }}</td>
            @if ($receiptSch->receipt_method == 'Cheque')
                <td>{{ $receiptSch->receipt_cheque }}</td>
            @endif
            <td>{{ $currency == 'other' ? $receiptSch->receipt_amount : $receiptSch->receipt_amount_idr }}</td>
        </tr>
    </table>
    <br>

    <table width="100%" class="table-detail" style="padding:8px 5px;">
        <tr align="center">
            <th width="5%">No</th>
            <th width="{{ $invoiceSch->is_full_amount == 0 ? '35%' : '70%' }}">Description</th>
            <th width="25%">Price</th>
            @if ($invoiceSch->is_full_amount == 0 && $invoiceSch->invb2b_pm == 'Full Payment')
                <th width="10%">Participants</th>
            @endif
            <th width="25%">Total</th>
        </tr>
        <tr>
            {{-- No --}}
            <td valign="top" align="center">1</td>

            {{-- Description --}}
            <td valign="top" style="padding-bottom:10px;">
                <div style="min-height:80px;">
                    <p>
                        <strong> {{ $invoiceSch->sch_prog->program->program_name }} </strong>
                    </p>
                    @if ($invoiceSch->invb2b_pm == 'Installment')
                        <p>
                            {{ $receiptSch->invoiceInstallment->invdtl_installment }} (
                            {{ $receiptSch->invoiceInstallment->invdtl_percentage }}% )
                        </p>
                        <br>
                    @endif
                    @if ($invoiceSch->is_full_amount == 1)
                        <p>
                            {{ $invoiceSch->invb2b_participants > 0 && $invoiceSch->invb2b_participants != null ? 'Participants: ' . $invoiceSch->invb2b_participants : '' }}
                        </p>
                        <br>
                    @endif
                    <p>
                        {!! $invoiceSch->invb2b_notes !!}
                    </p>
                </div>

                @if ($invoiceSch->invb2b_discidr != 0 && $invoiceSch->invb2b_discidr != null)
                    <div style="margin-top:5px;">
                        <p>
                            <strong> Discount</strong>
                        </p>
                    </div>
                @endif

                @if ($receiptSch->pph23 > 0)
                    <p>
                        PPH 23 {{ $receiptSch->pph23 }}%
                    </p>
                @endif
            </td>

            {{-- Price --}}
            <td valign="top" align="center">
                <div style="min-height:80px;">
                    <p>
                        <strong>
                            @if ($invoiceSch->invb2b_pm == 'Installment')
                                {{ $currency == 'other' ? $receiptSch->invoiceInstallment->invoicedtl_amount : $receiptSch->invoiceInstallment->invoicedtl_amountidr }}
                            @else
                                {{ $currency == 'other' ? $invoiceSch->invoicePrice : $invoiceSch->invoicePriceIdr }}
                            @endif
                        </strong>
                    </p>
                </div>
            </td>
            @if ($invoiceSch->is_full_amount == 0 && $invoiceSch->invb2b_pm == 'Full Payment')
                {{-- Participants --}}
                <td valign="top" align="center">
                    <p>
                        <strong>
                            {{ $invoiceSch->invb2b_participants }}
                        </strong>
                    </p>
                </td>
            @endif

            {{-- Total --}}
            <td valign="top" align="center">
                <div style="min-height:80px;">
                    <p>
                        <strong>
                            @if ($invoiceSch->invb2b_pm == 'Installment')
                                {{ $currency == 'other' ? $receiptSch->invoiceInstallment->invoicedtl_amount : $receiptSch->invoiceInstallment->invoicedtl_amountidr }}
                            @else
                                @if ($invoiceSch->is_full_amount == 0)
                                    {{ $currency == 'other' ? $invoiceSch->invoiceSubTotalprice : $invoiceSch->invoiceSubTotalpriceIdr }}
                                @else
                                    {{ $currency == 'other' ? $invoiceSch->invoicePrice : $invoiceSch->invoicePriceIdr }}
                                @endif
                            @endif
                        </strong>
                    </p>
                </div>

                @if ($invoiceSch->invb2b_discidr != 0 && $invoiceSch->invb2b_discidr != null)
                    {{-- <div style="margin-top:5px;"> --}}
                    <p>
                        <strong> -
                            {{ $currency == 'other' ? $invoiceSch->invoiceDiscount : $invoiceSch->invoiceDiscountIdr }}</strong>
                    </p>
                    {{-- </div> --}}
                @endif
                @if ($receiptSch->pph23 > 0)
                    <p>
                        <strong>
                            ({{ $currency == 'other' ? $receiptSch->str_pph23 : $receiptSch->str_pph23_idr }})
                        </strong>
                    </p>
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="{{ $invoiceSch->is_full_amount == 0 && $invoiceSch->invb2b_pm == 'Full Payment' ? '4' : '3' }}"
                align="right"><b>Total</b></td>
            <td valign="middle" align="center">
                <b>
                    {{ $currency == 'other' ? $receiptSch->receipt_amount : $receiptSch->receipt_amount_idr }}
                </b>
            </td>
        </tr>
    </table>

    <p style="text-align: right; padding-right:5px">
        Updated On: {{ date('d/m/Y H:i:s', strtotime($receiptSch->updated_at)) }}
    </p>

    <table>
        <tr>
            <td>
                <b style="letter-spacing:0.7px;"><i>Total Amount :
                        {{ $currency == 'other' ? $receiptSch->receipt_words : $receiptSch->receipt_words_idr }}</i></b>
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
                {{ isset($receiptSch->receipt_date) ? date('d F Y', strtotime($receiptSch->receipt_date)) : date('d F Y', strtotime($receiptSch->created_at)) }}
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
