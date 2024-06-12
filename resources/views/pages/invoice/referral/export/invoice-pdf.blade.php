@extends('layout.invoice')
@section('invoice_id', $invoiceB2b->invb2b_id)

@section('body')
    <div style="height:150px;">
        <table border="0" width="100%">
            <tr>
                <td width="60%">
                    <table width="100%" style="padding:0px; margin-left:-10px;">
                        <tr>
                            <td width="15%" valign="top">From : </td>
                            <td width="85%"><b>PT. Jawara Edukasih Indonesia</b><br>
                                {{ $companyDetail['address'] }} <br>
                                {{ $companyDetail['address_dtl'] }} <br>
                                {{ $companyDetail['city'] }}
                                <br><br>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">To : </td>
                            <td><b>
                                    {{ $invoiceB2b->referral->partner->corp_name }}
                                </b><br>
                                {{ $invoiceB2b->referral->partner->corp_region }}
                                <br>
                            </td>
                        </tr>
                    </table>
                </td>

                <td valign="top" width="45%">
                    <table border=0>
                        <tr>
                            <td>
                                Invoice No<br>
                                Date<br>
                                Due Date<br>
                            </td>
                            <td>
                                : &nbsp; {{ $invoiceB2b->invb2b_id }}<br>
                                : &nbsp; {{ date('d F Y', strtotime($invoiceB2b->invb2b_date)) }} <br>
                                : &nbsp; {{ date('d F Y', strtotime($invoiceB2b->invb2b_duedate)) }} <br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <tr>
            <td>
                Please process payment to {{ $companyDetail['name'] }} for the following services rendered :
            </td>
        </tr>
    </table>

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
                <p>
                    <strong> {{ $invoiceB2b->referral->additional_prog_name }} </strong>
                </p>
                <p>
                    {!! $invoiceB2b->invb2b_notes !!}
                </p>
            </td>
            <td valign="top" align="center">

                <p>
                    <strong>
                        {{ $currency == 'other' ? $invoiceB2b->invoiceTotalprice : $invoiceB2b->invoiceTotalpriceIdr }}</>
                    </strong>
                </p>
            </td>
            <td valign="top" align="center">

                <p>
                    <strong>
                        {{ $currency == 'other' ? $invoiceB2b->invoiceTotalprice : $invoiceB2b->invoiceTotalpriceIdr }}</>
                    </strong>
                </p>

            </td>
        </tr>
        <tr>
            <td colspan="3" align="right"><b>Total</b></td>
            <td valign="middle" align="center">
                {{ $currency == 'other' ? $invoiceB2b->invoiceTotalprice : $invoiceB2b->invoiceTotalpriceIdr }}</>
            </td>
        </tr>
    </table>

    <p style="text-align: right; padding-right: 5px;">
        Updated On: {{ date('d/m/Y H:i:s', strtotime($invoiceB2b->created_at)) }}
    </p>

    <table>
        <tr>
            <td>
                <b style="letter-spacing:0.7px;"><i>Total Amount :
                        {{ $currency == 'other' ? $invoiceB2b->invb2b_words : $invoiceB2b->invb2b_wordsidr }}</i></b>
                <br><br>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td>
                {{-- IF TERMS & CONDITION EXIST  --}}
                @if (isset($invoiceB2b->invb2b_tnc))
                    <br>
                    Terms & Conditions :
                    <div style="margin-left:2px;" class="tnc">
                        {!! $invoiceB2b->invb2b_tnc !!}
                    </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- BANK TRANSFER  --}}
    <br>
    <br>
    <table border=0 width="100%">
        <tr>
            <td width="60%" valign="top">
                <b>Bank transfer details :</b>
                <table border="0" style="padding:0px; margin-left:-6px;">
                    <tr>
                        <td>
                            Beneficiary <br>
                            Bank <br>
                            A/C No. <br>
                            Branch <br>
                            Swift Code <br>
                        </td>
                        <td width="78%">
                            : PT. Jawara Edukasih Indonesia <br>
                            : BCA <br>
                            : 2483016611 <br>
                            : KCP Pasar Kebayoran Lama Jakarta Selatan <br>
                            : CENAIDJA <br>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="40%" align="center" valign="top">
                {{ $companyDetail['name'] }}
                {{-- Jakarta, {{ date('d F Y') }} --}}
                <br><br><br><br><br><br><br>
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
@endsection
