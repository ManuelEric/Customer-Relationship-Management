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
                                @case('Parent')
                                    <p>Dear Mr./Mrs. {{ $recipient }},</p>
                                    <p>
                                        Terima kasih sudah mendaftar ke event <b>STEM+ Wonderlab.</b>
                                    </p>

                                    <p>
                                        Sampai jumpa <b>{{ date('Y-m-d') == '2023-11-10' ? 'BESOK!' : 'NANTI!' }}</b>
                                    </p>

                                    <p>
                                        <b>STEM+ Wonderlab
                                            <br>Indonesia's FIRST Student Makerspace Expo</b>
                                        <br>📍{{ strip_tags($event['eventLocation']) }}
                                        <br>📅{{ $event['eventDate_start'] }}
                                        <br> {{ $event['eventTime_start'] }} WIB
                                        <br> Maps: <a
                                            href="https://maps.app.goo.gl/Z2TZTU9SviH1TvkW8">https://maps.app.goo.gl/Z2TZTU9SviH1TvkW8</a>
                                    </p>

                                    <p style="text-align: center">
                                        Add to Calendar
                                        <br>
                                        <a href="https://calendar.google.com/calendar/u/0/r/eventedit?dates=20231111T050000Z/20231111T110000Z&text=STEM%2B+Wonderlab&details=Indonesia%E2%80%99s+FIRST+Student+Makerspace+Expo&location=Ciputra+Artpreneur,+Jakarta&pli=1"
                                            style="text-decoration: none;">
                                            <img loading="lazy"  src="{{ asset('img/icon/google.webp') }}" width="25" height="25"
                                                alt="">
                                        </a>
                                        &nbsp; &nbsp;
                                        <a href="https://groot.mailerlite.com/events/download?dates=20231111T050000Z/20231111T110000Z&text=STEM%2B%20Wonderlab&details=Indonesia%E2%80%99s%20FIRST%20Student%20Makerspace%20Expo&location=Ciputra%20Artpreneur%2C%20Jakarta"
                                            style="text-decoration: none;">
                                            <img loading="lazy"  src="{{ asset('img/icon/outlook.webp') }}" width="25" height="25"
                                                alt="">
                                        </a>
                                    </p>

                                    <p>
                                        Pintu masuk dapat diakses melalui:
                                    <ol>
                                        <li>Drop off lobby Ciputra Artpreneur lantai 11 (masuk melalui 'Kuningan Entrance' di
                                            belakang kompleks Mall)</li>
                                        <li>Gunakan lift Satrio atau Lift Avenue ke lantai 11</li>
                                    </ol>
                                    </p>

                                    <p style="text-align:center; ">
                                        <img loading="lazy"  src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $qr }}"
                                            alt="">
                                        <br>
                                        <b>
                                            <br>HARAP DIPERHATIKAN
                                            <br>Simpan QR code yang telah tertera & verifikasi di meja registrasi STEM+ Wonderlab
                                        </b>
                                        <br>
                                        <br>
                                        SEE YOU THERE!
                                    </p>
                                @break

                                @case('Student')
                                @case('Mentee')

                                @case('Teacher/Counselor')
                                    @if ($role == 'Teacher/Counselor')
                                        <p>Dear Mr./Mrs. {{ $recipient }},</p>
                                    @else
                                        <p>Dear {{ $recipient }},</p>
                                    @endif
                                    <p>
                                        Thank you for registering to <b>STEM+ Wonderlab</b> event.
                                    </p>
                                    <p>
                                        See you <b>{{ date('Y-m-d') == '2023-11-10' ? 'TOMORROW!' : 'SOON!' }}</b>
                                    </p>

                                    <p>
                                        <b>STEM+ Wonderlab
                                            <br>Indonesia's FIRST Student Makerspace Expo</b>
                                        <br>📍{{ strip_tags($event['eventLocation']) }}
                                        <br>📅{{ $event['eventDate_start'] }}
                                        <br> {{ $event['eventTime_start'] }} WIB
                                        <br> Maps: <a
                                            href="https://maps.app.goo.gl/Z2TZTU9SviH1TvkW8">https://maps.app.goo.gl/Z2TZTU9SviH1TvkW8</a>
                                    </p>

                                    <p style="text-align: center">
                                        Add to Calendar
                                        <br>
                                        <a href="https://calendar.google.com/calendar/u/0/r/eventedit?dates=20231111T050000Z/20231111T110000Z&text=STEM%2B+Wonderlab&details=Indonesia%E2%80%99s+FIRST+Student+Makerspace+Expo&location=Ciputra+Artpreneur,+Jakarta&pli=1"
                                            style="text-decoration: none;">
                                            <img loading="lazy"  src="{{ asset('img/icon/google.webp') }}" width="25" height="25"
                                                alt="">
                                        </a>
                                        &nbsp; &nbsp;
                                        <a href="https://groot.mailerlite.com/events/download?dates=20231111T050000Z/20231111T110000Z&text=STEM%2B%20Wonderlab&details=Indonesia%E2%80%99s%20FIRST%20Student%20Makerspace%20Expo&location=Ciputra%20Artpreneur%2C%20Jakarta"
                                            style="text-decoration: none;">
                                            <img loading="lazy"  src="{{ asset('img/icon/outlook.webp') }}" width="25" height="25"
                                                alt="">
                                        </a>
                                    </p>

                                    <p>
                                        The entrance can be accessed via:
                                    <ol>
                                        <li>Drop off Ciputra Artpreneur lobby 11th floor (enter via 'Kuningan Entrance' at the back
                                            of the Mall complex)</li>
                                        <li>Use the Satrio lift or Avenue lift to the 11th floor</li>
                                    </ol>
                                    </p>

                                    <p style="text-align:center;">
                                        <img loading="lazy"  src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $qr }}"
                                            alt="">
                                        <br>
                                        <b>
                                            <br>PLEASE NOTE:
                                            <br>Save this QR code & verify it at the STEM+ Wonderlab registration desk
                                        </b>
                                        <br>
                                        <br>
                                        SEE YOU THERE!
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
