<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $purchase->purchase_id }}</title>
    <style>
        h4 {
            font-size: 25px !important;
        }
        
        h6 {
            font-size: 15px !important;
        }
        
        .pdf {
            font-size: 12px !important;
            font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;
            margin: 0 20px;
        }
        
        .box {
            float: right;
            border: 1px solid #000;
            right: 40px;
            padding: 10px 30px;
            text-align: center;
            margin-top: 20px;
            background: #efefef;
        }
        
        .box small {
            font-size: 10px !important;
        }
        
        .box b {
            font-size: 15px !important;
        }
        
        table {
            border-collapse: collapse;
        }
        
        table tr td,
        th {
            padding: 4px 0px;
        }
        
        .table-detail td,
        th {
            border: 1px solid #dedede;
            padding: 8px 7px;
        }
    </style>
</head>
<body>
    <div class="pdf">
        <h4
            style="line-height:1.6; letter-spacing:3px; font-weight:bold; text-align:center; color:#606060; font-size:18px; margin-bottom:10px; ">
            <b>PT. JAWARA EDUKASIH INDONESIA</b>
        </h4>
        <hr style="border:1px dashed #606060;">
        <h6 style="text-align:center; color:#247df2;"><u>PURCHASE REQUEST</u></h6>
        <div class="box">
            <small>Purchase ID</small>
            <br>
            <b>{{ $purchase->purchase_id }}</b>
        </div>
        <table border="0" width="100%" class="tables">
            <tr>
                <td width="15%" valign="top">Department</td>
                <td width="3%">:</td>
                <td width="84%">{{ $purchase->department->dept_name }}</td>
            </tr>
            <tr>
                <td width="15%" valign="top">Request Status</td>
                <td width="3%">:</td>
                <td width="84%">{{ $purchase->purchase_statusrequest }}</td>
            </tr>
            <tr>
                <td width="15%" valign="top">Request Date</td>
                <td width="3%">:</td>
                <td width="84%">{{ date('D, d M Y', strtotime($purchase->purchase_requestdate)) }}</td>
            </tr>
            <tr>
                <td width="15%" valign="top">Notes</td>
                <td width="3%">:</td>
                <td width="85%">{{ strip_tags($purchase->purchase_notes) }}</td>
            </tr>
            <tr>
                <td width="15%" valign="top">Attachment</td>
                <td width="3%">:</td>
                <td width="85%">
                    @if ($purchase->purchase_attachment != NULL)
                        <a href="{{ public_path('storage/uploaded_file/finance/').$purchase->purchase_attachment }}">Download Attachment</a> 
                    @else
                        {{ "none" }}
                    @endif
                </td>
            </tr>
        </table>
        <br><br>
        <table width="100%" class="table-detail">
            <tr align="center"">
                <th width=" 3%">No</th>
                <th width="3%">Item Name</th>
                <th width="3%">Amount</th>
                <th width="3%">Price</th>
                <th width="3%">Total Price</th>
            </tr>
            @php
                $total = 0;
                $no = 1;
            @endphp
            @foreach ($details as $detail)
            <tr align="center"">
                <td width=" 5%">{{ $no++ }}</td>
                <td width="40%" align="left">{{ $detail->item }}</td>
                <td width="15%">{{ $detail->amount }}</td>
                <td width="15%">Rp. {{ number_format($detail->price_per_unit, 2, ",", ".") }}</td>
                <td width="15%">Rp. {{ number_format($detail->total, 2, ",", ".") }}</td>
            </tr>
            @php
                $total += $detail->total
            @endphp
            @endforeach
            <tr>
                <th colspan=4 align="center">
                    Total
                </th>
                <th align="center">
                    Rp. {{ number_format($total, 2, ",", ".") }}
                </th>
            </tr>
        </table>
    
        <br><br>
        <table border=0 width="100%">
            <tr>
                <td width="30%" align="center" valign="bottom">
                    Created by
                    <br><br><br><br><br>
                    (.....................................................)
                </td>
                <td width="40%"></td>
                <td width="30%" align="center" valign="top">
                    Jakarta, {{ date('D d M Y') }}<br>
                    Approved by
                    <br><br><br><br><br>
                    Devi Kasih
                </td>
            </tr>
        </table>
        <br>
    </div>
</body>
</html>