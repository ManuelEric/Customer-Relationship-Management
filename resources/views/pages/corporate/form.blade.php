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
    <form action="{{ isset($corporate) ? route('corporate.update', ['corporate' => $corporate->corp_id]) : route('corporate.store') }}" method="POST">
        @csrf
        @if (isset($corporate))
            @method('PUT')
            <input type="hidden" name="corp_id" value="{{ $corporate->corp_id }}">
        @endif
        <fieldset>
            <legend>Corporate Name</legend>
            <input type="text" name="corp_name" value="{{ isset($corporate->corp_name) ? $corporate->corp_name : null }}">
        </fieldset>

        <fieldset>
            <legend>Industry</legend>
            <input type="text" name="corp_industry" value="{{ isset($corporate->corp_industry) ? $corporate->corp_industry : null }}">
        </fieldset>

        <fieldset>
            <legend>Email</legend>
            <input type="email" name="corp_mail" value="{{ isset($corporate->corp_mail) ? $corporate->corp_mail : null }}">
        </fieldset>

        <fieldset>
            <legend>Contact Number</legend>
            <input type="text" name="corp_phone" value="{{ isset($corporate->corp_phone) ? $corporate->corp_phone : null }}">
        </fieldset>

        <fieldset>
            <legend>Instagram</legend>
            <input type="text" name="corp_insta" value="{{ isset($corporate->corp_insta) ? $corporate->corp_insta : null }}">
        </fieldset>

        <fieldset>
            <legend>Website</legend>
            <input type="text" name="corp_site" placeholder="https://xxxxxx.xxxx" value="{{ isset($corporate->corp_site) ? $corporate->corp_site : null }}">
        </fieldset>

        <fieldset>
            <legend>Region</legend>
            <input type="text" name="corp_region" value="{{ isset($corporate->corp_region) ? $corporate->corp_region : null }}">
        </fieldset>

        <fieldset>
            <legend>Address</legend>
            <textarea name="corp_address" cols="30" rows="10">{{ isset($corporate->corp_address) ? $corporate->corp_address : null }}</textarea>
        </fieldset>

        <fieldset>
            <legend>Note</legend>
            <textarea name="corp_note" cols="30" rows="10">{{ isset($corporate->corp_note) ? $corporate->corp_note : null }}</textarea>
        </fieldset>

        <br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>