<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt : INV/123/2425/12312/23 - PDF</title>
    {{-- <link rel="icon" href="#" type="image/gif" sizes="16x16"> --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
        }

        h4 {
            font-size: 30px !important;
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
    <div style="width: 100%; height:1100px; padding:0; margin:0;">
        <img src="{{ asset('img/pdf/header.png') }}" width="100%">
        <img src="{{ asset('img/pdf/confidential.png') }}" width="85%"
            style="position:absolute; left:8%; top:25%; z-index:-999; opacity:0.04;">
        <div class="" style="height: 840px; padding:0 30px; margin-top:-40px;">
            <h4 style="">
                <b>PAYMENT RECEIPT</b>
            </h4>

            <table border="0" width="100%">
                <tr>
                    <td width="60%">
                        <table width="100%" style="padding:0px; margin-left:-10px;">
                            <tr>
                                <td width="15%" valign="top">From : </td>
                                <td width="85%"><b>PT. Jawara Edukasih Indonesia</b><br>
                                    Jl Jeruk Kembar Blok Q9 No. 15 <br>
                                    Srengseng, Kembangan <br>
                                    DKI Jakarta
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
                                    Gabriella Anna Santoso
                                    <br>
                                    DKI Jakarta
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
                        Receipt No. 0013/REC-JEI/SATPRIV/XII/22
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
                    <td>Payment Method</td>
                    <td>Cheque No.</td>
                    <td>Amount</td>
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
                                <strong> Admissions Mentoring: Ultimate Package </strong>
                            </p>
                        </div>

                        <div style="margin-top:5px;">
                            <p>
                                <strong> Discount</strong>
                            </p>
                        </div>
                    </td>
                    <td valign="top" align="center">
                        <div style="height:80px;">
                            <p>
                                <strong>
                                    $8,150
                                </strong>
                            </p>
                        </div>
                    </td>
                    <td valign="top" align="center">
                        <div style="height:80px;">
                            <p>
                                <strong>
                                    $8,150
                                </strong>
                            </p>
                        </div>
                        <div style="margin-top:5px;">
                            <p>
                                <strong> - $50</strong>
                            </p>
                            <p>
                                <strong> - $100</strong>
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" align="right"><b>Total</b></td>
                    <td valign="middle" align="center">
                        <b>$8,000</b>
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <td>
                        <b style="letter-spacing:0.7px;"><i>Total Amount : Eight thousand dollars</i></b>
                    </td>
                </tr>
            </table>

            <table border=0 width="100%">
                <tr>
                    <td width="60%" valign="top">
                    </td>
                    <td width="40%" align="center" valign="top">
                        PT. Jawara Edukasih Indonesia
                        <br><br><br><br><br><br>
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
        <img src="{{ asset('img/pdf/footer.png') }}" width="100%">
    </div>
</body>

</html>
