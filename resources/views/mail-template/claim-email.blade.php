@extends('layout.email')
{{-- @section('header', 'Thanks for Joining') --}}
@section('content')
    <table role="presentation" class="main">

        <!-- START MAIN CONTENT AREA -->
        <tr>
            <td class="wrapper">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <p>Dear Mr./Mrs. {{ $client['name'] }},</p>
                            <p>
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Facilis consequatur provident quas hic nam exercitationem accusamus illum in, nisi soluta!
                            </p>

                            <p style="text-align: center;">
                               Lorem ipsum, dolor sit amet consectetur adipisicing elit. Itaque, temporibus.
                           
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
