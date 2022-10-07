@extends('layout.main')

@section('title', 'Vendor - Bigdata Platform')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('vendor') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Add Vendor
        </a>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <img src="{{ asset('img/vendor.png') }}" alt="" class="w-75">
                </div>
                <div class="col-md-8">

                    <form
                        action="@if (isset($vendor)) {{ '/vendor/' . $vendor->vendor_id }}@else{{ '/vendor' }} @endif"
                        method="POST">
                        @csrf
                        @if (isset($vendor))
                            @method('put')
                        @endif

                        <div class="mb-2">
                            <label>
                                Vendor Name <sup class="text-danger">*</sup>
                            </label>
                            <input type="text" name="vendor_name" class="form-control form-control-sm rounded"
                                value="@if (isset($vendor->vendor_name)) {{ $vendor->vendor_name }}@else{{ old('vendor_name') }} @endif">
                            @error('vendor_name')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <label for="">
                                Alamat
                            </label>
                            <textarea name="vendor_address" class="form-control form-control-sm rounded" rows="3">
@if (isset($vendor->vendor_address))
{{ $vendor->vendor_address }}@else{{ old('vendor_address') }}
@endif
        </textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Phone <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="vendor_phone" class="form-control form-control-sm rounded"
                                        value="@if (isset($vendor->vendor_phone)) {{ $vendor->vendor_phone }}@else{{ old('vendor_phone') }} @endif">
                                    @error('vendor_phone')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Type
                                    </label>
                                    <select name="vendor_type" class="form-select form-select-sm rounded">
                                        @foreach ($type as $item)
                                            <option value="{{ $item->name }}"
                                                @if ((isset($vendor->vendor_type) && $item->name == $vendor->vendor_type) || old('vendor_type') == $item->name) {{ 'selected' }} @endif>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Material
                                    </label>
                                    <input type="text" name="vendor_material"
                                        class="form-control form-control-sm rounded"
                                        value="@if (isset($vendor->vendor_material)) {{ $vendor->vendor_material }}@else{{ old('vendor_material') }} @endif">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Size
                                    </label>
                                    <input type="text" name="vendor_size" class="form-control form-control-sm rounded"
                                        value="@if (isset($vendor->vendor_size)) {{ $vendor->vendor_size }}@else{{ old('vendor_size') }} @endif">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Processing Time
                                    </label>
                                    <input type="text" name="vendor_processingtime"
                                        class="form-control form-control-sm rounded"
                                        value="@if (isset($vendor->vendor_processingtime)) {{ $vendor->vendor_processingtime }}@else{{ old('vendor_processingtime') }} @endif">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Price <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="vendor_unitprice"
                                        class="form-control form-control-sm rounded"
                                        value="@if (isset($vendor->vendor_unitprice)) {{ $vendor->vendor_unitprice }}@else{{ old('vendor_unitprice') }} @endif">
                                    @error('vendor_unitprice')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="">
                                Notes
                            </label>
                            <textarea type="text" name="vendor_notes" class="form-control form-control-sm rounded" rows="3">
        @if (isset($vendor->vendor_notes))
{{ $vendor->vendor_notes }}@else{{ old('vendor_notes') }}
@endif
        </textarea>
                        </div>
                        <div class="text-center">
                            <hr>
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-save2 me-1"></i>
                                Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
