@extends('layout.email')
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
                            We are thrilled to share an exclusive invitation to your family as VIP guests for {{ $event['eventName'] }}, Indonesia's FIRST student makerspace expo!                        
                        </p>
                        <p>
                           [flyer]
                        </p>
                        <p>
                           [flyer]
                        </p>
                        <p>
                           [flyer]
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
                            By registering for this event, enjoy special privileges as our VIP guests, such as:
                        </p>
                        <ul>
                            <li>
                                Priority access via the dedicated VIP lane and fast-track entry for your child to explore
                                event booths.
                            </li>
                            <li>
                                Delight in exclusive merchandise courtesy of ALL-in, adding to your special experience.
                            </li>
                            <li>
                                Gain exclusive access to a range of special promotions and premium products offered by our respected sponsors and partners.
                            </li>
                        </ul>
                        <p style="text-align: center; font-size:14px; margin-top:20px">
                            Moreover, we encourage you to take advantage of a unique offer available only to our VIP guests by <b>bringing along 3 of your friends!</b> 
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