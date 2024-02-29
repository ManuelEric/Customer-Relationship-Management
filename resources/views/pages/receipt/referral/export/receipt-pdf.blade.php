@extends('layout.receipt')
@section('receipt_id', $receiptRef->receipt_id)

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
                            {{ $receiptRef->invoiceB2b->referral->partner->corp_name }}
                            <br>
                            {{ $receiptRef->invoiceB2b->referral->partner->corp_region }}
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
                Receipt No. {{ $receiptRef->receipt_id }}
            </td>
        </tr>
    </table>

    <table width="100%" class="table-detail" style="padding:8px 5px;">
        <tr align="center">
            <th width="35%">Payment Method</th>
            @if ($receiptRef->receipt_method == 'Cheque')
                <th width="35%">Cheque No.</th>
            @endif
            <th width="30%">Amount</th>
        </tr>
        <tr align="center">
            <td>{{ $receiptRef->receipt_method }}</td>
            @if ($receiptRef->receipt_method == 'Cheque')
                <td>{{ $receiptRef->receipt_cheque }}</td>
            @endif
            <td>{{ $currency == 'other' ? $receiptRef->receipt_amount : $receiptRef->receipt_amount_idr }}</td>
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
                        <strong> {{ $receiptRef->invoiceB2b->referral->additional_prog_name }} </strong>
                    </p>
                </div>

                @if ($receiptRef->pph23 > 0)
                    <p>
                        PPH 23 {{ $receiptRef->pph23 }}%
                    </p>
                @endif
            </td>
            <td valign="top" align="center">
                <div style="min-height:80px;">
                    <p>
                        <strong>
                            @if ($receiptRef->invoiceB2b->invb2b_pm == 'Installment')
                                {{ $currency == 'other' ? $receiptRef->invoiceInstallment->invoicedtl_amount : $receiptRef->invoiceInstallment->invoicedtl_amountidr }}
                            @else
                                {{ $currency == 'other' ? $receiptRef->invoiceB2b->invoiceTotalprice : $receiptRef->invoiceB2b->invoiceTotalpriceIdr }}
                            @endif
                        </strong>
                    </p>
                </div>
            </td>
            <td valign="top" align="center">
                <div style="min-height:80px;">
                    <p>
                        <strong>
                            @if ($receiptRef->invoiceB2b->invb2b_pm == 'Installment')
                                {{ $currency == 'other' ? $receiptRef->invoiceInstallment->invoicedtl_amount : $receiptRef->invoiceInstallment->invoicedtl_amountidr }}
                            @else
                                {{ $currency == 'other' ? $receiptRef->invoiceB2b->invoiceTotalprice : $receiptRef->invoiceB2b->invoiceTotalpriceIdr }}
                            @endif
                        </strong>
                    </p>
                </div>
                @if ($receiptRef->pph23 > 0)
                    <p>
                        <strong>
                            ({{ $currency == 'other' ? $receiptRef->str_pph23 : $receiptRef->str_pph23_idr }})
                        </strong>
                    </p>
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="3" align="right"><b>Total</b></td>
            <td valign="middle" align="center">
                <b>
                    {{ $currency == 'other' ? $receiptRef->receipt_amount : $receiptRef->receipt_amount_idr }}
                </b>
            </td>
        </tr>
    </table>

    <p style="text-align: right; padding-right:5px">
        Updated On: {{ date('d/m/Y H:i:s', strtotime($receiptRef->updated_at)) }}
    </p>

    <table>
        <tr>
            <td>
                <b style="letter-spacing:0.7px;"><i>Total Amount :
                        {{ $currency == 'other' ? $receiptRef->receipt_words : $receiptRef->receipt_words_idr }}</i></b>
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
                {{ isset($receiptRef->receipt_date) ? date('d F Y', strtotime($receiptRef->receipt_date)) : date('d F Y', strtotime($receiptRef->created_at)) }}
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
