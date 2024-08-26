@extends('layout.email-stem')
@section('banner')
    <img loading="lazy"  src="{{ asset('img/makerspace/header_reminder_registration.jpg') }}" alt="STEM+ Wonderlab" width="2500"
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
                            <p>Dear {{ $recipient }},</p>
                            <p>
                                Thank you for attending <b>STEM+ Wonderlab</b>, the FIRST Student Makerspace Expo in Indonesia!
                            </p>

                            <p>
                                After getting a project idea and putting it into practice using sophisticated tools, now is the time for you to take your project idea to a higher level at the <b>Global Immersion Program: Innovators-in-Residence</b> in <b>Singapore</b> and solve real problems in the world!
                            </p>

                            <p style="text-align: center">
                                <a href="https://immersion-program.all-inedu.com/">
                                    <img loading="lazy"  src="{{ asset('img/makerspace/GIP.jpg') }}" width="350" alt="">
                                </a>
                            </p>

                            <p style="text-align: center">
                                By receiving this email, you are entitled to a <b>100 USD discount</b> on the <b>Innovators-in-Residence</b> program!
                            </p>

                            <p style="text-align: center">
                                Claim your discount to ALL-in Client Management Team right now!<br>
                                p.s. this offer will expire on Friday, 30 November 2023.

                            </p>

                            <p style="text-align: center">
                                Claim my discount to <br>
                                <a href="https://api.whatsapp.com/send?phone=+6281808081363&text={{ $wa_text_anggie }}" style="text-decoration: none;">
                                    <button style="border: 1px solid rgb(222, 222, 12); padding: 15px 50px 15px 50px; background-color:#F0833E; border-radius: 7px; color: #FFFFFF; cursor: pointer;">Anggie</button>
                                </a> 
                                or 
                                <a href="https://api.whatsapp.com/send?phone=+6287860811413&text={{ $wa_text_derry }}" style="text-decoration: none;">
                                    <button style="border: 1px solid rgb(222, 222, 12); padding: 15px 50px 15px 50px; background-color: #F0833E; border-radius: 7px; color: #FFFFFF; cursor: pointer;">Derry</button>
                                </a>
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
