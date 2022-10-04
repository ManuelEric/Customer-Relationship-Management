<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Input Asset</title>
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
    
    <form action="@if(isset($asset)){{ "/asset/".$asset->asset_id }}@else{{ "/asset" }}@endif" method="POST">
        @csrf
        @if (isset($asset))
        @method('put')
        @endif
        Asset Name
        <input type="text" name="asset_name" value="@if(isset($asset->asset_name)){{ $asset->asset_name }}@else{{ old('asset_name') }}@endif"       
        >
        <br>
        Asset Merk/Type
        <input type="text" name="asset_merktype" value="@if (isset($asset->asset_merktype)){{ $asset->asset_merktype }}@else{{ old('asset_merktype') }}@endif"       
        >
        <br>
        Archived Date
        <input type="date" name="asset_dateachieved" value="@if (isset($asset->asset_dateachieved)){{ $asset->asset_dateachieved }}@else{{ old('asset_dateachieved') }}@endif">
        <br>
        Amount
        <input type="number" name="asset_amount" value="@if (isset($asset->asset_amount)){{ $asset->asset_amount }}@else{{ old('asset_amount') }}@endif">
        <br>
        Unit(s)
        <input type="text" name="asset_unit" value="@if(isset($asset->asset_unit)){{ $asset->asset_unit }}@else{{ old('asset_unit') }}@endif">
        <br>
        Condition
        <select name="asset_condition">
            <option value="Good" @if(isset($asset->asset_condition) && ($asset->asset_condition == "Good")){{ "selected" }}@endif>Good</option>
            <option value="Good Enough" @if(isset($asset->asset_condition) && ($asset->asset_condition == "Good Enough")){{ "selected" }}@endif>Good Enough</option>
            <option value="Not Good" @if(isset($asset->asset_condition) && ($asset->asset_condition == "Not Good")){{ "selected" }}@endif>Not Good</option>
        </select>
        <br>
        Notes
        <textarea name="asset_notes" cols="30" rows="10">@if (isset($asset->asset_notes)){{ $asset->asset_notes }}@else{{ old('asset_notes') }}@endif</textarea>
        <br>
        <input type="submit" name="Submit">
    </form>
</body>
</html>