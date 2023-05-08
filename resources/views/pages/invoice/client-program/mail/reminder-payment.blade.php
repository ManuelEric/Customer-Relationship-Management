@extends('layout.email')
@section('header', 'Invoice')
<style>
    .table, .table th, .table td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    .table th, .table td {
        padding: 15px;
    }
</style>
@section('content')
    <p style="margin:0;">Dear Mr./Mrs. {{ $parent_fullname }},</p>
    <p>
        Thank you for trusting ALL-in Eduspace to help your child's future to get into the world's top universities!
    </p>
    <p>
        Your payment deadline for <b>{{ $program_name }}</b> program is only <b>7 days left,</b> with due on <b>{{ $due_date }}.</b>
    </p>
    <p>
        Here are the payment details:
    </p>
    <table class="table">
        <tr>
            <td>Name</td>
            <td>{{ $child_fullname }}</td>
        </tr>
        <tr>
            <td>Program</td>
            <td>{{ $program_name }}</td>
        </tr>
        <tr>
            <td>Note</td>
            <td>{{ $installment_notes }}</td>
        </tr>
        <tr>
            <td>Total Payment</td>
            <td>{{ $total_payment }}</td>
        </tr>
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
        After making payment, please <b>confirm</b> via email to emilia@all-inedu.com by attaching your proof of payment.
    </p>
    <p style="font-weight: bold;">
        If payment has been made, please ignore this email.
    </p>
    <p>
        Thank you <br><br>
        Best regards,<br>
        ALL-in Eduspace
    </p>
@endsection
