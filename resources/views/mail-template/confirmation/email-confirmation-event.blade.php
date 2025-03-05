@extends('layout.email')
@section('header', '')
@section('content')
<table role="presentation" class="main">

    <!-- START MAIN CONTENT AREA -->
    <tr>
        <td class="wrapper">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    @switch($role_name)
                    
                        @case('Parent')
                            <td>
                                <p>Halo {{ $recipient }},</p>
                                <p>
                                    Terima kasih sudah mendaftar ke <b>{{ $event->event_title }}</b> 
                                </p>
                                <p>
                                    {{-- 📅 Tanggal: {{ Carbon::parse($event->event_startdate)->locale('Id')->format('D, M d, Y') date('D, M d, Y', strtotime($event->event_startdate)) }} <br> --}}
                                    {{-- 📅 Tanggal: {{ \Carbon\Carbon::parse($event->event_startdate)->locale('id_ID')->format('D, M d, Y') }} <br> --}}
                                    📅 Tanggal: {{ date('l, F d, Y', strtotime($event->event_startdate)) }} <br>
                                    ⏰ Waktu: {{ date('g:i A', strtotime($event->event_startdate)) }} WIB <br>
                                    🔗 Zoom Meeting (link akan dikirim melalui WhatsApp Community EduALL Connect)
                                </p>
                                <p>
                                    Jangan sampai ketinggalan! Gabung ke WhatsApp Community EduALL Connect sekarang untuk mendapatkan link Zoom dan kesempatan Live Q&A setelah sesi dengan speakers.
                                </p>
                                <p>
                                    Sampai jumpa di sesi Masterclass.
                                </p>
                                <p style="text-align: center">
                                    <a href="https://chat.whatsapp.com/KXiCWCpYhpk2phSo3qy5sX" style="cursor:pointer;">
                                        <button style="border-radius: 3px; border: 1px solid #0100D4; color: #FFFFFF; background-color: #0168FF; padding: 15px 25px;">Gabung EduALL Connect</button>
                                    </a>
                                </p>
                            </td>
                            @break

                        @case('Student')
                            <td>
                                <p>Hi {{ $recipient }},</p>
                                <p>
                                    You're all set for <b>{{ $event->event_title }}</b> 
                                </p>
                                <p>
                                    📅 Date: {{ date('l, F d, Y', strtotime($event->event_startdate)) }} <br>
                                    ⏰ Time: {{ date('g:i A', strtotime($event->event_startdate)) }} WIB <br>
                                    🔗 Zoom Meeting (link will be sent via WhatsApp Community)
                                </p>
                                <p>
                                    Don’t miss out! Join our EduALL Connect WhatsApp Community now to receive the Zoom link and exclusive Live Q&A with speakers after the session. See you there.
                                </p>
                                <p style="text-align: center">
                                    <a href="https://chat.whatsapp.com/KXiCWCpYhpk2phSo3qy5sX" style="cursor:pointer;">
                                        <button style="border-radius: 3px; border: 1px solid #0100D4; color: #FFFFFF; background-color: #0168FF; padding: 15px 25px;">Join EduALL Connect</button>
                                    </a>
                                </p>
                            </td>
                            @break

                        @case('Teacher/Counselor')
                            <td>
                                <p>Hi {{ $recipient }},</p>
                                <p>
                                    You're all set for <b>{{ $event->event_title }}</b> 
                                </p>
                                <p>
                                    📅 Date: {{ date('l, F d, Y', strtotime($event->event_startdate)) }} <br>
                                    ⏰ Time: {{ date('g:i A', strtotime($event->event_startdate)) }} WIB <br>
                                    🔗 Zoom Meeting (link will be sent via EduALL Connect WhatsApp Community)
                                </p>
                                <p>
                                    Don’t miss out! Join our EduALL Connect WhatsApp Community now to receive the Zoom link and exclusive Live Q&A with speakers after the session. See you there.
                                </p>
                                <p style="text-align: center">
                                    <a href="https://chat.whatsapp.com/KXiCWCpYhpk2phSo3qy5sX" style="cursor:pointer;">
                                        <button style="border-radius: 3px; border: 1px solid #0100D4; color: #FFFFFF; background-color: #0168FF; padding: 15px 25px;">Join EduALL Connect</button>
                                    </a>
                                </p>
                            </td>
                            @break
                                                
                    @endswitch
                    
                </tr>
            </table>
        </td>
    </tr>

    <!-- END MAIN CONTENT AREA -->
</table>
@endsection