@extends('layout.email')
@section('header', 'Request Sign - Invoice')
@section('content')
    <p style="margin:0;">Dear {{ ucfirst($recipient) }},</p>
    <p>
        Please sign the invoice with details below:
    </p>
    <table border="0" style="margin:0;">
        <tr>
            <td>Full Name</td>
            <td>:</td>
            <td>{{ ucwords($param['fullname']) }}</td>
        </tr>
        <tr>
            <td>Program Name</td>
            <td>:</td>
            <td>{{ $param['program_name'] }}</td>
        </tr>
        <tr>
            <td>Invoice Date</td>
            <td>:</td>
            <td>{{ $param['invoice_date'] }}</td>
        </tr>
        <tr>
            <td>Invoice Due Date</td>
            <td>:</td>
            <td>{{ $param['invoice_duedate'] }}</td>
        </tr>
    </table>
    <p>

    </p>

    <p style="text-align: center;margin: 2.5em auto;">
        <a class="button"
            href="{{ route('invoice-ref.sign_document', ['invoice' => $param['invb2b_num'], 'currency' => $param['currency']]) }}?token={{ csrf_token() }} "
            style="background: #3b6cde; 
             text-decoration: none; 
             padding: .5em 1.5em;
             color: #ffffff; 
             border-radius: 48px;
             mso-padding-alt:0;
             text-underline-color:#156ab3">
            <!--[if mso]><i style="letter-spacing: 25px;mso-font-width:-100%;mso-text-raise:20pt">&nbsp;</i><![endif]-->
            <span style="mso-text-raise:10pt;font-weight:bold;">Sign Now</span>
            <!--[if mso]><i style="letter-spacing: 25px;mso-font-width:-100%">&nbsp;</i><![endif]-->
        </a>
    </p>
    <p>
        Thank you <br>
        Regards
    </p>
@endsection
