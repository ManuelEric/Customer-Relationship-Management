<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt : {{ $receiptRef->receipt_id }} - PDF</title>
    {{-- <link rel="icon" href="#" type="image/gif" sizes="16x16"> --}}
    <style>
        /* @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); */
        @import url('{{ public_path("library/dashboard/css/googleapisfont.css") }}');

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
        <img src="{{ public_path('img/pdf/header.png') }}" width="100%">
        <img src="{{ public_path('img/pdf/confidential.png') }}" width="85%"
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
                    @if($receiptRef->receipt_method == 'Cheque')
                        <th width="35%">Cheque No.</th>
                    @endif
                    <th width="30%">Amount</th>
                </tr>
                <tr align="center">
                    <td>{{ $receiptRef->receipt_method }}</td>
                    @if($receiptRef->receipt_method == 'Cheque')
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
                        <div style="height:80px;">
                            <p>
                                <strong> {{ $receiptRef->invoiceB2b->referral->additional_prog_name }} </strong>
                            </p>
                        </div>
                                                
                        @if ($receiptRef->pph23 > 0)
                            <p>
                                PPH 23 {{$receiptRef->pph23}}%
                            </p>
                        @endif
                    </td>
                    <td valign="top" align="center">
                        <div style="height:80px;">
                            <p>
                                <strong>
                                    @if ($receiptRef->invoiceB2b->invb2b_pm == "Installment")
                                        {{ $currency == 'other' ? $receiptRef->invoiceInstallment->invoicedtl_amount :  $receiptRef->invoiceInstallment->invoicedtl_amountidr }}
                                    @else
                                        {{ $currency == 'other' ? $receiptRef->invoiceB2b->invoiceTotalprice : $receiptRef->invoiceB2b->invoiceTotalpriceIdr }}
                                    @endif
                                </strong>
                            </p>
                        </div>
                    </td>
                    <td valign="top" align="center">
                        <div style="height:80px;">
                            <p>
                                <strong>
                                    @if ($receiptRef->invoiceB2b->invb2b_pm == "Installment")
                                        {{ $currency == 'other' ? $receiptRef->invoiceInstallment->invoicedtl_amount :  $receiptRef->invoiceInstallment->invoicedtl_amountidr }}
                                    @else
                                        {{ $currency == 'other' ? $receiptRef->invoiceB2b->invoiceTotalprice : $receiptRef->invoiceB2b->invoiceTotalpriceIdr }}
                                    @endif
                                </strong>
                            </p>
                        </div>
                        @if($receiptRef->pph23 > 0)
                            <p>
                                <strong>
                                    ({{ $currency == 'other' ? $receiptRef->str_pph23 :  $receiptRef->str_pph23_idr }})
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

            <table>
                <tr>
                    <td>
                        <b style="letter-spacing:0.7px;"><i>Total Amount : {{ $currency == 'other' ? $receiptRef->receipt_words : $receiptRef->receipt_words_idr }}</i></b>
                    </td>
                </tr>
            </table>

            <table border=0 width="100%">
                <tr>
                    <td width="60%" valign="top">
                    </td>
                    <td width="40%" align="center" valign="top">
                        {{-- PT. Jawara Edukasih Indonesia --}}
                        Jakarta, {{ isset($receiptRef->receipt_date) ? date('d F Y', strtotime($receiptRef->receipt_date)) : date('d F Y', strtotime($receiptRef->created_at)) }}
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
    <img src="{{ public_path('img/pdf/footer.png') }}" width="100%">
</body>

</html>
