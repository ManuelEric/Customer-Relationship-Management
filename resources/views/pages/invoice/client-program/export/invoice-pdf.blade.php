@extends('layout.invoice')
@section('invoice_id', $clientProg->invoice->inv_id)

@section('body')
    <div style="height:150px;">
        <table border="0" width="100%">
            <tr>
                <td width="60%">
                    <table width="100%" style="padding:0px; margin-left:-10px;">
                        <tr>
                            <td width="15%" valign="top">From: </td>
                            <td width="85%"><b>{{ $companyDetail['name'] }}</b><br>
                                {{ $companyDetail['address'] }} <br>
                                {{ $companyDetail['address_dtl'] }} <br>
                                {{ $companyDetail['city'] }}
                                <br><br>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">To : </td>
                            <td><b>
                                    {{ $clientProg->client->full_name }}
                                </b><br>
                                {{ html_entity_decode(strip_tags($clientProg->client->address)) }}
                                @if ($clientProg->client->city != null)
                                    {{ $clientProg->client->city }}
                                @endif
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
                                : &nbsp; {{ $clientProg->invoice->inv_id }}<br>
                                : &nbsp; {{ date('d F Y', strtotime($clientProg->invoice->created_at)) }} <br>
                                : &nbsp; {{ date('d F Y', strtotime($clientProg->invoice->inv_duedate)) }} <br>
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

    @if ($clientProg->invoice->inv_category == 'session')
        {{-- SESSION  --}}
        <table width="100%" class="table-detail" style="padding:8px 5px;">
            <tr align="center">
                <th width="5%">No</th>
                <th width="50%">Description</th>
                <th width="10%">Price/Hours</th>
                <th width="10%">Session</th>
                <th width="10%">Duration</th>
                <th width="15%">Total</th>
            </tr>
            <tr>
                <td valign="top" align="center">1</td>
                <td valign="top" style="padding-bottom:10px;">
                    <div style="min-height:80px;">
                        <p>
                            <strong> {{ $clientProg->program->program_name }} </strong>
                        </p>
                        <br>
                        <p class="notes">
                            {!! $clientProg->invoice->inv_notes !!}
                        </p>
                    </div>

                    <div style="margin-top:5px;">
                        <p>
                            <strong> Discount</strong>
                        </p>
                    </div>
                </td>
                <td valign="top" align="center">
                    <div style="min-height:80px;">
                        <p>
                            <strong>
                                {{ $clientProg->invoice->invoice_price_idr }}
                            </strong>
                        </p>
                    </div>
                </td>
                <td valign="top" align="center">
                    <p>{{ $clientProg->invoice->session }}x</p>
                </td>
                <td valign="top" align="center">
                    <p>{{ $clientProg->invoice->duration }} Min/Session</p>
                </td>
                <td valign="top" align="center">
                    <div style="min-height:80px;">
                        <p>
                            <strong>
                                @php
                                    $session = $clientProg->invoice->session;
                                    $duration = $clientProg->invoice->duration;
                                    $total_session = ($duration * $session) / 60; # hours;
                                @endphp
                                Rp. {{ number_format($clientProg->invoice->inv_price_idr * $total_session) }}
                            </strong>
                        </p>
                    </div>
                    <div style="margin-top:5px;">
                        <p>
                            <strong> - {{ $clientProg->invoice->invoice_discount_idr }}</strong>
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="5" align="right"><b>Total</b></td>
                <td valign="middle" align="center">
                    <b>Rp.
                        {{ number_format($clientProg->invoice->inv_price_idr * $total_session - $clientProg->invoice->inv_discount_idr) }}</b>
                </td>
            </tr>
        </table>
    @else
        {{-- NOT SESSION  --}}
        <table width="100%" class="table-detail" style="padding:8px 5px;">
            <tr align="center">
                <th width="5%">No</th>
                <th width="60%">Description</th>
                <th width="15%">Price</th>
                <th width="20%">Total</th>
            </tr>
            <tr>
                <td valign="top" align="center">1</td>
                <td valign="top" style="padding-bottom:10px;">
                    <div style="min-height:80px;">
                        <p>
                            <strong> {{ $clientProg->program->program_name }} </strong>
                        </p>
                        <p class="notes">
                            <br>
                            {{-- USD 5,400 (IDR 80,460,000) for Yeriel Abinawa Handoyo. <br>
                                            USD 2,750 (IDR 40,975,000) for Nemuell Jatinarendra Handoyo. --}}
                            {!! $clientProg->invoice->inv_notes !!}
                        </p>
                    </div>
                </td>
                <td valign="top" align="center">
                    <div style="min-height:80px;">
                        <p>
                            <strong>
                                {{ $clientProg->invoice->invoice_price_idr }}
                            </strong>
                        </p>
                    </div>
                </td>
                <td valign="top" align="center">
                    <div style="min-height:80px;">
                        <p>
                            <strong>
                                {{ $clientProg->invoice->invoice_price_idr }}
                            </strong>
                        </p>
                    </div>
                </td>
            </tr>
            @if ($clientProg->invoice->inv_earlybird_idr > 0)
                <tr>
                    <td colspan="3" align="right"><b>Early Bird</b></td>
                    <td valign="middle" align="center">
                        <b>{{ $clientProg->invoice->invoice_earlybird_idr }}</b>
                    </td>
                </tr>
            @endif
            @if ($clientProg->invoice->inv_discount_idr > 0)
                <tr>
                    <td colspan="3" align="right"><b>Discount</b></td>
                    <td valign="middle" align="center">
                        <b>{{ $clientProg->invoice->invoice_discount_idr }}</b>
                    </td>
                </tr>
            @endif
            <tr>
                <td colspan="3" align="right"><b>Total</b></td>
                <td valign="middle" align="center">
                    <b>{{ $clientProg->invoice->invoice_totalprice_idr }}</b>
                </td>
            </tr>
        </table>
    @endif

    <p style="text-align: right; padding-right: 5px;">
        Updated On: {{ date('d/m/Y H:i:s', strtotime($clientProg->invoice->created_at)) }}
    </p>

    <table>
        <tr>
            <td>
                <b style="letter-spacing:0.7px;"><i>Total Amount :
                        {{ $clientProg->invoice->inv_words_idr }}</i></b>
                <br><br>
            </td>
        </tr>
    </table>

    {{-- IF INSTALLMENT EXIST --}}
    @if ($clientProg->invoice()->has('invoiceDetail') && $clientProg->invoice->inv_paymentmethod == 'Installment')
        <table style="width: 100%; margin-top:-10px">
            <tr align="center">
                <th width="50%" style="border:0px !important;"></th>
                <th width="50%" style="border:0px !important;"></th>
            </tr>
            @foreach ($clientProg->invoice->invoiceDetail as $detail)
                {!! $loop->index == 0 ? '<tr><td valign="top">Terms of Payment : <br>' : null !!}
                {{ $detail->invdtl_installment . ' ' . $detail->invdtl_percentage . '% on ' . date('d F Y', strtotime($detail->invdtl_duedate)) . ' : ' . $detail->invoicedtl_amountidr }}
                <br>
                {!! $loop->index + 1 == round($clientProg->invoice->invoiceDetail->count() / 2)
                    ? '</td><td valign="top"><br>'
                    : null !!}
                {!! $loop->last ? '</td></tr>' : null !!}
            @endforeach
        </table>
    @endif

    <table>
        <tr>
            <td>
                {{-- IF TERMS & CONDITION EXIST  --}}
                @if (isset($clientProg->invoice->inv_tnc))
                    <br>
                    Terms & Conditions :
                    <div style="margin-left:2px;" class="tnc">
                        {!! $clientProg->invoice->inv_tnc !!}
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
                        </td>
                        <td width="78%">
                            : PT. Jawara Edukasih Indonesia <br>
                            : BCA <br>
                            : 2483016611 <br>
                            : KCP Pasar Kebayoran Lama Jakarta Selatan
                        </td>
                    </tr>
                </table>
            </td>
            <td width="40%" align="center" valign="top">
                {{ $companyDetail['name'] }}
                <br><br><br><br><br>
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