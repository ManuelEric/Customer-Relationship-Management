@extends('layout.email-stem')
@section('banner')
    <img src="{{asset('img/makerspace/header_thank_email.jpg')}}" alt="STEM+ Wonderlab" width="2500" style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;" >
@endsection
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
                            We wanted to extend our gratitude for joining us at {{ $event['eventName'] }} on {{ $event['eventDate_start'] }}. Your presence made a difference, and we're thrilled you could be there with us!
                        </p>

                        @if (array_key_exists('assessment_link', $client))
                        <p>
                            here's the link that you should access at the day of event {{ $event['eventName'] }}
                            <br>
                            <a href="{{ $client['assessment_link'] }}">{{ $client['assessment_link'] }}</a>
                        </p>
                        @endif

                        <p>
                            Once again, thank you for being a part of our community. We can't wait
                            to meet you on the event day!
                        </p>
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
@section('footer')
    <img src="{{asset('img/makerspace/email_footer.jpg')}}" alt="STEM+ Wonderlab" width="2500" style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;" >
@endsection
