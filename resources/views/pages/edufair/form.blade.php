<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        fieldset {
            border: 1px solid #ccc
        }
    </style>
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
    <form action="{{ isset($edufair) ? route('edufair.update', ['edufair' => $edufair->id]) :route('edufair.store') }}" method="POST">
        @csrf
        @if (isset($edufair))
            @method('put')
            <input type="hidden" name="id" value="{{ $edufair->id }}">
        @endif
        <fieldset>
            <legend>Organizer</legend>
            <input type="radio" name="organizer" value="school" {{ isset($edufair) ? (isset($edufair->sch_id) && $edufair->sch_id != NULL ? "checked" : null) : "checked" }}>School
            <input type="radio" name="organizer" value="corporate" {{ isset($edufair->corp_id) && $edufair->corp_id != NULL ? "checked" : null }}>Corporate
        </fieldset>

        <fieldset id="schoolList">
            <legend>School</legend>
            <select name="sch_id">
                <option value="">Select school</option>
                @foreach ($schools as $school)
                    <option value="{{ $school->sch_id }}" {{ isset($edufair->sch_id) && $edufair->sch_id == $school->sch_id ? "selected" : null  }}>{{ $school->sch_name }}</option>
                @endforeach
            </select>
        </fieldset>
        
        <fieldset id="corporateList" style="display:none">
            <legend>Corporate</legend>
            <select name="corp_id">
                <option value="">Select corporate</option>
                @foreach ($corporates as $corporate)
                    <option value="{{ $corporate->corp_id }}" {{ isset($edufair->corp_id) && $edufair->corp_id == $corporate->corp_id ? "selected" : null  }}>{{ $corporate->corp_name }}</option>
                @endforeach
            </select>
        </fieldset>

        <fieldset>
            <legend>Location</legend>
            <textarea name="location" cols="30" rows="10">{{ isset($edufair->location) ? $edufair->location :null }}</textarea>
        </fieldset>

        <fieldset>
            <legend>PIC from ALL-in</legend>
            <select name="intr_pic">
                @foreach ($internal_pic as $pic)
                    <option value="{{ $pic->id }}" {{ isset($edufair->intr_pic) && $edufair->intr_pic == $pic->id ? "selected" : null }}>{{ $pic->first_name.' '.$pic->last_name }}</option>
                @endforeach
            </select>
        </fieldset>

        <div>
            <h4>External PIC Profile</h4>
            <fieldset>
                <legend>Name</legend>
                <input type="text" name="ext_pic_name" value="{{ isset($edufair->ext_pic_name) ? $edufair->ext_pic_name : null }}">
            </fieldset>

            <fieldset>
                <legend>Email</legend>
                <input type="email" name="ext_pic_mail" value="{{ isset($edufair->ext_pic_mail) ? $edufair->ext_pic_mail : null }}">
            </fieldset>

            <fieldset>
                <legend>Phone</legend>
                <input type="text" name="ext_pic_phone" value="{{ isset($edufair->ext_pic_phone) ? $edufair->ext_pic_phone : null }}">
            </fieldset>
        </div>

        <fieldset>
            <legend>First Discussion</legend>
            <input type="date" name="first_discussion_date" value="{{ isset($edufair->first_discussion_date) ? $edufair->first_discussion_date : null }}">
        </fieldset>

        <fieldset>
            <legend>Last Discussion</legend>
            <input type="date" name="last_discussion_date" value="{{ isset($edufair->last_discussion_date) ? $edufair->last_discussion_date : null }}">
        </fieldset>

        <fieldset>
            <legend>Event Start</legend>
            <input type="date" name="event_start" value="{{ isset($edufair->event_start) ? $edufair->event_start : null }}">
        </fieldset>

        <fieldset>
            <legend>Event End</legend>
            <input type="date" name="event_end" value="{{ isset($edufair->event_end) ? $edufair->event_end : null }}">
        </fieldset>

        <fieldset>
            <legend>Status</legend>
            <select name="status">
                <option value="1" {{ isset($edufair->status) && $edufair->status == 1 ? "selected" : null }}>Active</option>
                <option value="0" {{ isset($edufair->status) && $edufair->status == 0 ? "selected" : null }}>Inactive</option>
            </select>
        </fieldset>

        <fieldset>
            <legend>Notes</legend>
            <textarea name="notes" cols="30" rows="10">{{ isset($edufair->notes) ? $edufair->notes : null }}</textarea>
        </fieldset>

        <br>
        <button type="submit">Submit</button>
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script async defer>
        $(document).ready(function() {
            $('input[name=organizer]').on('change', function() {
                change_organizer($(this).val())  
            })
        })

        function change_organizer(val)
        {
            if (val == 'school') 
            {
                $("#schoolList").show()
                $("#corporateList").hide()
            } else {
                $("#schoolList").hide()
                $("#corporateList").show()
            }
        }
    </script>
    @if (isset($edufair))
    <script async defer>
        var organizer = "{{ isset($edufair->sch_id) && $edufair->sch_id != NULL ? 'school' : 'corporate' }}"
        change_organizer(organizer)
    </script>
    @endif
</body>
</html>