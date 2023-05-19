@extends('layout.email')
@section('header', 'Invoice')
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
    <p style="margin:0;">Dear {{ $finance_name }},</p>
    <p>
        Here are the following partner information has not been completed:
    </p>
    <table class="table">
        <tr>
            <td>No.</td>
            <td>Partner Name</td>
        </tr>
        @foreach ($partner_have_no_pic as $data)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $data['partner_name'] }}</td>
            </tr>
        @endforeach
    </table>
    <br>
    <p>
        Please complete the following partner's email so we can send them a reminder
    </p>
@endsection
