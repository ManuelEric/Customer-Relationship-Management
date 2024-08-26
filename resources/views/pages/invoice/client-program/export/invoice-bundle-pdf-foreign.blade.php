@extends('layout.invoice')
@section('invoice_id', $bundle->invoice_b2c->inv_id)

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
                                {{ $bundle->details[0]->client_program->client->full_name }}
                            </b><br>
                            {{ html_entity_decode(strip_tags($bundle->details[0]->client_program->client->address)) }}
                            @if ($bundle->details[0]->client_program->client->city != null)
                                {{ $bundle->details[0]->client_program->client->city }}
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
                            : &nbsp; {{ $bundle->invoice_b2c->inv_id }}<br>
                            : &nbsp; {{ date('d F Y', strtotime($bundle->invoice_b2c->created_at)) }} <br>
                            : &nbsp; {{ date('d F Y', strtotime($bundle->invoice_b2c->inv_duedate)) }} <br>
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
                        <strong> {{ $bundle->details[0]->client_program->program->program_name }} </strong>
                    </p>
                    <p>
                        Bundle package with:
                    </p>
                    @for($i = 1; $i < count($bundle->details); $i++)
                        <p>
                            <strong> {{ $bundle->details[$i]->client_program->program->program_name }} </strong>
                        </p>
                    
                    @endfor
                </div>
                <p>
                    {{-- USD 5,400 (IDR 80,460,000) for Yeriel Abinawa Handoyo. <br>
                                USD 2,750 (IDR 40,975,000) for Nemuell Jatinarendra Handoyo. --}}
                    {!! $bundle->invoice_b2c->inv_notes !!}
                </p>
            </td>
            <td valign="top" align="center">
                <div style="min-height:80px;">
                    <p>
                        <strong>
                            {{ $bundle->invoice_b2c->invoice_price }}
                        </strong>
                    </p>
                </div>
            </td>
            <td valign="top" align="center">
                <div style="min-height:80px;">
                    <p>
                        <strong>
                            {{ $bundle->invoice_b2c->invoice_price }}
                        </strong>
                    </p>
                </div>
            </td>
        </tr>
        @if ($bundle->invoice_b2c->inv_earlybird_idr > 0)
            <tr>
                <td colspan="3" align="right"><b>Early Bird</b></td>
                <td valign="middle" align="center">
                    <b>{{ $bundle->invoice_b2c->invoice_earlybird }}</b>
                </td>
            </tr>
        @endif
        @if ($bundle->invoice_b2c->inv_discount_idr > 0)
            <tr>
                <td colspan="3" align="right"><b>Discount</b></td>
                <td valign="middle" align="center">
                    <b>{{ $bundle->invoice_b2c->invoice_discount }}</b>
                </td>
            </tr>
        @endif
        <tr>
            <td colspan="3" align="right"><b>Total</b></td>
            <td valign="middle" align="center">
                <b>{{ $bundle->invoice_b2c->invoice_totalprice }}</b>
            </td>
        </tr>
    </table>


<p style="text-align: right; padding-right: 5px;">
    Updated On: {{ date('d/m/Y', strtotime($bundle->invoice_b2c->created_at)) }}
</p>

<table>
    <tr>
        <td>
            <b style="letter-spacing:0.7px;"><i>Total Amount : {{ $bundle->invoice_b2c->inv_words }}</i></b>
            <br><br>
        </td>
    </tr>
</table>

{{-- IF INSTALLMENT EXIST --}}
@if ($bundle->invoice_b2c()->has('invoiceDetail') && $bundle->invoice_b2c->inv_paymentmethod == 'Installment')
    <table style="width: 100%; margin-top:-10px">
        <tr align="center">
            <th width="50%" style="border:0px !important;"></th>
            <th width="50%" style="border:0px !important;"></th>
        </tr>

        @foreach ($bundle->invoice_b2c->invoiceDetail as $detail)
            {!! $loop->index == 0 ? '<tr><td valign="top">Terms of Payment : <br>' : null !!}
            {{ $detail->invdtl_installment . ' ' . $detail->invdtl_percentage . '% on ' . date('d F Y', strtotime($detail->invdtl_duedate)) . ' : ' . $detail->invoicedtl_amount }}
            <br>
            {!! $loop->index + 1 == round($bundle->invoice_b2c->invoiceDetail->count() / 2)
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
            @if (isset($bundle->invoice_b2c->inv_tnc))
                <br>
                Terms & Conditions :
                <div style="margin-left:2px;">
                    {!! $bundle->invoice_b2c->inv_tnc !!}
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
