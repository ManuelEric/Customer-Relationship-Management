@extends('layout.email-event')
@section('banner')
    <img loading="lazy"  src="{{asset('img/event/EduAll/banner_email_eduall_2024.webp')}}" alt="EduAll Launchpad" width="2500" style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;" >
@endsection
@section('content')
    <table role="presentation" class="main">

        <!-- START MAIN CONTENT AREA -->
        <tr>
            <td class="wrapper">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <p>Dear Mr./Mrs. {{ $client['recipient'] }},</p>
                            <p>
                                We are excited to announce that ALL-in Eduspace is evolving into EduALL! 
                            </p>
                            <p>
                                In hope to celebrate this milestone, we cordially invite you to our grand launching:
                            </p>

                            <p>
                                EduALL Launchpad: Where Your Future Takes Off!
                                <br>
                                {{ $event['eventDate_start'] }}, {{ $event['eventTime_start'] }} WIB

                            </p>

                            <p style="text-align: center;margin: 2.5em auto;">
                                <a class="button" href="{{ $cta }}"
                                    style="background: #3b6cde; 
                                        text-decoration: none; 
                                        padding: .6em 1.5em;
                                        color: #ffffff; 
                                        border-radius: 2px;
                                        mso-padding-alt:0;
                                        text-underline-color:#156ab3">
                                        <!--[if mso]><i style="letter-spacing: 25px;mso-font-width:-100%;mso-text-raise:20pt">&nbsp;</i><![endif]-->
                                        <span style="mso-text-raise:10pt;font-weight:bold;">Click Here for More Info</span>
                                        <!--[if mso]><i style="letter-spacing: 25px;mso-font-width:-100%">&nbsp;</i><![endif]-->
                                </a>
                            </p>
                        
                            <p>
                                In EduALL Launchpad, we invite you and your child to gain insights and experience hands-on activities in university application preparation to enter the world’s best universities.
                            </p>

                            <p>
                                We hope to connect with you soon!
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
<img loading="lazy"  src="{{asset('img/event/EduAll/banner-email-footer.webp')}}" alt="EduAll Launchpad" width="2500" style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;" >
@endsection
