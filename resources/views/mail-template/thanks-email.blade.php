@extends('layout.email')
@section('header', 'Thanks for Joining Our Event')
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
                            We wanted to extend our gratitude for joining us at {{ $event['eventName'] }} on {{ $event['eventDate'] }}. Your presence made a difference, and we're thrilled you could be there with us!
                        </p>
                        <p>
                            Once again, thank you for being a part of our community. We can't wait
                            to meet you on the event day!
                        </p>
                        @if(isset($notes) && $notes == 'VVIP')
                            <p>
                                <a href="{{$referral_link}}">Your Referral Link</a>
                            </p>
                        @endif
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