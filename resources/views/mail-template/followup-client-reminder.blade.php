@extends('layout.email')
@section('header', 'Today Schedule')
@section('content')
    <p style="margin:0;">Dear Mr./Mrs. {{ ucwords($name) }},</p>
    <p>
        I hope this email finds you well. I wanted to remind you about following up with our valued client,
    </p>
    <div style="background-color:bisque; padding: 20px;">
        <p>
            <table>
                @php
                    $row = count($schedules);
                    $i = 1;
                @endphp
                @foreach ($schedules as $data) 
                    <tr>
                        <td><b>Name</b></td>
                        <td><b>:</b></td>
                        <td>{{ $data['client']->full_name }}</td>
                    </tr>
                    <tr>
                        <td><b>Date</b></td>
                        <td><b>:</b></td>
                        <td>{{ date('d F Y', strtotime($data['followup']->followup_date)) }}</td>
                    </tr>
                    <tr>
                        <td><b>Time</b></td>
                        <td><b>:</b></td>
                        <td>{{ date('H:i', strtotime($data['followup']->followup_date)) }}</td>
                    </tr>
                    <tr valign="top">
                        <td><b>Notes</b></td>
                        <td><b>:</b></td>
                        <td><i>{{ strip_tags($data['followup']->notes) }}</i></td>
                    </tr>
                    @if ($i < $row)
                    <tr>
                        <td colspan="3">&nbsp;
                            <hr>
                        </td>
                    </tr>
                    @endif
                    @php
                        $i++
                    @endphp
                @endforeach
            </table>
        </p>
    </div>
    <p>
        Thank you for your attention to this matter, and I trust that you will handle this follow-up with your usual professionalism and expertise.
    </p>
    <p>
        Thank you <br>
        Regards
    </p>
@endsection
