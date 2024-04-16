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
                            </td>
                            {{-- <td width="85%"><b>Jawara Edukasih International Pte Ltd</b><br>
                                        10 Anson Road<br>
                                        #27-18<br>
                                        International Plaza <br>
                                        Singapore (079903)
                                        <br><br>
                                    </td> --}}
                        </tr>
                        <tr>
                            <td valign="top">To : </td>
                            <td><b>
                                    {{ $invoiceB2b->partner_prog->corp->corp_name }}
                                </b><br>
                                @if (isset($invoiceB2b->partner_prog->corp->corp_address))
                                    {{ html_entity_decode(strip_tags($invoiceB2b->partner_prog->corp->corp_address)) }}
                                @else
                                    {{ $invoiceB2b->partner_prog->corp->corp_region }}
                                @endif
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
            {{-- <td>
                        Please process payment to Jawara Edukasih International Pte Ltd for the following services rendered :
                    </td> --}}
        </tr>
    </table>

    <table width="100%" class="table-detail" style="padding:8px 5px;">
        <tr align="center">
            <th width="5%">No</th>
            <th width="{{ $invoiceB2b->is_full_amount == 0 ? '35%' : '40%' }}">Description</th>
            @if ($invoiceB2b->is_full_amount == 0)
                <th width="25%">Price</th>
                <th width="10%">Participants</th>
            @endif
            <th width="25%">Total</th>
        </tr>
        <tr>
            {{-- No --}}
            <td valign="top" align="center">1</td>

            {{-- Description --}}
            <td valign="top" style="padding-bottom:10px;">
                <div style="height:80px;">
                    <p>
                        <strong> {{ $invoiceB2b->partner_prog->program->program_name }} </strong>
                    </p>
                    @if ($invoiceB2b->is_full_amount == 1)
                        <p>
                            {{ $invoiceB2b->invb2b_participants > 0 || $invoiceB2b->invb2b_participants != null ? 'Participants: ' . $invoiceB2b->invb2b_participants : '' }}
                        </p>
                        <br>
                    @endif
                    <p>
                        {!! $invoiceB2b->invb2b_notes !!}
                    </p>
                </div>

                @if ($invoiceB2b->invb2b_discidr != 0 && $invoiceB2b->invb2b_discidr != null)
                    <div style="margin-top:5px;">
                        <p>
                            <strong> Discount</strong>
                        </p>
                    </div>
                @endif
            </td>

            @if ($invoiceB2b->is_full_amount == 0)
                {{-- Price --}}
                <td valign="top" align="center">
                    <div style="height:80px;">
                        <p>
                            <strong>
                                {{ $currency == 'other' ? $invoiceB2b->invoicePrice : $invoiceB2b->invoicePriceIdr }}
                            </strong>
                        </p>
                    </div>
                </td>

                {{-- Participants --}}
                <td valign="top" align="center">
                    <p>
                        <strong>
                            {{ $invoiceB2b->invb2b_participants }}
                        </strong>
                    </p>
                </td>
            @endif

            {{-- Total --}}
            <td valign="top" align="center" class="text-center">
                <div style="height:80px">
                    <p>
                        @if ($invoiceB2b->is_full_amount == 0)
                            <strong>
                                {{ $currency == 'other' ? $invoiceB2b->invoiceSubTotalprice : $invoiceB2b->invoiceSubTotalpriceIdr }}
                            </strong>
                        @else
                            <strong>
                                {{ $currency == 'other' ? $invoiceB2b->invoicePrice : $invoiceB2b->invoicePriceIdr }}
                            </strong>
                        @endif
                    </p>
                </div>
                @if ($invoiceB2b->invb2b_discidr != 0 && $invoiceB2b->invb2b_discidr != null)
                    <div style="margin-top:5px;">
                        <p>
                            <strong> -
                                {{ $currency == 'other' ? $invoiceB2b->invoiceDiscount : $invoiceB2b->invoiceDiscountIdr }}</strong>
                        </p>
                    </div>
                @endif
            </td>
        </tr>

        {{-- Grand Total --}}
        <tr>
            <td colspan="{{ $invoiceB2b->is_full_amount == 0 ? '4' : '2' }}" align="right"><b>Total</b></td>
            <td valign="middle" align="center">
                <b>{{ $currency == 'other' ? $invoiceB2b->invoiceTotalprice : $invoiceB2b->invoiceTotalpriceIdr }}</b>
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

    {{-- IF INSTALLMENT EXIST --}}
    @if (count($invoiceB2b->inv_detail) > 0 && $invoiceB2b->invb2b_pm == 'Installment')
        <table style="width: 100%; margin-top:-10px">
            <tr align="center">
                <th width="50%" style="border:0px !important;"></th>
                <th width="50%" style="border:0px !important;"></th>
            </tr>
            @foreach ($invoiceB2b->inv_detail as $installment)
                {!! $loop->index == 0
                    ? '<tr>
                                                                                                            <td valign="top">Terms of Payment : <br>'
                    : null !!}
                {{ $installment->invdtl_installment . '  (' . $installment->invdtl_percentage . '%) ' . date('d F Y', strtotime($installment->invdtl_duedate)) }}
                {{ $currency == 'other' ? $installment->invoicedtlAmount : $installment->invoicedtlAmountIdr }}
                <br>
                {!! $loop->index + 1 == round($invoiceB2b->inv_detail->count() / 2)
                    ? '</td>
                                                                                                            <td valign="top"><br>'
                    : null !!}
                {!! $loop->last
                    ? '</td>
                                                                                                        </tr>'
                    : null !!}
            @endforeach
        </table>
    @endif

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
                            {{-- Branch Address <br> --}}
                            Swift Code <br>
                        </td>
                        <td width="78%">
                            : PT. Jawara Edukasih Indonesia <br>
                            : BCA <br>
                            : 2483016611 <br>
                            : KCP Pasar Kebayoran Lama Jakarta Selatan <br>
                            : CENAIDJA
                        </td>
                        {{-- <td width="78%">
                                    : Jawara Edukasih International Pte Ltd <br>
                                    : UOB <br>
                                    : 3963153242 <br>
                                    : United Overseas Bank Limited <br>
                                    : Raffless Place 80, UOB Plaza
                                </td> --}}
                    </tr>
                </table>
            </td>
            <td width="40%" align="center" valign="top">
                {{ $companyDetail['name'] }}
                {{-- Jawara Edukasih International Pte Ltd --}}
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
