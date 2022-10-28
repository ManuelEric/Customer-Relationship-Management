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
            <tr>
                <td>Score</td>
                <td>
                    <select name="sch_score">
                        <option value="7" {{ isset($school->sch_score) && $school->sch_score == 7 ? "selected" : null }}>Up Market</option>
                        <option value="5" {{ isset($school->sch_score) && $school->sch_score == 5 ? "selected" : null }}>Mid Market</option>
                        <option value="3" {{ isset($school->sch_score) && $school->sch_score == 3 ? "selected" : null }}>Low Market</option>
                    </select>
                </td>
                {{-- <td><input type="number" name="sch_score" value="{{ isset($school->sch_score) ? $school->sch_score: null }}"></td> --}}
            </tr>
        </table>
        
        <input type="submit" value="Save">
    </form>

    @if (isset($school))
    <a href="{{ url('master/school/'.$school->sch_id.'/detail/create') }}">
        <button>Add Contact Person</button>
    </a>
    @endif

    @if (isset($details))
    <table>
        <tr>
            <th>#</th>
            <th>Fullname</th>
            <th>Email</th>
            <th>Grade</th>
            <th>Position</th>
            <th>Phone</th>
            <th colspan="2">Action</th>
        </tr>
        @php
            $no = 1;
        @endphp
        @foreach ($details as $detail)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $detail->schdetail_fullname }}</td>
                <td>{{ $detail->schdetail_email }}</td>
                <td>{{ $detail->schdetail_grade }}</td>
                <td>{{ $detail->schdetail_position }}</td>
                <td>{{ $detail->schdetail_phone }}</td>
                <td><a href="{{ url('master/school/'.$detail->sch_id.'/detail/'.$detail->schdetail_id.'/edit') }}"><button>Edit</button></a></td>
                <td>
                    <form action="{{ url('master/school/'.$detail->sch_id.'/detail/'.$detail->schdetail_id) }}" method="POST">
                        @csrf
                        @method('delete')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    @endif
</body>
</html>