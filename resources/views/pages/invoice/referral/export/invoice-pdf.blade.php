<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice : INV/123/2425/12312/23 - PDF</title>
    {{-- <link rel="icon" href="#" type="image/gif" sizes="16x16"> --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

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
                                        Jl Jeruk Kembar Blok Q9 No. 15 <br>
                                        Srengseng, Kembangan <br>
                                        DKI Jakarta
                                        <br><br>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top">To : </td>
                                    <td><b>
                                            Rainier Owen Harsono
                                        </b><br>
                                        Malang Jawa Timur
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
                                        : &nbsp; INV-/12124/21412/12<br>
                                        : &nbsp; 09 Desember 2022 <br>
                                        : &nbsp; 15 Desember 2022 <br>
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
                        Please process payment to PT Jawara Edukasih Indonesia for the following services rendered :
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
                            <strong> Admissions Mentoring: Ultimate Package </strong>
                        </p>
                        <p>
                            USD 5,400 (IDR 80,460,000) for Yeriel Abinawa Handoyo. <br>
                            USD 2,750 (IDR 40,975,000) for Nemuell Jatinarendra Handoyo.
                        </p>
                    </td>
                    <td valign="top" align="center">

                        <p>
                            <strong>
                                $8,150
                            </strong>
                        </p>
                    </td>
                    <td valign="top" align="center">

                        <p>
                            <strong>
                                $8,150
                            </strong>
                        </p>

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
                        <br><br>

                        {{-- IF INSTALLMENT EXIST --}}
                        Terms of Payment :
                        <div style="margin-left:2px;">
                        </div>


                        {{-- IF TERMS & CONDITION EXIST  --}}
                        <br>
                        Terms & Conditions :
                        <div style="margin-left:2px;">
                            - Program fee is not refundable
                        </div>
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
                        PT. Jawara Edukasih Indonesia
                        <br><br><br><br><br><br>
                        Nicholas Hendra Soepriatna <br>
                        Director
                    </td>
                </tr>
            </table>
        </div>
        <img src="{{ asset('img/pdf/footer.png') }}" width="100%">
    </div>
</body>

</html>
