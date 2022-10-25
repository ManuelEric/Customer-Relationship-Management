<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h1 style="float:left">CONTACT PERSON</h1>
    @if (!isset($schoolDetail))
    <div style="float:right"><button onclick="addCP()">Add Contact Person</button></div>
    @endif
    <div style="clear:both"></div>

    <form id="storeCP" action="{{ url(isset($schoolDetail) ? 'master/school/'.$school_id.'/detail/' . $schoolDetail->schdetail_id : 'master/school/detail') }}"
        method="POST">
        @csrf
        @if (isset($schoolDetail))
            @method('put')
            <input type="hidden" readonly name="schdetail_id" value="{{ $schoolDetail->schdetail_id }}">
        @endif

        <input type="hidden" readonly name="sch_id" value="{{ $school_id }}">

        <table>
            <tr>
                <td>Fullname</td>
                <td><input type="text" name="schdetail_name[]" value="{{ isset($schoolDetail->schdetail_fullname) ? $schoolDetail->schdetail_fullname : null }}"></td>
            </tr>
            <tr>
                <td>E-mail</td>
                <td><input type="email" name="schdetail_mail[]" value="{{ isset($schoolDetail->schdetail_email) ? $schoolDetail->schdetail_email : null }}"></td>
            </tr>
            <tr>
                <td>Phone Number</td>
                <td><input type="text" name="schdetail_phone[]" value="{{ isset($schoolDetail->schdetail_phone) ? $schoolDetail->schdetail_phone : null }}"></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    <select name="schdetail_position[]">
                        <option {{ !isset($schoolDetail->schdetail_position) ? "selected" : null }}>Please select one</option>
                        <option value="Principal" {{ isset($schoolDetail->schdetail_position) && $schoolDetail->schdetail_position == "Principal" ? "selected" : null }}>Principal</option>
                        <option value="Counselor" {{ isset($schoolDetail->schdetail_position) && $schoolDetail->schdetail_position == "Counselor" ? "selected" : null }}>Counselor</option>
                        <option value="Teacher" {{ isset($schoolDetail->schdetail_position) && $schoolDetail->schdetail_position == "Teacher" ? "selected" : null }}>Teacher</option>
                        <option value="Marketing" {{ isset($schoolDetail->schdetail_position) && $schoolDetail->schdetail_position == "Marketing" ? "selected" : null }}>Marketing</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>School Grade</td>
                <td>
                    <select name="schdetail_grade[]">
                        <option {{ !isset($schoolDetail->schdetail_grade) ? "selected" : null }}>Please select one</option>
                        <option value="Middle School" {{ isset($schoolDetail->schdetail_grade) && $schoolDetail->schdetail_grade == "Middle School" ? "selected" : null }}>Middle School</option>
                        <option value="High School" {{ isset($schoolDetail->schdetail_grade) && $schoolDetail->schdetail_grade == "High School" ? "selected" : null }}>High School</option>
                        <option value="Middle School & High School" {{ isset($schoolDetail->schdetail_grade) && $schoolDetail->schdetail_grade == "Middle School & High School" ? "selected" : null }}>Middle School & High School</option>
                    </select>   
                </td>
            </tr>
        </table>
        
    </form>

    <input type="submit" value="Save Contact" form="storeCP">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" async defer>
        function addCP()
        {
            $("form").append("<table>" +
                                "<tr>" +
                                    "<td>Fullname</td>" +
                                    '<td><input type="text" name="schdetail_name[]"></td>' +
                                "</tr>" +
                                "<tr>" +
                                    "<td>E-mail</td>" +
                                    '<td><input type="email" name="schdetail_mail[]"></td>' +
                                "</tr>" +
                                "<tr>" +
                                    "<td>Phone Number</td>" +
                                    '<td><input type="text" name="schdetail_phone[]"></td>' +
                                "</tr>" +
                                "<tr>" +
                                    "<td>Status</td>" +
                                    "<td>" +
                                        '<select name="schdetail_position[]">' +
                                            "<option>Please select one</option>" +
                                            '<option value="Principal">Principal</option>' +
                                            '<option value="Counselor">Counselor</option>' +
                                            '<option value="Teacher">Teacher</option>' +
                                            '<option value="Marketing">Marketing</option>' +
                                        "</select>" +
                                    "</td>" +
                                "</tr>" +
                                "<tr>" +
                                    "<td>School Grade</td>" +
                                    "<td>" +
                                        '<select name="schdetail_grade[]">' +
                                            "<option>Please select one</option>" +
                                            '<option value="Middle School">Middle School</option>' +
                                            '<option value="High School">High School</option>' +
                                            '<option value="Middle School & High School">Middle School & High School</option>' +
                                        "</select>" +
                                    "</td>" +
                                "</tr>" +
                            "</table>");
        }
    </script>
</body>
</html>