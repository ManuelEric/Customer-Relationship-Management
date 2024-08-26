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
                            <p style="text-align: center;">
                                <img loading="lazy"  src="{{ asset('img/makerspace/certificate/certificate_quest_level_'.$level.'-min.jpg') }}"
                                   height="250" alt="">
                                <br>
                                <br>
                                By receiving this email, you are entitled to a <b>100 USD discount</b> on the <b>Innovators-in-Residence</b> program!
                                <br>
                                Let’s find out about the program and claim your prize by contacting 
                                <br> 
                                <br>
                                <a href="https://api.whatsapp.com/send?phone=+6281808081363&text={{ $wa_text_anggie }}" style="text-decoration: none;">
                                    <button style="border: 1px solid rgb(222, 222, 12); padding: 10px 20px 10px 20px; background-color:#F0833E; border-radius: 7px; color: #FFFFFF; cursor: pointer;">Anggie</button>
                                </a> 
                                or 
                                <a href="https://api.whatsapp.com/send?phone=+6287860811413&text={{ $wa_text_derry }}" style="text-decoration: none;">
                                    <button style="border: 1px solid rgb(222, 222, 12); padding: 10px 20px 10px 20px; background-color: #F0833E; border-radius: 7px; color: #FFFFFF; cursor: pointer;">Derry</button>
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