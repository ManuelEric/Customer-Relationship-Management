<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Input University</title>
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

    <form action="@if(isset($university)){{ "/university/".$university->univ_id }}@else{{ "/university" }}@endif" method="POST">
        @csrf
        @if (isset($university))
        @method('put')
        @endif
        University Name
        <input type="text" name="univ_name" value="@if(isset($university->univ_name)){{ $university->univ_name }}@else{{ old('univ_name') }}@endif"       
        >
        <br>
        Country
        <select name="univ_country">
            @foreach ($type as $item)
            <option value="{{ $item->name }}" 
                @if ((isset($vendor->vendor_type) && $item->name == $vendor->vendor_type) || (old('vendor_type') == $item->name))
                {{ "selected" }}
                @endif
                >{{ $item->name }}</option>
            @endforeach
        </select>
        <br>
        Address
        <input type="text" name="univ_address" value="@if(isset($university->univ_address)){{ $university->univ_address }}@else{{ old('univ_address') }}@endif">
        <input type="submit" name="Submit">
    </form>
</body>
</html>