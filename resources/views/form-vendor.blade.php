<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Input Vendor</title>
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

    <form action="@if(isset($vendor)){{ "/vendor/".$vendor->vendor_id }}@else{{ "/vendor" }}@endif" method="POST">
        @csrf
        @if (isset($vendor))
        @method('put')
        @endif
        Nama Vendor
        <input type="text" name="vendor_name" value="@if(isset($vendor->vendor_name)){{ $vendor->vendor_name }}@else{{ old('vendor_name') }}@endif"       
        >
        <br>
        Alamat
        <textarea name="vendor_address" cols="30" rows="10">@if (isset($vendor->vendor_address)){{ $vendor->vendor_address }}@else{{ old('vendor_address') }}@endif</textarea>
        <br>
        Phone
        <input type="text" name="vendor_phone" value="@if (isset($vendor->vendor_phone)){{ $vendor->vendor_phone }}@else{{ old('vendor_phone') }}@endif">
        <br>
        Type
        <select name="vendor_type">
            @foreach ($type as $item)
            <option value="{{ $item->name }}" 
                @if ((isset($vendor->vendor_type) && $item->name == $vendor->vendor_type) || (old('vendor_type') == $item->name))
                {{ "selected" }}
                @endif
                >{{ $item->name }}</option>
            @endforeach
        </select>
        <br>
        Material
        <input type="text" name="vendor_material" value="@if(isset($vendor->vendor_material)){{ $vendor->vendor_material }}@else{{ old('vendor_material') }}@endif">
        <br>
        Size
        <input type="text" name="vendor_size" value="@if(isset($vendor->vendor_size)){{ $vendor->vendor_size }}@else{{ old('vendor_size') }}@endif">
        <br>
        Processing Time
        <input type="text" name="vendor_processingtime" value="@if(isset($vendor->vendor_processingtime)){{ $vendor->vendor_processingtime }}@else{{ old('vendor_processingtime') }}@endif">
        <br>
        Price
        <input type="text" name="vendor_unitprice" value="@if(isset($vendor->vendor_unitprice)){{ $vendor->vendor_unitprice }}@else{{ old('vendor_unitprice') }}@endif">
        <br>
        Notes
        <input type="text" name="vendor_notes" value="@if(isset($vendor->vendor_notes)){{ $vendor->vendor_notes }}@else{{ old('vendor_notes') }}@endif">
        <br>
        <input type="submit" name="Submit">
    </form>
</body>
</html>