@extends('layout.email-stem')
@section('banner')
    <img src="{{ asset('img/makerspace/header_reminder_registration.jpg') }}" alt="STEM+ Wonderlab" width="2500"
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
                            <p>Dear Mr./Mrs. {{ $recipient }},</p>
                            <p>
                                Terima kasih telah menghadiri <b>STEM+ Wonderlab</b>, Indonesia's FIRST Student Makerspace Expo!
                            </p>

                            <p>
                                Setelah mendapat ide project dan mempraktekkannya lewat alat-alat canggih, sekarang saatnya anak Anda membawa ide projectnya ke level yang lebih tinggi di <b>Global Immersion Program: Innovators-in-Residence</b> di <b>Singapore</b> dan memecahkan permasalahan nyata di dunia!
                            </p>

                            <p style="text-align: center">
                                <img src="{{ asset('img/makerspace/GIP.jpg') }}" width="350" alt="">
                            </p>

                            <p style="text-align: center">
                                Dengan mendapatkan email ini, Anda berhak mendapatkan <b>diskon 100 USD</b> untuk program <b>Innovators-in-Residence!</b>
                            </p>

                            <p style="text-align: center">
                                Dapatkan sekarang di <br>
                                <a href="https://api.whatsapp.com/send?phone=+6281808081363&text={{ $wa_text_anggie }}" style="text-decoration: none;">
                                    <button style="border: 1px solid rgb(222, 222, 12); padding: 10px 16px 10px 16px; background-color:#F0833E; border-radius: 7px; color: #FFFFFF;">Anggie</button>
                                </a> 
                                atau 
                                <a href="https://api.whatsapp.com/send?phone=+6287860811413&text={{ $wa_text_derry }}" style="text-decoration: none;">
                                    <button style="border: 1px solid rgb(222, 222, 12); padding: 10px 16px 10px 16px; background-color: #F0833E; border-radius: 7px; color: #FFFFFF">Derry</button>
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
    <img src="{{ asset('img/makerspace/email_footer.jpg') }}" alt="STEM+ Wonderlab" width="2500"
        style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;">
@endsection
