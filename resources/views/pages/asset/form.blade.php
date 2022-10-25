@extends('layout.main')

@section('title', 'Asset - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('master/asset') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Asset
        </a>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <img src="{{ asset('img/asset.png') }}" alt="" class="w-75">
                    {{ Request::segment(4) }}
                </div>
                <div class="col-md-8">
                    <div class="text-end ">
                        <a href="{{ url('master/asset/' . $asset->asset_id . '/edit') }}"
                            class="btn btn-sm btn-warning rounded-3 {{ Request::segment(4) == 'edit' ? 'd-none' : '' }}"><i
                                class="bi bi-pencil me-1"></i> Edit</a>
                        <a href="{{ url('master/asset/' . $asset->asset_id) }}"
                            class="btn btn-sm btn-dark rounded-3 {{ Request::segment(4) == 'edit' ? '' : 'd-none' }}"><i
                                class="bi bi-info-circle me-1"></i> View</a>
                    </div>
                    <form
                        action="@if (isset($asset)) {{ '/master/asset/' . $asset->asset_id }}@else{{ '/master/asset' }} @endif"
                        method="POST">
                        @csrf
                        @if (isset($asset) && Request::segment(4) == 'edit')
                            @method('put')
                        @endif
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Asset Name <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="asset_name" class="form-control form-control-sm rounded"
                                        value="{{ isset($asset->asset_name) ? $asset->asset_name : old('asset_name') }}"
                                        {{ Request::segment(4) == 'edit' ? '' : 'readonly' }}>
                                    @error('asset_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Asset Merk/Type
                                    </label>
                                    <input type="text" name="asset_merktype" class="form-control form-control-sm rounded"
                                        value="{{ isset($asset->asset_merktype) ? $asset->asset_merktype : old('asset_merktype') }}"
                                        {{ Request::segment(4) == 'edit' ? '' : 'readonly' }}>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Achieved Date
                                    </label>
                                    <input type="date" name="asset_dateachieved"
                                        class="form-control form-control-sm rounded"
                                        value="{{ isset($asset->asset_dateachieved) ? $asset->asset_dateachieved : old('asset_dateachieved') }}"
                                        {{ Request::segment(4) == 'edit' ? '' : 'readonly' }}>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="">
                                        Amount
                                    </label>
                                    <input type="number" name="asset_amount" class="form-control form-control-sm rounded"
                                        value="{{ isset($asset->asset_amount) ? $asset->asset_amount : old('asset_amount') }}"
                                        {{ Request::segment(4) == 'edit' ? '' : 'readonly' }}>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="">
                                        Unit(s)
                                    </label>
                                    <input type="text" name="asset_unit" class="form-control form-control-sm rounded"
                                        value="{{ isset($asset->asset_unit) ? $asset->asset_unit : old('asset_unit') }}"
                                        {{ Request::segment(4) == 'edit' ? '' : 'readonly' }}>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="">
                                        Condition
                                    </label>
                                    <select name="asset_condition" class="select w-100"
                                        {{ Request::segment(4) == 'edit' ? '' : 'disabled' }}>
                                        <option data-placeholder="true"></option>
                                        <option value="Good"
                                            {{ isset($asset->asset_condition) && $asset->asset_condition == 'Good' ? 'selected' : '' }}>
                                            Good
                                        </option>
                                        <option value="Good Enough"
                                            {{ isset($asset->asset_condition) && $asset->asset_condition == 'Good Enough' ? 'selected' : '' }}>
                                            Good
                                            Enough
                                        </option>
                                        <option value="Not Good"
                                            {{ isset($asset->asset_condition) && $asset->asset_condition == 'Not Good' ? 'selected' : '' }}>
                                            Not
                                            Good
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Notes
                                    </label>
                                    <textarea name="asset_notes" cols="30" rows="2" class="form-control form-control-sm rounded">{{ isset($asset->asset_notes) ? $asset->asset_notes : old('asset_notes') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12 {{ Request::segment(4) == 'edit' ? '' : 'd-none' }}">
                                <div class="text-center">
                                    <hr>
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-save2 me-1"></i>
                                        Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if (isset($asset) && Request::segment(4) != 'edit')
                <hr>
                <div class="row mt-3">
                    <div class="col-12 mb-2">
                        <button class="btn btn-sm btn-info rounded-3">Add User</button>
                    </div>

                    <div class="col-12 mb-3">
                        <div class="row g-1 border border-1 rounded-2 p-3 ">
                            <div class="col-md-3">
                                <label for="">
                                    User
                                </label>
                                <select name="asset_condition" class="select w-100">
                                    <option data-placeholder="true"></option>
                                </select>

                            </div>
                            <div class="col-md-1">
                                <label for="">
                                    Amount
                                </label>
                                <input type="number" name="asset_merktype" class="form-control form-control-sm rounded"
                                    value="">
                            </div>
                            <div class="col-md-2">
                                <label for="">
                                    Start Using
                                </label>
                                <input type="date" name="asset_merktype" class="form-control form-control-sm rounded"
                                    value="">
                            </div>
                            <div class="col-md-2">
                                <label for="">
                                    End Using
                                </label>
                                <input type="date" name="asset_merktype" class="form-control form-control-sm rounded"
                                    value="">
                            </div>
                            <div class="col-md-2">
                                <label for="">
                                    Condition
                                </label>
                                <select name="asset_condition" class="select w-100">
                                    <option data-placeholder="true"></option>
                                </select>

                            </div>
                            <div class="col-md-2">
                                <label for="">
                                    Status
                                </label>
                                <select name="asset_condition" class="select w-100">
                                    <option data-placeholder="true"></option>
                                </select>

                            </div>
                            <div class="col-12 text-end mt-2">
                                <button class="btn btn-sm btn-outline-danger rounded-3"><i class="bi bi-x me-1"></i>
                                    Cancel</button>
                                <button class="btn btn-sm btn-primary rounded-3"><i class="bi bi-save me-1"></i>
                                    Save</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <table class="table table-bordered">
                            <thead class="bg-secondary text-center">
                                <tr>
                                    <th>No</th>
                                    <th>User</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Amount</th>
                                    <th>Condition</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <td>No</td>
                                    <td>User</td>
                                    <td>Start Date</td>
                                    <td>End Date</td>
                                    <td>Amount</td>
                                    <td>Condition</td>
                                    <td>Status</td>
                                    <td>
                                        <button class="btn btn-sm bg-warning mx-1"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm bg-danger mx-1"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>


@endsection
