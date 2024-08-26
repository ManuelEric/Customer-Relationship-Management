@extends('layout.main')

@section('title', 'Vendors')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Vendor</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Detail</li>
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-md-3 text-center">
            <div class="card rounded">
                <div class="card-body">
                    <img loading="lazy"  loading="lazy" src="{{ asset('img/icon/vendor.webp') }}" alt="" class="w-25">
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card rounded">
                <div class="card-body">
                    <form action="{{ url(isset($vendor) ? 'master/vendor/' . $vendor->vendor_id : 'master/vendor') }}"
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
                                value="{{ isset($vendor->vendor_name) ? $vendor->vendor_name : old('vendor_name') }}">
                            @error('vendor_name')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <label for="">
                                Address
                            </label>
                            <textarea name="vendor_address" class="form-control form-control-sm rounded" rows="3">{{ isset($vendor->vendor_address) ? $vendor->vendor_address : old('vendor_address') }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Phone <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="vendor_phone" class="form-control form-control-sm rounded"
                                        value="{{ isset($vendor->vendor_phone) ? $vendor->vendor_phone : old('vendor_phone') }}">
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
                                    <select name="vendor_type" class="select w-100">
                                        <option data-placeholder="true"></option>
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
                                        value="{{ isset($vendor->vendor_material) ? $vendor->vendor_material : old('vendor_material') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Size
                                    </label>
                                    <input type="text" name="vendor_size" class="form-control form-control-sm rounded"
                                        value="{{ isset($vendor->vendor_size) ? $vendor->vendor_size : old('vendor_size') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Processing Time
                                    </label>
                                    <input type="text" name="vendor_processingtime"
                                        class="form-control form-control-sm rounded"
                                        value="{{ isset($vendor->vendor_processingtime) ? $vendor->vendor_processingtime : old('vendor_processingtime') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Price <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="vendor_unitprice"
                                        class="form-control form-control-sm rounded"
                                        value="{{ isset($vendor->vendor_unitprice) ? $vendor->vendor_unitprice : old('vendor_unitprice') }}">
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
                            <textarea type="text" name="vendor_notes" class="form-control form-control-sm rounded" rows="3">{{ isset($vendor->vendor_notes) ? $vendor->vendor_notes : old('vendor_notes') }}</textarea>
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
