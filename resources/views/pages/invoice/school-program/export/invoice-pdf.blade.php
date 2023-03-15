<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice : {{ $invoiceSch->invb2b_id }} - PDF</title>
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

        h4 {
            font-size: 25px !important;
            font-family: 'Archivo Black', sans-serif;
            letter-spacing: 10px !important;
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
            <h4
                style="line-height:1.6; letter-spacing:3px; font-weight:bold; text-align:center; color:#247df2; font-size:18px; margin-bottom:10px; ">
                <u><b>INVOICE</b></u>
            </h4>
            <br><br>
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
                                            {{$invoiceSch->sch_prog->school->sch_name}}
                                        </b><br>
                                        {{$invoiceSch->sch_prog->school->sch_city}}
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
                                        : &nbsp; {{ $invoiceSch->invb2b_id }}<br>
                                        : &nbsp; {{ date("d F Y", strtotime($invoiceSch->invb2b_date)) }} <br>
                                        : &nbsp; {{ date("d F Y", strtotime($invoiceSch->invb2b_duedate)) }} <br>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>

            <br>
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
                    <th width="45%">Description</th>
                    <th width="25%">Price</th>
                    <th width="10%">Participants</th>
                    <th width="15%">Total</th>
                </tr>
                <tr>
                    <td valign="top" align="center">1</td>
                    <td valign="top" style="padding-bottom:10px;">
                        <div style="height:80px;">
                            <p>
                                <strong> {{ (($invoiceSch->sch_prog->program->prog_sub != '-')) ? $invoiceSch->sch_prog->program->prog_sub . ': ' . $invoiceSch->sch_prog->program->prog_program : $invoiceSch->sch_prog->program->prog_program }} </strong>
                            </p>
                            <p>
                               
                                {{-- USD 5,400 (IDR 80,460,000) for Yeriel Abinawa Handoyo. <br>
                                USD 2,750 (IDR 40,975,000) for Nemuell Jatinarendra Handoyo. --}}
                                {!! $invoiceSch->invb2b_notes !!}
                            </p>
                        </div>

                        @if($invoiceSch->invb2b_discidr != 0)
                            <div style="margin-top:5px;">
                                <p>
                                    <strong> Discount</strong>
                                </p>
                            </div>
                        @endif
                    </td>
                        <td valign="top" align="center">
                            <div style="height:80px;">
                                <p>
                                    <strong>
                                        {{ $currency == 'other' ? $invoiceSch->invoicePrice : $invoiceSch->invoicePriceIdr }}
                                    </strong>
                                </p>
                            </div>
                        </td>
                    <td valign="top" align="center">
                        <p>
                            <strong>
                                {{ $invoiceSch->invb2b_participants }}
                            </strong>
                        </p>
                    </td>
                    <td valign="top" align="center">
                        <div style="height:80px;width: 150px;">
                            <p>
                                <strong>
                                     {{ $currency == 'other' ? $invoiceSch->invoicePrice : $invoiceSch->invoicePriceIdr }}
                                </strong>
                            </p>
                        </div>
                        @if($invoiceSch->invb2b_discidr != 0)
                            <div style="margin-top:5px;">
                                <p>
                                    <strong> - {{ $currency == 'other' ? $invoiceSch->invoiceDiscount : $invoiceSch->invoiceDiscountIdr }}</strong>
                                </p>
                            </div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="4" align="right"><b>Total</b></td>
                    <td valign="middle" align="center">
                        <b>{{ $currency == 'other' ? $invoiceSch->invoiceTotalprice : $invoiceSch->invoiceTotalpriceIdr }}</b>
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <td>
                        <b style="letter-spacing:0.7px;"><i>Total Amount : {{  $currency == 'other' ? $invoiceSch->invb2b_words : $invoiceSch->invb2b_wordsidr }}</i></b>
                        <br><br>

                        {{-- IF INSTALLMENT EXIST --}}
                        @if(count($invoiceSch->inv_detail) > 0)
                            Terms of Payment :
                            <div style="margin-left:2px;">
                                @foreach ($invoiceSch->inv_detail as $installment)
                                    {{ $installment->invdtl_installment  . '  (' . $installment->invdtl_percentage .'%) ' . date("d F Y", strtotime($installment->invdtl_duedate)) }} {{$currency == 'other' ? $installment->invoicedtlAmount : $installment->invoicedtlAmountIdr}}
                                    <br>  
                                @endforeach
                            </div>
                        @endif


                        {{-- IF TERMS & CONDITION EXIST  --}}
                        @if(isset($invoiceSch->invb2b_tnc))
                            <br>
                            Terms & Conditions :
                            <div style="margin-left:2px;">
                                {!! $invoiceSch->invb2b_tnc !!}
                            </div>
                        @endif
                    </td>
                </tr>
            </table>

            {{-- BANK TRANSFER  --}}
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
                        <br><br><br><br><br><br>
                        Nicholas Hendra Soepriatna <br>
                        Director
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <img src="{{ public_path('img/pdf/footer.webp') }}" width="100%">
</body>

</html>
