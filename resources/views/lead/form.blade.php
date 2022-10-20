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

    <form
        action="@if (isset($lead)) {{ '/master/lead/' . $lead->lead_id }}@else{{ '/master/lead' }} @endif"
        method="POST">
        @csrf
        @if (isset($lead))
            @method('put')
        @endif

        <input type="hidden" name="id" value="{{ $lead->id }}">

        <div id="main_lead">
            Lead name: <input type="text" name="main_lead"
                value="@if (isset($lead->main_lead) && $lead->main_lead != 'KOL') {{ $lead->main_lead }}@else{{ old('main_lead') }} @endif">
            <br>
        </div>

        <div id="sub_lead" style="display: none">
            Lead name: <input type="text" name="sub_lead"
                value="@if (isset($lead->sub_lead)) {{ $lead->sub_lead }}@else{{ old('sub_lead') }} @endif">
            <br>
        </div>

        Score: <input type="number" name="score" value="{{ isset($lead->score) ? $lead->score : old('score') }}">
        <br>
        KOL <input type="checkbox" name="kol" onchange="changeKOL()"
            @if (isset($lead->main_lead) && $lead->main_lead == 'KOL') {{ 'checked' }} @endif>
        <br>
        <input type="submit" value="Submit">

    </form>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"
        integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            changeKOL()
        })

        function changeKOL() {
            var checked = $("input[name=kol]").prop('checked');

            if (checked) {
                $("#main_lead").hide();
                $("#sub_lead").show();
            } else {
                $("#main_lead").show();
                $("#sub_lead").hide();
            }
        }
    </script>
</body>

</html>
