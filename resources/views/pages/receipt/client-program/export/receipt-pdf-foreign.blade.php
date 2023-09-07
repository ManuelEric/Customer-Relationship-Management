<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt : {{ $receipt->receipt_id }} - PDF</title>
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
                                    {{ $receipt->invoiceProgram->clientProg->client->parents[0]->full_name }}
                                    <br>
                                    {{-- @if ($receipt->invoiceProgram->clientProg->client->parents[0]->state != NULL)
                                        {{ $receipt->invoiceProgram->clientProg->client->parents[0]->state }}
                                    @endif --}}
                                    @if ($receipt->invoiceProgram->clientProg->client->address != NULL)
                                        {{ html_entity_decode(strip_tags($receipt->invoiceProgram->clientProg->client->address)) }}
                                    @endif
                                    @if ($receipt->invoiceProgram->clientProg->client->city != NULL)
                                        {{ $receipt->invoiceProgram->clientProg->client->city }}
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
                    <td>{{ $receipt->receipt_amount }}</td>
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
                                <strong> {{ $receipt->invoiceProgram->clientProg->invoice_program_name }} </strong>
                            </p>
                            @if ($receipt->invoiceProgram->inv_paymentmethod == "Installment")
                            <p>
                                {{ $receipt->invoiceInstallment->invdtl_installment }} ( {{ $receipt->invoiceInstallment->invdtl_percentage }}% )
                            </p>
                            @endif
                            <p>
                                {!! $receipt->invoiceProgram->inv_notes !!}
                            </p>
                        </div>
                    </td>
                    <td valign="top" align="center">
                        <div style="height:80px;">
                            <p>
                                <strong>
                                    @if ($receipt->invoiceProgram->inv_paymentmethod == "Installment")
                                        {{ $receipt->invoiceInstallment->invoicedtl_amount }}
                                    @else
                                    {{ $receipt->invoiceProgram->invoice_price }}
                                    @endif
                                </strong>
                            </p>
                        </div>
                    </td>
                    <td valign="top" align="center">
                        <div style="height:80px;">
                            <p>
                                <strong>
                                    @if ($receipt->invoiceProgram->inv_paymentmethod == "Installment")
                                        {{ $receipt->invoiceInstallment->invoicedtl_amount }}
                                    @else
                                        {{ $receipt->invoiceProgram->invoice_price }}
                                    @endif
                                </strong>
                            </p>
                        </div>
                    </td>
                </tr>
                @if ($receipt->invoiceProgram->inv_earlybird > 0)
                <tr>
                    <td colspan="3" align="right"><b>Early Bird</b></td>
                    <td valign="middle" align="center">
                        <b>  
                            {{ $receipt->invoiceProgram->invoice_earlybird }}
                        </b>
                    </td>
                </tr>
                @endif
                @if ($receipt->invoiceProgram->inv_discount > 0)
                <tr>
                    <td colspan="3" align="right"><b>Discount</b></td>
                    <td valign="middle" align="center">
                        <b>  
                            {{ $receipt->invoiceProgram->invoice_discount }}
                        </b>
                    </td>
                </tr>
                @endif
                <tr>
                    <td colspan="3" align="right"><b>Total</b></td>
                    <td valign="middle" align="center">
                        <b>
                            {{ $receipt->receipt_amount }}
                        </b>
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <td>
                        <b style="letter-spacing:0.7px;"><i>Total Amount : {{ $receipt->receipt_words }}</i></b>
                    </td>
                </tr>
            </table>

            <table border=0 width="100%">
                <tr>
                    <td width="60%" valign="top">
                    </td>
                    <td width="40%" align="center" valign="top">
                        {{-- PT. Jawara Edukasih Indonesia --}}
                        Jakarta, {{ date('d F Y') }}
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
    <img src="{{ public_path('img/pdf/footer.webp') }}" style="position:relative;" width="100%">
</body>

</html>
