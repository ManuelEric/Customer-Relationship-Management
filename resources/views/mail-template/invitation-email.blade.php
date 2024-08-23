@extends('layout.email-stem')
@section('banner')
    <img loading="lazy"  src="{{asset('img/makerspace/header_invitation_email.jpg')}}" alt="STEM+ Wonderlab" width="2500" style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;" >
@endsection

{{-- @section('header', 'Thanks for Joining Our Event') --}}
@section('content')
<table role="presentation" class="main">

    <!-- START MAIN CONTENT AREA -->
    <tr>
        <td class="wrapper">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <p>Dear Mr./Mrs. {{$recipient}},</p>
                        <p>
                            We are thrilled to share an exclusive invitation to your family as {{$notes}} guests for <b>STEM+ Wonderlab</b>, Indonesia's FIRST Student Makerspace Expo!                        
                        </p>
                        <p style="text-align: center;">
                            <img loading="lazy"  src="https://drive.google.com/uc?id=1FnGPGz4aM0uHC7nRvmLTc9zGX0MVXvGe" width="400" height="600">
                        </p>
                        <p style="text-align: center;margin: 2.5em auto;">
                            <a class="button" href="{{ $param['link'] }}"
                                style="background: #3b6cde; 
                                    text-decoration: none; 
                                    padding: .6em 1.5em;
                                    color: #ffffff; 
                                    border-radius: 2px;
                                    mso-padding-alt:0;
                                    text-underline-color:#156ab3">
                                    <!--[if mso]><i style="letter-spacing: 25px;mso-font-width:-100%;mso-text-raise:20pt">&nbsp;</i><![endif]-->
                                    <span style="mso-text-raise:10pt;font-weight:bold;">I will attend the event</span>
                                    <!--[if mso]><i style="letter-spacing: 25px;mso-font-width:-100%">&nbsp;</i><![endif]-->
                            </a>
                        </p>
                        <p>
                            By registering for this event, enjoy special privileges as our {{$notes}} guests, such as:
                        </p>
                        <ul>
                            <li>
                                <b>Priority access</b> via the dedicated {{$notes}} lane and fast-track entry for your child to explore
                                event booths.
                            </li>
                            <li>
                                Delight in <b>exclusive merchandise</b> courtesy of ALL-in, adding to your special experience.
                            </li>
                            <li>
                                Gain <b>exclusive access</b> to a range of special promotions and premium products offered by our respected sponsors and partners.
                            </li>
                        </ul>
                        <p style="text-align: center; font-size:14px; margin-top:20px">
                            Moreover, we encourage you to take advantage of a unique offer available only to our {{$notes}} guests by <b>bringing along 3 of your friends!</b> 
                        </p>
                        <p style="text-align: center;margin: 2.5em auto;">
                            <a class="button" href="{{ $param['referral_page'] }}"
                                style="background: #3b6cde; 
                                    text-decoration: none; 
                                    padding: .5em 1.5em;
                                    color: #ffffff; 
                                    border-radius: 2px;
                                    mso-padding-alt:0;
                                    text-underline-color:#156ab3">
                                    <!--[if mso]><i style="letter-spacing: 25px;mso-font-width:-100%;mso-text-raise:20pt">&nbsp;</i><![endif]-->
                                    <span style="mso-text-raise:10pt;font-weight:bold;">Claim my link now</span>
                                    <!--[if mso]><i style="letter-spacing: 25px;mso-font-width:-100%">&nbsp;</i><![endif]-->
                            </a>
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
    <img loading="lazy"  src="{{asset('img/makerspace/email_footer.jpg')}}" alt="STEM+ Wonderlab" width="2500" style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;" >
@endsection
