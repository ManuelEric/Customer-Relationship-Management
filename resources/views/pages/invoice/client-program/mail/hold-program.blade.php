@extends('layout.email')
@section('header', '')
@section('style')
<style>
    .table, .table th, .table td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    .table th, .table td {
        padding: 15px;
    }
</style>
@endsection
@section('content')
    <p style="margin:0;">Dear Mr./Mrs. {{$parent_fullname}},</p>
    <p>
        I hope this message finds you well. I am writing to inform you that we have not yet received the installment payment that was due two weeks ago for {{ $child_name }} mentoring services.
    </p>
    <p>
        As outlined in our agreement, timely payments are essential to ensure the continuous provision of our services. Unfortunately, until the outstanding payment is received, <b>we will need to place {{ $child_name }} mentoring sessions on hold.</b>
    </p>
    <p>
        We understand that situations can arise, and if there are any issues or concerns regarding the payment, please do not hesitate to reach out. We are more than willing to discuss alternative arrangements if necessary. Please arrange for the payment as soon as possible so that we can resume {{ $child_name }} mentoring without further interruption.
    </p>
    <p>
        Here are the payment details:
    </p>
    <table class="table">
        <tr>
            <td>Name</td>
            <td>{{ $child_name }}</td>
        </tr>
        <tr>
            <td>Program</td>
            <td>{{ $program_name }}</td>
        </tr>
        @if($invDetail != null)
            <tr>
                <td>Note</td>
                <td>{{ $invDetail->invdtl_installment }}</td>
            </tr>
        @endif
        @if($invoiceMaster->currency != 'idr')
            <tr>
                <td>Total Payment</td>
                <td>{{ $invDetail != null? $invDetail->invoicedtlAmount : $invoiceMaster->invoiceTotalprice }}</td>
            </tr>
            <tr>
                <td>Total Payment IDR</td>
                <td>{{ $invDetail != null? $invDetail->invoicedtlAmountidr : $invoiceMaster->invoiceTotalpriceIdr }}</td>
            </tr>
        @else
            <tr>
                <td>Total Payment</td>
                <td>{{ $invDetail != null? $invDetail->invoicedtlAmountidr : $invoiceMaster->invoiceTotalpriceIdr }}</td>
            </tr>
        @endif
    </table>
    <br>
    <p>Please send the payment to:</p>
    <table class="table">
        <tr>
            <td>Bank</td>
            <td>Bank Central Asia (BCA)</td>
        </tr>
        <tr>
            <td>Account Number</td>
            <td>2483016611</td>
        </tr>
        <tr>
            <td>Beneficiary Name</td>
            <td>PT. Jawara Edukasih Indonesia</td>
        </tr>
    </table>
    <br>
    <p>
        After making payment, please <b>confirm</b> via email to emilia@edu-all.com by attaching your proof of payment.
    </p>
    <p>
        Thank you for your attention to this matter.
    </p>
    <p>
        Best regards,<br>
        EduALL
    </p>
@endsection
