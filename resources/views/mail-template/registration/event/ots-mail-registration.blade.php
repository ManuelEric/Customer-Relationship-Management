@extends('layout.email-event')
@section('banner')
    <img src="{{asset('img/event/EduAll/banner_email_eduall_2024.webp')}}" alt="EduAll Launchpad" width="2500" style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;" >
@endsection
@section('content')
    <style>
        /* custom styles */
        ul li+li {
            margin-top: 15px;
        }
    </style>
    <table role="presentation" class="main">

        <!-- START MAIN CONTENT AREA -->
        <tr>
            <td class="wrapper">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <p>Hi {{ $client['name'] }}, welcome to {{ $event['eventName'] }} </p>

                            @if (array_key_exists('assessment_link', $client))
                            <p>
                                Don't forget to take <b>Initial Assessment Test</b> to start your journey.
                            </p>
                            <p style="text-align: center">
                                <a href="{{ $client['assessment_link'] }}" style="cursor:pointer;">
                                    <button style="border-radius: 3px; border: 1px solid #0100D4; color: #FFFFFF; background-color: #0168FF; padding: 15px 25px;">Take the Test</button>
                                </a>
                            </p>
                            @endif

                            <p>
                                We’re so excited to have you here with us. Here’s what you’ll experience at EduALL Launchpad:
                            </p>

                            <ol>
                                <li>
                                    <p>
                                        Parenting & Educational Talks<br>
                                        University Admissions Success Stories and Collaborative Efforts for Next-Gen Changemakers
                                    </p>
                                </li>
                                <li>
                                    <p>
                                        EduALL Experiential Expo<br>
                                        <ul>
                                            <li>
                                                Interest Exploration<br>
                                                Identify your personality, values, interests, and skills to make informed decisions about your future careers.
                                            </li>
                                            <li>
                                                Portfolio Building<br>
                                                Cultivate knowledge, skills, and understanding of a particular topic through dedicated commitment of time, energy, and resources (personal project, Mini STEM+ Wonderlab, NGOs)
                                            </li>
                                            <li>
                                                Academic Improvement<br>
                                                We equip you to excel in university studies and competitive admissions, from course selection to targeted improvement strategies.
                                            </li>
                                            <li>
                                                Essay Writing<br>
                                                Learn how to master essay prompts for admission success with our guidance on various types, crafting compelling and creative responses for standout applications.
                                            </li>
                                        </ul>
                                    </p>
                                </li>
                            </ol>
                        </td>
                    </tr>
                    
                    <tr>

                    </tr>
                    <tr>
                        <td>
                            <p style="padding-top:20px;">
                                Best Regards,<br>
                                EduALL Team
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
   <img src="{{asset('img/event/EduAll/banner-email-footer.png')}}" alt="EduAll Launchpad" width="2500" style="width:2500px;max-width:100%;height:auto;border:none;text-decoration:none;color:#ffffff;" >
@endsection
