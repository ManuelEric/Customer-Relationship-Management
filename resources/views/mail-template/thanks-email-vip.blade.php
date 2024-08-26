@extends('layout.email-stem')
@section('banner')
    <img loading="lazy"  src="{{ asset('img/makerspace/header_thank_email.jpg') }}" alt="STEM+ Wonderlab" width="2500"
        style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;">
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
                            <p>Dear Mr./Mrs. {{ $client['name'] }},</p>
                            <p>
                                Thank you for your {{ $notes }} registration at <b>STEM+ Wonderlab</b>, Indonesia's
                                FIRST Student Makerspace Expo!
                            </p>
                            <p>
                                By registering for this event, enjoy special privileges as our {{ $notes }} guests,
                                such as
                            </p>
                            <ul>
                                <li>
                                    <b>Priority access</b> via the dedicated {{ $notes }} lane and fast-track entry
                                    for your child to explore event booths.
                                </li>
                                <li>
                                    Delight in <b>exclusive merchandise</b> courtesy of ALL-in, adding to your special
                                    experience.
                                </li>
                                <li>
                                    Gain <b>exclusive access</b> to a range of special promotions and premium products
                                    offered
                                    by our respected sponsors and partners
                                </li>
                            </ul>

                            <p style="text-align: center;">
                                📍{{ strip_tags($event['eventLocation']) }}
                                <br>📅{{ $event['eventDate_start'] }} | {{ $event['eventTime_start'] }} WIB
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

                            <p class="font-size:10px;">
                                Now, it's time to spread the good news to others and earn our special professional
                                photoshoot
                                session for your child by inviting 3 of your friends by clicking this link
                            </p>
                            <p style="text-align: center;margin: 2.5em auto;">
                                <a class="button" href="{{ $referral_page }}"
                                    style="background: #3b6cde; 
                                    text-decoration: none; 
                                    padding: .5em 1.5em;
                                    color: #ffffff; 
                                    border-radius: 2px;
                                    mso-padding-alt:0;
                                    text-underline-color:#156ab3">
                                    <!--[if mso]><i style="letter-spacing: 25px;mso-font-width:-100%;mso-text-raise:20pt">&nbsp;</i><![endif]-->
                                    <span style="mso-text-raise:10pt;font-weight:bold;">Claim my link now</span>
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
    <img loading="lazy"  src="{{ asset('img/makerspace/email_footer.jpg') }}" alt="STEM+ Wonderlab" width="2500"
        style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;">
@endsection
