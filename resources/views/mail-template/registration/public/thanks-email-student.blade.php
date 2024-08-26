@extends('layout.email')
@section('header', 'Your Registration is Confirmed!')
@section('content')
<h4 style="margin-top:-15px;margin-bottom:1.38em; letter-spacing:-0.02em; text-align:center">
    What's Next?
</h4>
<table role="presentation" class="main">

    <!-- START MAIN CONTENT AREA -->
    <tr>
        <td class="wrapper">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <p>Dear {{ ucfirst($client['name']) }},</p>
                        <p>
                            I hope this email finds you well.<br>
                            <br>
                            This email is to confirm that you have been successfully registered for the <u>{{ $program['name'] }}</u>. We're so excited to have you join us!

                            <br>
                            We'll be sending out more information about the program in the coming weeks, including the syllabus, a list of required materials, and contact information for the program leaders.<br>
                            <br>
                            In the meantime, if you have any questions, please don't hesitate to reach out to us.<br>
                            <br>
                            Thank you for trusting EduALL, we look forward to seeing you soon!
                        </p>
                        <p>
                            Warm regards, <br>
                            Edu ALL
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- END MAIN CONTENT AREA -->
</table>
@endsection