@extends('layout.email')
{{-- @section('header', 'Thanks for Joining') --}}
@section('content')
    <table role="presentation" class="main">

        <!-- START MAIN CONTENT AREA -->
        <tr>
            <td class="wrapper">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <p>Dear Mr./Mrs. {{ $recipient }},</p>
                            <p>
                                We hope this message finds you well and brimming with excitement for <b>STEM+ Wonderlab</b>,
                                Indonesia's FIRST student makerspace expo. In this event, your child will gain access to
                                advanced tools, connect with fellow young innovators, collaborate on exciting projects, and
                                contribute to real impact on global issues!
                            </p>
                               
                            <p style="text-align: center;">
                                <b>STEM+ Wonderlab</b>
                                <br>📍{{ strip_tags($event['eventLocation']) }}
                                <br>🗓{{ $event['eventDate_start'] }} | {{ $event['eventTime_start'] }} WIB
                                <br>
                                Show this QR at the registration table at the event
                            </p>

                            <p>
                                As our VIP guest, we're here to remind you to also empower other parents and share the insightful experience
                            </p>
                            <p>
                                It's very simple:
                                <ul>
                                    <li>
                                        Step 1: Invite three of your friends by sharing this link: <a href="{{ $param['referral_page'] }}">Here</a>
                                    </li>
                                    <li>
                                        Step 2: Watch your child's profile shine with a professional photoshoot session by ALL-in
                                        Eduspace
                                    </li>
                                </ul>
                            </p>

                            <p>
                                Don't miss out on these benefits that are reserved for our VIP guests only! Take a moment now
                                to complete your registration and secure your place!
                            </p>

                            <p style="text-align: center;margin: 2.5em auto;">
                                    <a class="button" href="{{ $param['link'] }}"
                                        style="background: #3b6cde; 
                                        text-decoration: none; 
                                        padding: .5em 1.5em;
                                        color: #ffffff; 
                                        border-radius: 2px;
                                        mso-padding-alt:0;
                                        text-underline-color:#156ab3">
                                        <!--[if mso]><i style="letter-spacing: 25px;mso-font-width:-100%;mso-text-raise:20pt">&nbsp;</i><![endif]-->
                                        <span style="mso-text-raise:10pt;font-weight:bold;">Secure Your Spot Now!</span>
                                        <!--[if mso]><i style="letter-spacing: 25px;mso-font-width:-100%">&nbsp;</i><![endif]-->
                                    </a>
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
