@extends('layout.email')
@section('header', 'Your Registration is Confirmed')
@section('content')
<table role="presentation" class="main">

    <!-- START MAIN CONTENT AREA -->
    <tr>
        <td class="wrapper">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <p>Dear Mr./ Mrs. {{ ucfirst($client['name']) }},</p>
                        <p>
                            I hope this email finds you well.<br>
                            <br>
                            {{-- I'm writing to confirm that your child, <b>{{ ucwords($client['children_details']['name']) }}</b>, has been successfully registered for the <u>{{ $program['name'] }}</u> program. We're so excited to have them join us!<br> --}}
                            I'm writing to confirm that your child, <b>[Child]</b>, has been successfully registered for the <u>{{ $program['name'] }}</u> program. We're so excited to have them join us!<br>
                            <br>
                            We'll be sending out more information about the program in the coming weeks, including the syllabus, a list of required materials, and contact information for the program leaders.<br>
                            <br>
                            In the meantime, if you have any questions, please don't hesitate to reach out to us.<br>
                            <br>
                            Thank you for registering your child for the <u>{{ $program['name'] }}</u> program. We look forward to seeing them soon!    
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