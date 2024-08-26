@extends('layout.email-stem')
@section('banner')
    <img loading="lazy"  src="{{asset('img/makerspace/header_reminder_registration.jpg')}}" alt="STEM+ Wonderlab" width="2500" style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;" >
@endsection

{{-- @section('header', 'Thanks for Joining') --}}
@section('content')
    <table role="presentation" class="main">

        <!-- START MAIN CONTENT AREA -->
        <tr>
            <td class="wrapper">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <p>Dear Mr./Mrs. {{$recipient}}, we've noticed you haven't registered for <b>STEM+ Wonderlab</b> yet. It's not
                                too late to register for Indonesia's FIRST Student Makerspace Expo!</p>
                            <p>
                               
                            <p style="text-align: center;">
                                <b>STEM+ Wonderlab</b>
                                <br>📍{{ strip_tags($event['eventLocation']) }}
                                <br>📅{{ $event['eventDate_start'] }} | {{ $event['eventTime_start'] }} WIB
                                <br>
                                Show this QR at the registration table at the event
                            </p>

                            <p>
                                Here's what you'll enjoy as a registered {{ $notes }} guest participant:
                            </p>
                            <ul>
                                <li>
                                    <b>Priority access</b> via the dedicated {{ $notes }} lane and fast-track entry for your child to explore event booths.
                                </li>
                                <li>
                                    Delight in <b>exclusive merchandise</b> courtesy of ALL-in, adding to your special experience.
                                </li>
                                <li>
                                    Gain <b>exclusive access</b> to a range of special promotions and premium products offered
                                    by our respected sponsors and partners
                                </li>
                            </ul>

                            <p>
                                Don't miss out on these benefits that are reserved for our {{ $notes }} guests only! Take a moment now
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
                                    <span style="mso-text-raise:10pt;font-weight:bold;">Claim your {{ $notes }} pass</span>
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
@section('footer')
    <img loading="lazy"  src="{{asset('img/makerspace/email_footer.jpg')}}" alt="STEM+ Wonderlab" width="2500" style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;" >
@endsection

