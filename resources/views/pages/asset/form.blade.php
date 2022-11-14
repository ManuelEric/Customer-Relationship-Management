@extends('layout.main')

@section('title', 'Asset - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('master/asset') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Asset
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card rounded">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <img src="{{ asset('img/asset.png') }}" alt="" class="w-75">
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
                    <form action="{{ isset($user) ? url('master/asset').'/'.$asset->asset_id.'/used/'.$request->route('used').'/returned'  : url('master/asset').'/'.$asset->asset_id.'/used' }}" method="POST">
                        @csrf
                        <input type="hidden" name="user" value="{{ isset($user) ? $user->id : null }}">
                        <input type="hidden" name="usedId" value="{{ isset($usedId) ? $usedId : null }}">
                        <input type="hidden" name="assetId" value="{{ $asset->asset_id }}">
                        <h5>
                            @if (!isset($user))
                                Add User Form
                            @else
                                Returned Form
                            @endif
                        </h5>
                        <div class="col-12 mb-3">
                            <div class="row g-1 border border-1 rounded-2 p-3 ">
                                @if (!isset($user))
                                <div class="col-md-3">
                                    <label for="">
                                        User
                                    </label>
                                    <select name="user" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                @if (isset($user) && $user->id == $employee->id)
                                                {{ "selected" }}
                                                @endif
                                                >{{ $employee->first_name.' '.$employee->last_name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                @endif
                                <div class="col-md-1">
                                    <label for="">
                                        Amount
                                    </label>
                                    <input type="hidden" name="old_amount_used" value="{{ isset($user->pivot->amount_used) ? $user->pivot->amount_used : 0 }}">
                                    @if (!isset($user))
                                        <input type="number" name="amount_used" class="form-control form-control-sm rounded" value="0" min=0>
                                    @else

                                        <input type="number" name="amount_returned" class="form-control form-control-sm rounded"
                                            value="{{ isset($user->pivot->amount_used) ? $user->pivot->amount_used : 0 }}" min=0>
                                    @endif
                                </div>
                                @if (!isset($user))
                                <div class="col-md-2">
                                    <label for="">
                                        Start Using
                                    </label>
                                    <input type="date" name="used_date" class="form-control form-control-sm rounded"
                                        value="{{ isset($user->pivot->used_date) ? $user->pivot->used_date : null }}" >
                                </div>
                                @endif

                                @if (isset($user))
                                <div class="col-md-2">
                                    <label for="">
                                        End Using
                                    </label>
                                    <input type="hidden" name="old_used_date" value="{{ isset($user->pivot->used_date) ? $user->pivot->used_date : null }}">
                                    <input type="date" name="returned_date" class="form-control form-control-sm rounded"
                                        value="{{ isset($user->pivot->returned_date) ? $user->pivot->returned_date : null }}">
                                </div>
                                @endif
                                <div class="col-md-2">
                                    <label for="">
                                        Condition
                                    </label>
                                    <select name="condition" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        <option value="Good">Good</option>
                                        <option value="Not Good">Not Good</option>
                                    </select>
                                </div>
                                {{-- <div class="col-md-2">
                                    <label for="">
                                        Status
                                    </label>
                                    <select name="asset_condition" class="select w-100">
                                        <option data-placeholder="true"></option>
                                    </select>

                                </div> --}}
                                <div class="col-12 text-end mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3"><i class="bi bi-x me-1"></i>
                                        Cancel</button>
                                    <button type="submit" class="btn btn-sm btn-primary rounded-3"><i class="bi bi-save me-1"></i>
                                        Save</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="col-12 mb-3">
                        <h5>Last Used</h5>
                        <table class="table table-bordered assetUserTable">
                            <thead class="bg-secondary text-center">
                                <tr>
                                    <th>No</th>
                                    <th>User</th>
                                    <th>Start Date</th>
                                    <th>Stock</th>
                                    <th>Available Qty</th>
                                    <th>First Condition</th>
                                    <th>Status</th>
                                    <th>Detail</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @if (count($asset->userUsedAsset) > 0)
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($asset->userUsedAsset as $user)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $user->first_name.' '.$user->last_name }}</td>
                                        <td>{{ date('F d, Y', strtotime($user->pivot->used_date)) }}</td>
                                        <td>{{ $user->pivot->amount_used }}</td>
                                        <td>{{ $user->pivot->amount_used - $asset->asset_running_stock }}</td>
                                        <td>{{ $user->pivot->condition ?? '-' }}</td>
                                        <td>{{ $asset->asset_running_stock != 0 ? "on used" : "available" }}</td>
                                        <td align="center">
                                            @if (count($user->pivot->returned_detail) > 0)
                                            <table class="returnedTable">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Returned Date</th>
                                                    <th>Amount</th>
                                                    <th>Condition</th>
                                                    <th></th>
                                                </tr>
                                                @php
                                                    $subno = 1;
                                                @endphp
                                                @foreach ($user->pivot->returned_detail as $returned_data)
                                                    <tr>
                                                        <td>{{ $subno++ }}</td>
                                                        <td>{{ date('F d, Y', strtotime($returned_data->returned_date)) }}</td>
                                                        <td align="center">{{ $returned_data->amount_returned }}</td>
                                                        <td>{{ $returned_data->condition }}</td>
                                                        <td>
                                                            <button class="btn btn-sm bg-danger mx-1 deleteReturned" data-usedid="{{ $user->pivot->id }}" data-returnedid="{{ $returned_data->id }}"><i class="bi bi-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm bg-info mx-1 returnAsset" data-usedid="{{ $user->pivot->id }}"><i class="bi bi-search"></i></button>
                                            <button class="btn btn-sm bg-danger mx-1 deleteUsed" data-usedid="{{ $user->pivot->id }}"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                                @else
                                    <tr>
                                        <td colspan="8">
                                            <label for="" class="py-2">No Data</label>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script type="text/javascript">
    var assetId = "{{ $asset->asset_id }}"
    var usedId = "{{ $request->route('used') }}"
    var returnedId = "{{ $request->route('returned') }}"

        $(".returnAsset").each(function(index, item) {
            $(this).click(function() {

                var usedId = $(item).data('usedid');

                window.location.href = "{{ url('master/asset') }}/" + assetId.toLowerCase() + '/used/' + usedId;
            })
        })

        $(".deleteUsed").each(function(index, item) {
            $(this).click(function() {
                
                var usedId = $(item).data('usedid')

                confirmDelete('master/asset', assetId + '/used/' + usedId)
            })
        })

        $(".deleteReturned").each(function(index, item) {
            $(this).click(function() {
                var usedId = $(item).data('usedid')
                var returnedId = $(item).data('returnedid')

                confirmDelete('master/asset', assetId + '/used/' + usedId + '/returned/' + returnedId)
            })
        })
    </script>

@endsection
