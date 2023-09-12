@extends('layout.email')
@section('header', 'Thanks for Joining')
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
                                Indonesia! In this event, you will gain access to advanced tools, <b>connect</b> with fellow young
                                innovators, <b>collaborate</b> on exciting projects, and contribute to real impact on global issues!
                            </p>

                            <p style="text-align: center;">
                                <b>STEM+ Wonderlab</b>
                                <br>📍{{ strip_tags($event['eventLocation']) }}
                                <br>🗓{{ $event['eventDate_start'] }} | {{ $event['eventTime_start'] }} WIB
                                <br>
                                Show this QR at the registration table at the event
                            </p>

                                <p style="text-align: center;margin: 2.5em auto;">
                                    <a class="button" href="{{ $qr_page }}"
                                        style="background: #3b6cde; 
                                        text-decoration: none; 
                                        padding: .5em 1.5em;
                                        color: #ffffff; 
                                        border-radius: 2px;
                                        mso-padding-alt:0;
                                        text-underline-color:#156ab3">
                                        <!--[if mso]><i style="letter-spacing: 25px;mso-font-width:-100%;mso-text-raise:20pt">&nbsp;</i><![endif]-->
                                        <span style="mso-text-raise:10pt;font-weight:bold;">Your QR Confirmation</span>
                                        <!--[if mso]><i style="letter-spacing: 25px;mso-font-width:-100%">&nbsp;</i><![endif]-->
                                    </a>
                                </p>
                                
                            <p class="text-align: center">
                                This virtual event is <b>FREE</b>.
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
