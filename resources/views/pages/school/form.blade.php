<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>School</title>
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
    
    <form action="{{ url(isset($school) ? 'master/school/' . $school->sch_id : 'master/school') }}"
        method="POST">
        @csrf
        @if (isset($school))
            @method('put')
        @endif

        <table>
            <tr>
                <td>School Name : </td>
                <td><input type="text" name="sch_name" value="{{ isset($school->sch_name) ? $school->sch_name : null }}"></td>
            </tr>
            <tr>
                <td>Type</td>
                <td>
                    <select name="sch_type">
                        <option {{ !isset($school->sch_type) ? "selected" : null }}>Please select one</option>
                        <option value="National" {{ isset($school->sch_type) && ($school->sch_type == "National") ? "selected" : null }}>National</option>
                        <option value="International" {{ isset($school->sch_type) && ($school->sch_type == "International") ? "selected" : null }}>International</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Curriculum</td>
                <td>
                    <select name="sch_curriculum">
                        <option {{ !isset($school->sch_curriculum) ? "selected" : null }}>Please select one</option>
                        @foreach ($curriculums as $curriculum)
                        <option value="{{ $curriculum->name }}" 
                                {{ isset($school->sch_curriculum) && $school->sch_curriculum == $curriculum->name ? "selected" : null  }}
                            >{{ $curriculum->name }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>Instagram</td>
                <td><input type="text" name="sch_insta" value="{{ isset($school->sch_insta) ? $school->sch_insta : null }}"></td>
            </tr>
            <tr>
                <td>School Mail</td>
                <td><input type="text" name="sch_mail" value="{{ isset($school->sch_mail) ? $school->sch_mail : null }}"></td>
            </tr>
            <tr>
                <td>Telephone</td>
                <td><input type="text" name="sch_phone" value="{{ isset($school->sch_phone) ? $school->sch_phone : null }}"></td>
            </tr>
            <tr>
                <td>City</td>
                <td><input type="text" name="sch_city" value="{{ isset($school->sch_city) ? $school->sch_city : null }}"></td>
            </tr>
            <tr>
                <td>Location</td>
                <td><input type="text" name="sch_location" value="{{ isset($school->sch_location) ? $school->sch_location : null }}"></td>
            </tr>
        </table>

        <hr>

        {{-- <div>
            <div style="float:left"><h1>Contact Person</h1></div>
            <div style="float:right"><button type="button">Add Contact Person</button></div>
        </div>
        <div style="clear:both"></div>

        <table>
            <tr>
                <td>Fullname</td>
                <td><input type="text" name="schdetail_name[]"></td>
            </tr>
            <tr>
                <td>E-mail</td>
                <td><input type="email" name="schdetail_mail[]"></td>
            </tr>
            <tr>
                <td>Phone Number</td>
                <td><input type="text" name="schdetail_phone[]"></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    <select name="schdetail_position[]">
                        <option>Please select one</option>
                        <option value="Principal">Principal</option>
                        <option value="Counselor">Counselor</option>
                        <option value="Teacher">Teacher</option>
                        <option value="Marketing">Marketing</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>School Grade</td>
                <td>
                    <select name="schdetail_grade[]">
                        <option>Please select one</option>
                        <option value="Middle School">Middle School</option>
                        <option value="High School">High School</option>
                        <option value="Middle School & High School">Middle School & High School</option>
                    </select>   
                </td>
            </tr>
        </table> --}}
        
        <input type="submit" value="Save">
    </form>
</body>
</html>