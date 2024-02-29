@extends('layout.email-event')
@section('banner')
    <img src="{{asset('img/event/EduAll/banner_email_eduall_2024.webp')}}" alt="Edu All Launchpad" width="2500" style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;" >
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
                                
                                    <table>
                                        <tr>
                                            <td colspan="2">
                                                <div style="margin: 20px auto auto 25px;">
                                                    <b style="font-size: 20px;">{{ $event['eventName'] }}</b>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <div style="margin: auto 25px;"><hr></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div style="margin: auto 25px">
                                                    <table>
                                                        <tr>
                                                            <td valign="top">
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15 15"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M464 256A208 208 0 1 1 48 256a208 208 0 1 1 416 0zM0 256a256 256 0 1 0 512 0A256 256 0 1 0 0 256zM232 120V256c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2V120c0-13.3-10.7-24-24-24s-24 10.7-24 24z"/></svg>
                                                            </td>
                                                            <td valign="top">
                                                                <p style="text-align: left">
                                                                    {{ $event['eventDate_start'] }} at
                                                                    {{ date('g A', strtotime($event['eventTime_start'])) }} WIB
                                                                </p>
                                                            </td>
                                                            <td>
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15 20"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M280 64h40c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128C0 92.7 28.7 64 64 64h40 9.6C121 27.5 153.3 0 192 0s71 27.5 78.4 64H280zM64 112c-8.8 0-16 7.2-16 16V448c0 8.8 7.2 16 16 16H320c8.8 0 16-7.2 16-16V128c0-8.8-7.2-16-16-16H304v24c0 13.3-10.7 24-24 24H192 104c-13.3 0-24-10.7-24-24V112H64zm128-8a24 24 0 1 0 0-48 24 24 0 1 0 0 48z"/></svg>
                                                            </td>
                                                            <td>
                                                                <p style="text-align: center">
                                                                    Ticket Number<br>{{ $event['ticket'] }} 
                                                                    <div style="margin-bottom: 15px; text-align: center;">
                                                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $qr }}" alt="">
                                                                    </div>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/></svg>
                                                            </td>
                                                            <td valign="top">{{ strip_tags($event['eventLocation']) }}</td>
                                                            <td valign="top" colspan="2">
                                                                <p style="text-align: center; visibility:hidden">
                                                                    Check-in with this QR Code when you arrived at the event.
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

                                </div>

                            </p>

                            <p style="text-align: center">
                                Save this email & show the QR Code later for a swift entry!
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
    <div style="background-color: blue; color: white; padding: 50px;" >
        <p style="text-align: center;">
            This is a service email that is automatically generated when you do an event registration. If you do not wish to be registered for the event, please contact marketing.indonesia@eduall.com
            <br>
            <br>
            © 2024 Edu ALL, All rights reserved
        </p>
    </div>
@endsection
