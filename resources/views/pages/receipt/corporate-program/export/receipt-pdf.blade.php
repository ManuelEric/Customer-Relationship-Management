<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt : {{ $receiptPartner->receipt_id }} - PDF</title>
    {{-- <link rel="icon" href="#" type="image/gif" sizes="16x16"> --}}
    <style>
        /* @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); */
        @import url('{{ public_path("dashboard-template/css/googleapisfont.css") }}');
         * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        body {
            font-family: 'Poppins', sans-serif;
        }

        h3 {
            font-size: 26px !important;
            font-weight: 800;
            font-family: 'Archivo Black', sans-serif;
            letter-spacing: 5px !important;
            color: #9d9c9c;
        }

        p {
            margin: 0;
            line-height: 1.2;
        }

        table {
            border-collapse: collapse;
        }

        table tr td,
        th {
            padding: 8px 7px;
            line-height: 16px;
        }

        .table-detail th {
            background: #EEA953;
            color: #fff;
            border: 1px solid #ce8e40;
        }

        .table-detail td,
        th {
            border: 1px solid #dedede;
        }
    </style>
</head>

<body style="padding: 0; margin:0">
    <div style="width: 100%; height:1059px; padding:0; margin:0;">
        <img src="{{ public_path('img/pdf/header.webp') }}" width="100%">
        <img src="{{ public_path('img/pdf/confidential.webp') }}" width="85%"
            style="position:absolute; left:8%; top:25%; z-index:-999; opacity:0.04;">
        <div class="" style="height: 840px; padding:0 30px; margin-top:-40px;">
            <h3 style="">
                PAYMENT RECEIPT
            </h3>

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
                                {{-- <td width="85%"><b>Jawara Edukasih International Pte Ltd</b><br>
                                    10 Anson Road<br>
                                    #27-18<br>
                                    International Plaza <br>
                                    Singapore (079903)
                                    <br><br>
                                </td> --}}
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
                                    {{ $invoicePartner->partner_prog->corp->corp_name }}
                                    <br>
                                    @if(isset($invoicePartner->partner_prog->corp->corp_address))
                                        {{ html_entity_decode(strip_tags($invoicePartner->partner_prog->corp->corp_address)) }}
                                    @else
                                        {{ $invoicePartner->partner_prog->corp->corp_region }}
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
                        Receipt No. {{ $receiptPartner->receipt_id }}
                    </td>
                </tr>
            </table>

            <table width="100%" class="table-detail" style="padding:8px 5px;">
                <tr align="center">
                    <th width="35%">Payment Method</th>
                    @if($receiptPartner->receipt_method == 'Cheque')
                        <th width="35%">Cheque No.</th>
                    @endif
                    <th width="30%">Amount</th>
                </tr>
                <tr align="center">
                    <td>{{ $receiptPartner->receipt_method }}</td>
                    @if($receiptPartner->receipt_method == 'Cheque')
                        <td>{{ $receiptPartner->receipt_cheque }}</td>
                    @endif
                    <td>{{ $currency == 'other' ? $receiptPartner->receipt_amount : $receiptPartner->receipt_amount_idr }}</td>
                </tr>
            </table>
            <br>

            <table width="100%" class="table-detail" style="padding:8px 5px;">
                <tr align="center">
                    <th width="5%">No</th>
                    <th width="{{$invoicePartner->is_full_amount == 0 ? '35%' : '70%' }}">Description</th>
                    <th width="25%">Price</th>
                     @if($invoicePartner->is_full_amount == 0 && $invoicePartner->invb2b_pm == 'Full Payment')
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
                                <strong> {{ $invoicePartner->partner_prog->program->program_name }} </strong>
                            </p>
                            @if ($invoicePartner->invb2b_pm == "Installment")
                                <p>
                                    {{ $receiptPartner->invoiceInstallment->invdtl_installment }} ( {{ $receiptPartner->invoiceInstallment->invdtl_percentage }}% )
                                </p>
                            @endif
                              @if($invoicePartner->is_full_amount == 1)
                                <p>
                                    {{ $invoicePartner->invb2b_participants > 0 && $invoicePartner->invb2b_participants != null ? 'Participants: ' . $invoicePartner->invb2b_participants : ''}}
                                </p>
                                <br>
                            @endif
                             <p>
                                {!! $invoicePartner->invb2b_notes !!}
                            </p>
                        </div>

                        @if($invoicePartner->invb2b_discidr != 0 && $invoicePartner->invb2b_discidr != null)
                            <div style="margin-top:5px;">
                                <p>
                                    <strong> Discount</strong>
                                </p>
                            </div>
                        @endif
                        
                        @if ($receiptPartner->pph23 > 0)
                            <p>
                                PPH 23 {{$receiptPartner->pph23}}%
                            </p>
                        @endif
                    </td>

                    {{-- Price --}}
                    <td valign="top" align="center">
                        <div style="height:80px;">
                            <p>
                                <strong>
                                    @if ($invoicePartner->invb2b_pm == "Installment")
                                        {{ $currency == 'other' ? $receiptPartner->invoiceInstallment->invoicedtl_amount :  $receiptPartner->invoiceInstallment->invoicedtl_amountidr }}
                                    @else
                                        {{ $currency == 'other' ? $invoicePartner->invoicePrice : $invoicePartner->invoicePriceIdr }}
                                    @endif
                                </strong>
                            </p>
                        </div>
                    </td>

                    @if($invoicePartner->is_full_amount == 0 && $invoicePartner->invb2b_pm == 'Full Payment')
                        {{-- Participants --}}
                        <td valign="top" align="center">
                            <p>
                                <strong>
                                    {{ $invoicePartner->invb2b_participants }}
                                </strong>
                            </p>
                        </td>
                    @endif

                    {{-- Total --}}
                    <td valign="top" align="center">
                        <div style="height:80px;">
                            <p>
                                <strong>
                                    @if ($invoicePartner->invb2b_pm == "Installment")
                                        {{ $currency == 'other' ? $receiptPartner->invoiceInstallment->invoicedtl_amount :  $receiptPartner->invoiceInstallment->invoicedtl_amountidr }}
                                    @else
                                        @if($invoicePartner->is_full_amount == 0)
                                            {{ $currency == 'other' ? $invoicePartner->invoiceSubTotalprice : $invoicePartner->invoiceSubTotalpriceIdr }}
                                        @else
                                            {{ $currency == 'other' ? $invoicePartner->invoicePrice : $invoicePartner->invoicePriceIdr }}
                                        @endif              
                                    @endif
                                </strong>
                            </p>
                        </div>
                        @if($invoicePartner->invb2b_discidr != 0 && $invoicePartner->invb2b_discidr != null)
                            {{-- <div style="margin-top:5px;"> --}}
                                <p>
                                    <strong> - {{ $currency == 'other' ? $invoicePartner->invoiceDiscount : $invoicePartner->invoiceDiscountIdr }}</strong>
                                </p>
                            {{-- </div> --}}
                        @endif
                        @if($receiptPartner->pph23 > 0)
                            <p>
                                <strong>
                                    ({{ $currency == 'other' ? $receiptPartner->str_pph23 :  $receiptPartner->str_pph23_idr }})
                                </strong>
                            </p>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="{{$invoicePartner->is_full_amount == 0 && $invoicePartner->invb2b_pm == 'Full Payment' ? '4' : '3'}}" align="right"><b>Total</b></td>
                    <td valign="middle" align="center">
                        <b>
                            {{ $currency == 'other' ? $receiptPartner->receipt_amount : $receiptPartner->receipt_amount_idr }}
                        </b>
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <td>
                        <b style="letter-spacing:0.7px;"><i>Total Amount : {{ $currency == 'other' ? $receiptPartner->receipt_words : $receiptPartner->receipt_words_idr }}</i></b>
                    </td>
                </tr>
            </table>

            <table border=0 width="100%">
                <tr>
                    <td width="60%" valign="top">
                    </td>
                    <td width="40%" align="center" valign="top">
                        {{-- PT. Jawara Edukasih Indonesia --}}
                        Jakarta, {{ isset($receiptPartner->receipt_date) ? date('d F Y', strtotime($receiptPartner->receipt_date)) : date('d F Y', strtotime($receiptPartner->created_at)) }}
                        <br><br><br><br><br><br><br>
                        Nicholas Hendra Soepriatna <br>
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
    </div>
    <img src="{{ public_path('img/pdf/footer.webp') }}" width="100%">
</body>

</html>
