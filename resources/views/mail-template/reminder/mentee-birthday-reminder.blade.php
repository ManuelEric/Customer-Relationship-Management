@extends('layout.email')
@section('header', '')
@section('content')
    <style type="text/css">
        h1 {
            color: #d14d72; /* Pink heading */
            text-align: center;
            margin-bottom: 20px;
        }
        p {
            margin-bottom: 16px;
        }
        .birthday-message {
            font-size: 18px;
            font-weight: bold;
            color: #e67e22; /* Orange for the birthday message */
            text-align: center;
            margin-bottom: 20px;
        }
        .reminder-text {
            font-style: italic;
            color: #555555; /* Medium gray italic */
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #888888; /* Gray footer text */
        }
    </style>
    <table role="presentation" class="main">

        <!-- START MAIN CONTENT AREA -->
        <tr>
            <td class="wrapper">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            {{-- <h1>Happy Birthday Reminder!</h1> --}}
                            <p>Dear Mentor,</p>
                            <p class="birthday-message">This is a friendly reminder that your mentee(s) <b>{{ $mentees }}</b>, will be celebrating their birthday today.</p>
                            <p>It's a great opportunity to reach out and make their day special with a message or a small gesture.  Your support and encouragement mean a lot!</p>
                            <p class="reminder-text">Remember, a little effort can go a long way in strengthening your mentoring relationship.</p>

                            <p class="footer">This is an automated reminder from our mentoring program.  Please do not reply to this email.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- END MAIN CONTENT AREA -->
    </table>
@endsection
