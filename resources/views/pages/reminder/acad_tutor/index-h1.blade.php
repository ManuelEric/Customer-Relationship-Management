@extends('layout.email')
@section('header', 'Reminder')
@section('content')
    <p style="margin:0;">Hi {{ ucwords($recipient['name']) }},</p>
    <p>
        I hope this message finds you well. This is a friendly reminder about your upcoming tutoring session in 24 hours. Please mark your calendar and make note of the following details:
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
            <td><a href="{{ $tutoring_detail['link'] }}">Join Now</a></td>
        </tr>
    </table>
    <p>
        If you want to reschedule the session please inform it to the <b>Education Coordinator</b> today by clicking 
        <a href="https://wa.me/+6281774821143">here</a>.
    </p>
    <p>
        Looking forward to seeing you at the tutoring session and can't wait to work together to achieve your academic goals.
    </p>
    <p>
        Best regards, <br>
        All-in Eduspace
    </p>
@endsection
