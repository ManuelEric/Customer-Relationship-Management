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
                            <p>Dear {{ $client['name'] }},</p>
                            <p>
                                We are thrilled that you've decided to be a part of our upcoming event.
                                Your
                                presence will play a significant role in the success of this occasion.
                            </p>
                            <p>
                                As a token of our appreciation, we'd like to share a registration QR
                                code
                                that will streamline your experience when you arrive at the event venue.
                                This QR code can be used for re-registration on the event day, allowing
                                you
                                to swiftly and smoothly enter the array of activities we've prepared
                                with
                                utmost enthusiasm.
                            </p>

                            <table style="margin-bottom:20px; width:100%;">
                                <tr align="center">
                                    <td style="padding: 20px;">
                                        {!! QrCode::size(150)->generate($url) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table style="width:100%;">
                                            @if ($event['eventDate_start'] == $event['eventDate_end'])
                                            <tr>
                                                <td align="left" width="20%">📅 Date</td>
                                                <td>: {{ $event['eventDate_start'] }}</td>
                                            </tr>
                                            @else
                                            <tr>
                                                <td align="left" width="20%">📅 Date</td>
                                                <td>: {{ $event['eventDate_start'] }} - {{ $event['eventDate_end'] }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td align="left">🕒 Time</td>
                                                <td>: {{ $event['eventTime_start'] }} - {{ $event['eventTime_end'] }} WIB</td>
                                            </tr>
                                            <tr>
                                                <td align="left">📍 Location</td>
                                                <td>: {{ strip_tags($event['eventLocation']) }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <p>
                                QR Code Usage Instructions:
                            </p>
                            <table border="0">
                                <tr>
                                    <td width="5%">1.</td>
                                    <td>Save this QR code on your mobile device.</td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td>
                                        On the event day, present the QR code to the registration
                                        personnel at the venue to expedite the entry process.
                                    </td>
                                </tr>
                                <tr>
                                    <td>3.</td>
                                    <td>Enjoy our event and take the opportunity to connect with fellow
                                        peers.</td>
                                </tr>
                            </table>
                            <p style="margin-top: 20px;">
                                Once again, thank you for being a part of our community. We can't wait
                                to meet you on the event day!
                            </p>
                            @if(isset($notes) && $notes == 'VIP')
                                <p style="text-align: center;margin: 2.5em auto;">
                                    <a class="button" href="{{ $link }}"
                                        style="background: #3b6cde; 
                                        text-decoration: none; 
                                        padding: .5em 1.5em;
                                        color: #ffffff; 
                                        border-radius: 48px;
                                        mso-padding-alt:0;
                                        text-underline-color:#156ab3">
                                        <!--[if mso]><i style="letter-spacing: 25px;mso-font-width:-100%;mso-text-raise:20pt">&nbsp;</i><![endif]-->
                                        <span style="mso-text-raise:10pt;font-weight:bold;">Sign Now</span>
                                        <!--[if mso]><i style="letter-spacing: 25px;mso-font-width:-100%">&nbsp;</i><![endif]-->
                                    </a>
                                </p>
                            @endif
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
