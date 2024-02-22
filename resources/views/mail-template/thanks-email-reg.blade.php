@extends('layout.email-stem')
@section('banner')
    <img src="{{asset('img/makerspace/header_thank_email.jpg')}}" alt="STEM+ Wonderlab" width="2500" style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;" >
@endsection
@section('content')
    <table role="presentation" class="main">

        <!-- START MAIN CONTENT AREA -->
        <tr>
            <td class="wrapper">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <p>Dear Mr./Mrs. {{ $client['name'] }},</p>
                            <p>
                                Thank you for registering to <b>STEM+ Wonderlab</b>, the FIRST Student Makerspace Expo in
                                Indonesia! In this event, you will gain <b>access</b> to advanced tools, <b>connect</b> with fellow young
                                innovators, <b>collaborate</b> on exciting projects, and <b>contribute</b> to real impact on global issues!
                            </p>

                            <p style="text-align: center;">

                                <b>STEM+ Wonderlab</b>
                                <br>📍{{ strip_tags($event['eventLocation']) }}
                                <br>📅{{ $event['eventDate_start'] }} | {{ $event['eventTime_start'] }} WIB
                                <br>
                                Show this QR at the registration table at the event
                            </p>

                            <p style="text-align: center">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $qr }}" alt="">
                            </p>
                                
                            <p class="text-align: center">
                                <b>See you there</b>.
                            </p>
                            <p>
                                Warm regards, <br>
                                ALL-in Eduspace
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
    <img src="{{asset('img/makerspace/email_footer.jpg')}}" alt="STEM+ Wonderlab" width="2500" style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;" >
@endsection
