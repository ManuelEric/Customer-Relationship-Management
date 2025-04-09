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
                        <p>Dear Partnership Team,</p>
                        <p>
                            I hope this email finds you well.<br>
                            <br>
                            This email is to confirm that new teacher/counsellor has been successfully registered.</u>.

                            <br>
                            Here are some details regarding the registrant:

                            <br>

                            <table>
                                <tr>
                                    <td>Name</td>
                                    <td>:</td>
                                    <td>{{ $client['name'] }}</td>
                                </tr>
                                <tr>
                                    <td>School</td>
                                    <td>:</td>
                                    <td>{{ $client['school'] }}</td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td>:</td>
                                    <td>{{ $client['phone'] }}</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>:</td>
                                    <td>{{ $client['email'] }}</td>
                                </tr>
                            </table>
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