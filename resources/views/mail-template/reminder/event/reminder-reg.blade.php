@extends('layout.email-event')
@section('banner')
    <img src="{{asset('img/event/EduAll/banner_email_eduall_2024.webp')}}" alt="EduAll Launchpad" width="2500" style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;" >
@endsection
@section('content')
    <table role="presentation" class="main">

        <!-- START MAIN CONTENT AREA -->
        <tr>
            <td class="wrapper">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <p>Hi {{ ucwords($recipient) }},</p>
                            <p>
                                This email is a reminder for you to attend EduALL Launchpad TOMORROW!
                            </p>

                            <p>
                                As a reminder, the event details are as follows: <br>
                                Event: EduALL Launchpad: Where Your Future Takes Off! <br>
                                Start Date/Time: {{ $event['eventDate_start'] }}, at {{ $event['eventTime_start'] }} WIB
                            </p>

                            <p style="text-align:center; ">
                                <b>
                                    <br>Save this email & show the QR Code later for express registration!
                                </b>
                                <br>
                                <br>
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $ticket_id }}"
                                    alt="">
                            </p>
                        
                            <p>
                                We hope you will enjoy the talk sessions, gain inspiration, and support your child to engage in hands-on activities, exploring interests, building portfolios, and strengthening skills for university application and beyond.
                            </p>

                            <p>
                                Best regards, <br>
                                EduALL Team 
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- END MAIN CONTENT AREA -->
    </table>
@endsection
@section('footer')
    <img src="{{asset('img/event/EduAll/banner-email-footer.png')}}" alt="EduAll Launchpad" width="2500" style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;" >
@endsection
