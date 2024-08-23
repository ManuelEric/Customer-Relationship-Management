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
                            <p>Dear Mr./Mrs. {{ $recipient }},</p>
                            <p>
                                Terima kasih telah menghadiri <b>STEM+ Wonderlab</b>, Indonesia's FIRST Student Makerspace Expo!
                            </p>

                            <p>
                                Setelah mendapat ide project dan mempraktekkannya lewat alat-alat canggih, sekarang saatnya anak Anda membawa ide projectnya ke level yang lebih tinggi di <b>Global Immersion Program: Innovators-in-Residence</b> di <b>Singapore</b> dan memecahkan permasalahan nyata di dunia!
                            </p>

                            <p style="text-align: center">
                                <a href="https://immersion-program.all-inedu.com/">
                                    <img loading="lazy"  src="{{ asset('img/makerspace/GIP.jpg') }}" width="350" alt="">
                                </a>
                            </p>

                            <p style="text-align: center">
                                Dengan mendapatkan email ini, Anda berhak mendapatkan <b>diskon 100 USD</b> untuk program <b>Innovators-in-Residence!</b>
                            </p>

                            <p style="text-align: center">
                                Dapatkan diskon Anda melalui tim Client Management ALL-in  sekarang!<br>
                                p.s. Penawaran ini akan berakhir pada Jumat, 30 November 2023.
                            </p>

                            <p style="text-align: center">
                                Dapatkan sekarang di <br>
                                <a href="https://api.whatsapp.com/send?phone=+6281808081363&text={{ $wa_text_anggie }}" style="text-decoration: none;">
                                    <button style="border: 1px solid rgb(222, 222, 12); padding: 15px 50px 15px 50px; background-color:#F0833E; border-radius: 7px; color: #FFFFFF; cursor: pointer;">Anggie</button>
                                </a> 
                                atau 
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
