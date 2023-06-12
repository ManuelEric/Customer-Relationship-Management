@extends('layout.email')
@section('header', 'Reminder')
@section('content')
    <p style="margin:0;">Hi {{ ucwords($recipient['name']) }},</p>
    <p>
        Just a quick reminder that your tutoring session is scheduled to begin in just 3 hours. Please make sure to be prepared and ready to make the most of our time together. Here are the details:    
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
        Remember to gather any necessary materials and resources beforehand to ensure a smooth and productive session.
    </p>
    <p>
        Looking forward to our session. See you soon!
    </p>
    <p>
        Best regards, <br>
        All-in Eduspace
    </p>
@endsection
