@extends('layout.main')

@section('title', 'Assets')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Asset</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Detail</li>
@endsection
@section('content')
    <div class="row g-3">
        <div class="col-md-3 text-center">
            <div class="card">
                <div class="card-body">
                    <img loading="lazy"  loading="lazy" src="{{ asset('img/icon/asset.webp') }}" alt="" class="w-25">
                    @if (isset($asset))
                        <div class="text-center">
                            <div class="badge badge-primary mb-2">
                                Unused Amount:
                                {{ $asset->asset_amount - $asset->asset_running_stock }}
                            </div> <br>
                            @if (empty($edit))
                                <a href="{{ url('master/asset/' . $asset->asset_id . '/edit') }}"
                                    class="btn btn-sm rounded btn-outline-warning "><i class="bi bi-pencil me-1"></i>
                                    Edit</a>
                            @else
                                <a href="{{ url('master/asset/' . $asset->asset_id) }}"
                                    class="btn btn-sm rounded btn-outline-primary "><i class="bi bi-arrow-left me-1"></i>
                                    Back</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-tags me-2"></i>
                            Asset Detail Information
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <form
                        action="@if (isset($asset)) {{ '/master/asset/' . $asset->asset_id }}@else{{ '/master/asset' }} @endif"
                        method="POST">
                        @csrf
                        @if (isset($asset) && isset($edit))
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
                                        {{ empty($asset) || isset($edit) ? '' : 'disabled' }}>
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
                                    <input type="text" placeholder="ex : Apple / Hewlett Packard" name="asset_merktype" class="form-control form-control-sm rounded"
                                        value="{{ isset($asset->asset_merktype) ? $asset->asset_merktype : old('asset_merktype') }}"
                                         {{ empty($asset) || isset($edit) ? '' : 'disabled' }}>
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
                                         {{ empty($asset) || isset($edit) ? '' : 'disabled' }}>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="">
                                        Amount <sup class="text-warning">*</sup>
                                    </label>
                                    <input type="number" placeholder="ex : Quantity" name="asset_amount" class="form-control form-control-sm rounded"
                                        value="{{ isset($asset->asset_amount) ? $asset->asset_amount : old('asset_amount') }}"
                                         {{ empty($asset) || isset($edit) ? '' : 'disabled' }}>
                                    @error('asset_amount')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="">
                                        Unit(s) <sup class="text-warning">*</sup>
                                    </label>
                                    <input type="text" placeholder="ex : Unit / etc " name="asset_unit" class="form-control form-control-sm rounded"
                                        value="{{ isset($asset->asset_unit) ? $asset->asset_unit : old('asset_unit') }}" 
                                        {{ empty($asset) || isset($edit) ? '' : 'disabled' }}>
                                    @error('asset_unit')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="">
                                        Condition <sup class="text-warning">*</sup>
                                    </label>
                                    <select name="asset_condition" class="select w-100"
                                        {{ empty($asset) || isset($edit) ? '' : 'disabled' }}>
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
                                    @error('asset_condition')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
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
                            @if (empty($asset) || isset($edit))
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <hr>
                                        <button type="submit" class="btn btn-sm btn-primary"><i
                                                class="bi bi-save2 me-1"></i>
                                            Submit</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>


    @if (isset($asset) && empty($edit))
        <div class="row mt-3">
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="">
                            <h6 class="m-0 p-0">
                                <i class="bi bi-list me-2"></i>
                                Last Used
                            </h6>
                        </div>
                        <div class="">
                            <button class="btn btn-sm btn-outline-primary rounded-3" data-bs-toggle="modal"
                                data-bs-target="#picForm">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered assetUserTable">
                                <thead class="bg-secondary text-center">
                                    <tr class="text-white">
                                        <th>No</th>
                                        <th>User</th>
                                        <th>Start Date</th>
                                        <th>Amount Used</th>
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
                                                <td class="text-start">{{ $user->first_name . ' ' . $user->last_name }}
                                                </td>
                                                <td>{{ date('F d, Y', strtotime($user->pivot->used_date)) }}</td>
                                                <td>{{ $user->pivot->amount_used }}</td>
                                                <td>{{ $user->pivot->condition ?? '-' }}</td>
                                                <td>
                                                    {{ $user->pivot->amount_used - $user->pivot->returned_detail()->sum('amount_returned') == 0 ? 'Returned' : 'On Used' }}
                                                </td>
                                                <td align="center">
                                                    @if (count($user->pivot->returned_detail) > 0)
                                                        <table class="table">
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
                                                                    <td>{{ date('F d, Y', strtotime($returned_data->returned_date)) }}
                                                                    </td>
                                                                    <td align="center">
                                                                        {{ $returned_data->amount_returned }}
                                                                    </td>
                                                                    <td>{{ $returned_data->condition }}</td>
                                                                    <td>
                                                                        <button
                                                                            onclick="confirmDelete('master/asset/{{ $asset->asset_id }}/used/{{ $user->pivot->id }}/returned', '{{ $returned_data->id }}')"
                                                                            class="btn btn-sm btn-outline-danger p-1 py-0 mx-1"><i
                                                                                class="bi bi-trash2"></i></button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </table>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info mx-1 returnAsset"
                                                        data-bs-toggle="modal" data-bs-target="#returnForm"
                                                        onclick="returnData('{{ $asset->asset_id }}','{{ $user->pivot->id }}')">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger mx-1"
                                                        onclick="confirmDelete('master/asset/{{ $asset->asset_id }}/used', '{{ $user->pivot->id }}')"><i
                                                            class="bi bi-trash2"></i></button>
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
                </div>
            </div>
        </div>


        <div class="modal modal-md fade" id="picForm" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="m-0 p-0">
                            <i class="bi bi-person me-2"></i>
                            @if (!isset($user) || !isset($usedId))
                                Add User Form
                            @else
                                Returned Form
                            @endif
                        </h4>
                    </div>
                    <div class="modal-body">
                        {{-- @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="pb-0 mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif --}}
                        <form action="{{ url('master/asset') . '/' . $asset->asset_id . '/used' }}" method="POST">
                            @csrf
                            {{-- <input type="text" name="user" value="{{ isset($user) ? $user->id : null }}">
                            <input type="hidden" name="usedId" value="{{ isset($usedId) ? $usedId : null }}"> --}}
                            <input type="hidden" name="assetId" value="{{ $asset->asset_id }}">

                            <div class="row">
                                <div class="col-md-8 mb-2">
                                    <label for="">
                                        User
                                    </label>
                                    <select name="user" class="modal-select w-100">
                                        <option data-placeholder="true"></option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">
                                                {{ $employee->first_name . ' ' . $employee->last_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('user')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="">
                                        Amount
                                    </label>
                                    <input type="hidden" name="old_amount_used"
                                        value="{{ $asset->asset_amount - $asset->asset_running_stock }}">
                                    <input type="number" name="amount_used" class="form-control form-control-sm rounded"
                                        value="0" min=0>
                                    @error('amount_used')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="">
                                        Start Using
                                    </label>
                                    <input type="date" name="used_date" class="form-control form-control-sm rounded">
                                    @error('used_date')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="">
                                        Condition
                                    </label>
                                    <select name="condition" class="modal-select w-100">
                                        <option data-placeholder="true"></option>
                                        <option value="Good">Good</option>
                                        <option value="Not Good">Not Good</option>
                                    </select>
                                    @error('condition')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 d-flex justify-content-between mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                        data-bs-dismiss="modal"><i class="bi bi-x me-1"></i>
                                        Cancel</button>
                                    <button type="submit" class="btn btn-sm btn-primary rounded-3"><i
                                            class="bi bi-save me-1"></i>
                                        Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal modal-md fade" id="returnForm" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="m-0 p-0">
                            <i class="bi bi-person me-2"></i>
                            Returned Form
                        </h4>
                    </div>
                    <div class="modal-body">
                        {{-- @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="pb-0 mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif --}}
                        <form action="" method="POST" id="formReturn">
                            @csrf
                            <input type="hidden" name="user" id="userId">
                            <input type="hidden" name="usedId" id="usedId">
                            <input type="hidden" name="assetId" id="assetId">

                            <div class="row">
                                <div class="col-md-2 mb-2">
                                    <label for="">
                                        Amount
                                    </label>
                                    <input type="hidden" name="old_amount_used" id="oldAmountUsed">
                                    <input type="number" name="amount_returned" value="{{ old('amount_returned') }}"
                                        class="form-control form-control-sm rounded" id="amountReturned" min=0>
                                    @error('amount_returned')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-5 mb-2">
                                    <label for="">
                                        End Using
                                    </label>
                                    <input type="hidden" name="old_used_date" id="oldUsedDate">
                                    <input type="date" name="returned_date"
                                        class="form-control form-control-sm rounded" id="returnedDate" value="{{ old('returned_date') }}">
                                    @error('returned_date')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-5 mb-2">
                                    <label for="">
                                        Condition
                                    </label>
                                    <select name="condition" class="modal-select2 w-100">
                                        <option data-placeholder="true"></option>
                                        <option value="Good" @selected(old('condition') == 'Good')>Good</option>
                                        <option value="Not Good" @selected(old('condition') == 'Not Good')>Not Good</option>
                                    </select>
                                    @error('condition')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 d-flex justify-content-between mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                        data-bs-dismiss="modal"><i class="bi bi-x me-1"></i>
                                        Cancel</button>
                                    <button type="submit" class="btn btn-sm btn-primary rounded-3"><i
                                            class="bi bi-save me-1"></i>
                                        Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        
        $(document).ready(function() {
            $('.modal-select').select2({
                dropdownParent: $('#picForm .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });
        });

        $(document).ready(function() {
            $('.modal-select2').select2({
                dropdownParent: $('#returnForm .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });

            if (sessionStorage.getItem('asset_link'))
                $("#formReturn").attr('action', sessionStorage.getItem('asset_link'))
        });


        function returnData(asset_id, used_id) {
            let link = "{{ url('master/asset') }}/" + asset_id.toLowerCase() + '/used/' +
                used_id

            axios.get(link)
                .then(function(response) {

                    // handle success
                    let data = response.data
                    $('#userId').val(data.user.id)
                    $('#usedId').val(data.usedId)
                    $('#assetId').val(data.asset.asset_id)
                    $('#oldAmountUsed').val(data.user.pivot.amount_used - data.amount_returned)
                    $('#amountReturned').val(data.user.pivot.amount_used - data.amount_returned)
                    $('#oldUsedDate').val(data.user.pivot.used_date)

                    var action = '{{ url('master/asset') }}/' + data.asset.asset_id + '/used/' +
                        data.usedId + '/returned'

                    $('#formReturn').attr('action', action)


                    sessionStorage.setItem('asset_link', action)
                })
                .catch(function(error) {
                    // handle error
                    notification('error', error)
                })
        }
    </script>

    @if (
        $errors->has('user') |
            $errors->has('amount_used') |
            $errors->has('used_date') |
            $errors->has('condition'))
        <script>
            $(document).ready(function() {
                $('#picForm').modal('show');

            })
        </script>
    @endif

    @if (
        $errors->has('amount_returned') |
            $errors->has('returned_date') |
            $errors->has('condition'))
        <script>
            $(document).ready(function() {
                $('#returnForm').modal('show');

            })
        </script>
    @endif

@endsection
