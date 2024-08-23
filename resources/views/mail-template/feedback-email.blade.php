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
                            @switch($role)
                                @case('parent')
                                    <p>Dear Mr./Mrs. {{ $recipient }},</p>
                                    <p>
                                        Terima kasih atas kehadirannya dalam acara <b>STEM+ Wonderlab</b>, Indonesia's FIRST Student
                                        Makerspace Expo! Kami harap Anda merasa terbantu oleh acara ini untuk mendorong dan
                                        mendukung anak Anda masuk ke universitas top dunia yang menjadi impiannya.
                                    </p>

                                    <p>
                                        Untuk menghadirkan acara atau program yang lebih baik, maukah anda membantu kami dengan
                                        mengisi feedback form di bawah ini?
                                    </p>

                                    <p style="text-align: center">
                                        <a href="https://bit.ly/FEEDBACK-STEMWONDERLAB" style="text-decoration: none;">
                                            <button
                                                style="border: 1px solid rgb(222, 222, 12); padding: 15px 50px 15px 50px; background-color:#F0833E; border-radius: 7px; color: #FFFFFF; cursor: pointer;">STEM+
                                                Wonderlab Feedback Form</button>
                                        </a>
                                    </p>

                                    <p>
                                        Ingin mengikutsertakan anak anda dalam kegiatan eksplorasi karir atau persiapan pendaftaran
                                        universitas lainnya? Kami punya banyak sekali kegiatan menarik khusus untuk anak anda!
                                    </p>
                                    <p style="text-align: center;">
                                        <a href="https://all-inedu.com/id-en/resources/upcoming-events"
                                            style="text-decoration: none;">
                                            <button
                                                style="border: 1px solid rgb(222, 222, 12); padding: 15px 50px 15px 50px; background-color:#F0833E; border-radius: 7px; color: #FFFFFF; cursor: pointer;">Kegiatan
                                                ALL-in Lainnya</button>
                                        </a>
                                    </p>
                                    <p>
                                        Sekali lagi, terima kasih sudah mempercayai kami, dan sampai jumpa di kegiatan selanjutnya!
                                    </p>
                                @break

                                @case('student')
                                @case('mentee')

                                @case('teacher/counselor')
                                    @if ($role == 'teacher/counselor')
                                        <p>Dear Mr./Mrs. {{ $recipient }},</p>
                                    @else
                                        <p>Dear {{ $recipient }},</p>
                                    @endif
                                    <p>
                                        Thank you for joining <b>STEM+ Wonderlab</b>, Indonesia's FIRST Student Makerspace Expo this
                                        year! We hope you enjoyed it as much as we did!
                                    </p>

                                    <p>
                                        To bring you even better events in the future, would you mind telling us what you think
                                        about this event, what you liked, and things you want us to improve?
                                    </p>

                                    <p style="text-align: center">
                                        <a href="https://bit.ly/FEEDBACK-STEMWONDERLAB" style="text-decoration: none;">
                                            <button
                                                style="border: 1px solid rgb(222, 222, 12); padding: 15px 50px 15px 50px; background-color:#F0833E; border-radius: 7px; color: #FFFFFF; cursor: pointer;">STEM+
                                                Wonderlab Feedback Form</button>
                                        </a>
                                    </p>

                                    <p style="text-align: center">
                                        @if ($role == 'teacher/counselor')
                                            Are you looking for other events to explore your interests or prepare for your students'
                                            university application? We have a bunch of exciting events for you to join!
                                        @else
                                            Are you looking for other events to explore your interests or prepare for your
                                            university application? We have a bunch of exciting events for you to join!
                                        @endif
                                        <br>Check out our events here:
                                    </p>

                                    <p style="text-align: center">
                                        <a href="https://all-inedu.com/id-en/resources/upcoming-events"
                                            style="text-decoration: none;">
                                            <button
                                                style="border: 1px solid rgb(222, 222, 12); padding: 15px 50px 15px 50px; background-color:#F0833E; border-radius: 7px; color: #FFFFFF; cursor: pointer;">Upcoming
                                                ALL-in Events</button>
                                        </a>
                                    </p>
                                    <p style="text-align: center">
                                        Once again, thank you for joining us, and we hope to see you at our next events!
                                    </p>
                                @break
                            @endswitch
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
