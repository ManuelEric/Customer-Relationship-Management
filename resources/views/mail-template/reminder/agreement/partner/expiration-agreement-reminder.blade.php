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
    <p style="margin:0;">Hi Team,</p>
    <p>
        Just a quick reminder that our agreement with {{ $agreement['full_name'] }} is set to expire on {{ $agreement['end_date'] }}, about one month from now.
    </p>
    <p>
        Let's discuss any necessary actions or preparations for renewal or adjustments during our next meeting. Please feel free to share any updates or input beforehand.
    </p>
    <p>
        Best regards,<br>
        EduALL
    </p>
@endsection