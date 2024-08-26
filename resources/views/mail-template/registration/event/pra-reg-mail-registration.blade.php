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
                            <p>Hello Mr./Mrs. {{ ucwords($client['name']) }},</p>
                            <p>
                                Thank you for registering to EduALL Launchpad: Where Your Future Takes Off. We are thrilled that you will be able to join us!
                            </p>
                            <p>
                                This event combines academic excellence with real-world experience. You will enjoy talks, gain inspiration, and engage in hands-on activities, exploring interests, building portfolios, and strengthening skills for university application and beyond.
                            </p>

                            <p>
                                The event details are as follows:
                            </p>

                            <p style="text-align: center;">
                                <div style="border: 1px solid #ccc; border-radius: 3px">
                                
                                    <table style="padding:25px; width: 100%;">
                                        <tr>
                                            <td>Event:</td>
                                            <td><b>{{ $event['eventName'] }}</b></td>
                                        </tr>
                                        <tr>
                                            <td>Start Date/Time:</td>
                                            <td>
                                                {{ $event['eventDate_start'] }}, at
                                                {{ date('g A', strtotime($event['eventTime_start'])) }} WIB
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <div style="margin-bottom: 15px; margin-top: 15px; text-align: center;">
                                                    <img loading="lazy"  src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $qr }}" style="border: 1px solid #ccc; padding: 10px; border-radius: 10px;" alt="">
                                                </div>

                                                <p style="text-align: center">
                                                    Save this email & show the QR Code later for a swift entry!
                                                </p>
                                            </td>
                                        </tr>
                                    </table>

                                </div>

                            </p>

                            

                            <p style="text-align: center">
                                We look forward to seeing you!
                            </p>
                        </td>
                    </tr>
                    <tr>

                    </tr>
                    <tr>
                        <td>
                            <p style="padding-top:20px;">
                                Best Regards,<br>
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
