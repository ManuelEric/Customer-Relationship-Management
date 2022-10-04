<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Input Volunteer</title>
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

    <form action="@if(isset($volunteer)){{ "/volunteer/".$volunteer->volunt_id }}@else{{ "/volunteer" }}@endif" method="POST">
        @csrf
        @if (isset($volunteer))
        @method('put')
        @endif
        First Name
        <input type="text" name="volunt_firstname" value="@if(isset($volunteer->volunt_firstname)){{ $volunteer->volunt_firstname }}@else{{ old('volunt_firstname') }}@endif"       
         {{ old('volunt_firstname') }}>
        <br>
        Last Name
        <input type="text" name="volunt_lastname" value="@if(isset($volunteer->volunt_lastname)){{ $volunteer->volunt_lastname }}@else{{ old('volunt_lastname') }}@endif"       
        >
        <br>
        Email
        <input type="email" name="volunt_mail" value="@if(isset($volunteer->volunt_mail)){{ $volunteer->volunt_mail }}@else{{ old('volunt_mail') }}@endif">
        <br>
        Address
        <textarea name="volunt_address" cols="30" rows="10">@if (isset($volunteer->volunt_address)){{ $volunteer->volunt_address }}@else{{ old('volunt_address') }}@endif</textarea>
        <br>
        Phone
        <input type="text" name="volunt_phone" value="@if (isset($volunteer->volunt_phone)){{ $volunteer->volunt_phone }}@else{{ old('volunt_phone') }}@endif">
        <br>
        Graduated From
        <input type="text" name="volunt_graduatedfr" value="@if(isset($volunteer->volunt_graduatedfr)){{ $volunteer->volunt_graduatedfr }}@else{{ old('volunt_graduatedfr') }}@endif">
        <br>
        Major
        <input type="text" name="volunt_major" value="@if(isset($volunteer->volunt_major)){{ $volunteer->volunt_major }}@else{{ old('volunt_major') }}@endif">
        <br>
        Position
        <input type="text" name="volunt_position" value="@if(isset($volunteer->volunt_position)){{ $volunteer->volunt_position }}@else{{ old('volunt_position') }}@endif">
        <br>
        <input type="submit" name="Submit">
    </form>
</body>
</html>