@extends('layout.email')
@section('header', 'Reminder')
@section('content')
    <p style="margin:0;">Hi {{ ucwords($recipient['name']) }},</p>
    <p>
        I hope this message finds you well. This is a friendly reminder about your upcoming tutoring sessions. Please mark your calendar and make note of the following details:
    </p>
    <table>
        <tr>
            <td>Date</td>
            <td>:</td>
            <td>{{ $tutoring_detail['date'] }}</td>
        </tr>
        <tr>
            <td>Time</td>
            <td>:</td>
            <td>{{ $tutoring_detail['time'] }}</td>
        </tr>
        <tr>
            <td>Link</td>
            <td>:</td>
            <td><a href="{{ $tutoring_detail['link'] }}">{{ $tutoring_detail['link'] }}</a></td>
        </tr>
    </table>
    <p>
        Looking forward to seeing you at the tutoring session and working together to achieve your academic goals.
    </p>
    <p>
        Best regards, <br>
        All-in Eduspace
    </p>
@endsection
