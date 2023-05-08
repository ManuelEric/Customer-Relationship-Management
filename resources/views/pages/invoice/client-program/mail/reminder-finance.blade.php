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
    <p style="margin:0;">Dear {{ $finance_name }},</p>
    <p>
        Here are the following client information has not been completed:
    </p>
    <table class="table">
        <tr>
            <td>No.</td>
            <td>Name</td>
            <td>Phone Number</td>
        </tr>
        @foreach ($parents_have_no_email as $data)
            <tr>
                <td>{{ $loop->iterations }}</td>
                <td>{{ $data['fullname'] }}</td>
                <td>{{ $data['phone'] }}</td>
            </tr>
        @endforeach
    </table>
    <br>
    <p>
        Please complete the following client's email so we can send them a reminder
    </p>
@endsection
