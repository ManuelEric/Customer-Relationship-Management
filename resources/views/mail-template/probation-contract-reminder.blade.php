@extends('layout.email')
@section('header', 'Contract Expiration Notification - Probation')
@section('content')
    <p style="margin:0;">Dear HR Department,</p>
    <p>
        As per the record on Big Data, there is an upcoming contract expiration within your organization with details below:
    </p>
    <div style="background-color:bisque; padding: 20px;">
        <p>
            <table style="font-size:12px;">
                @php
                    $row = count($list_contracts);
                    $index = 1;
                @endphp
                @foreach ($list_contracts as $employee)
                <tr>
                    <td>Name</td>
                    <td>:</td>
                    <td>{{ $employee['full_name'] }}</td>
                </tr>
                <tr>
                    <td>Employment Type</td>
                    <td>:</td>
                    <td>{{ $employee['employment_type'] }}</td>
                </tr>
                <tr>
                    <td>Contract Start Date</td>
                    <td>:</td>
                    <td>{{ date('d F Y', strtotime($employee['contract_start_date'])) }}</td>
                </tr>
                <tr>
                    <td>Contract End Date</td>
                    <td>:</td>
                    <td>{{ date('d F Y', strtotime($employee['contract_end_date'])) }}</td>
                </tr>
                @if ($index < $row)
                <tr>
                    <td colspan="3"><hr></td>
                </tr>
                @endif
                @php
                    $index++;
                @endphp
                @endforeach
            </table>
        </p>
    </div>
    <p>
        Please initiate a thorough review of the contract terms, including any special conditions or provisions that may require attention. Also, start consider the decision to renew or finish the contract based on stakeholder review.
    </p>
    <p>
        Thank you for your attention to this matter.
    </p>
@endsection
