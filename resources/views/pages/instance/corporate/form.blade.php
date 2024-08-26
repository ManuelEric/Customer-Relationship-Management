@extends('layout.main')

@section('title', 'Partners Detail')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Partners</a></li>
    <li class="breadcrumb-item active" aria-current="page">Form Partner</li>
@endsection
@section('content')
    @php
        $type = ['Corporate', 'Individual Professional', 'Tutoring Center', 'Course Center', 'Agent', 'Community', 'NGO'];
        sort($type);
        
        $partnership_type = ['Market Sharing', 'Program Collaborator', 'Internship', 'External Mentor'];
        sort($partnership_type);
    @endphp

    <div class="row">
        <div class="col-md-4 mb-2">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <img loading="lazy"  src="{{ asset('img/school.webp') }}" alt="" class="w-75">
                    <h5>
                        {{ isset($corporate) ? $corporate->corp_name : 'Add New Partner' }}
                    </h5>
                    @if (isset($corporate))
                        <div class="mt-3 d-flex justify-content-center">
                            @if (isset($edit))
                                <a href="{{ url('instance/corporate/' . strtolower($corporate->corp_id)) }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>
                            @else
                                <a href="{{ url('instance/corporate/' . strtolower($corporate->corp_id) . '/edit') }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            @endif
                            <button type="button"
                                onclick="confirmDelete('instance/corporate', '{{ $corporate->corp_id }}')"
                                class="btn btn-sm btn-outline-danger rounded mx-1">
                                <i class="bi bi-trash2"></i> Delete
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            @if (isset($corporate) && empty($edit))
                @include('pages.instance.corporate.form-detail.agreement')
                @include('pages.instance.corporate.form-detail.event')
                @include('pages.instance.corporate.form-detail.contact')
            @endif

        </div>
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-building me-2"></i>
                            Partner Detail
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <form
                        action="{{ isset($corporate) ? route('corporate.update', ['corporate' => $corporate->corp_id]) : route('corporate.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($corporate))
                            @method('PUT')
                            <input type="hidden" name="corp_id" value="{{ $corporate->corp_id }}">
                        @endif

                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label>Corporate / Partner Name <sup class="text-danger">*</sup></label>
                                <input type="text" name="corp_name"
                                    value="{{ isset($corporate->corp_name) ? $corporate->corp_name : old('corp_name') }}"
                                    class="form-control form-control-sm rounded"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                @error('corp_name')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-2">
                                <label>Industry</label>
                                <input type="text" name="corp_industry"
                                    value="{{ isset($corporate->corp_industry) ? $corporate->corp_industry : old('corp_industry') }}"
                                    class="form-control form-control-sm rounded"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                @error('corp_industry')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-2">
                                <label>Email <sup class="text-danger">*</sup></label>
                                <input type="email" name="corp_mail"
                                    value="{{ isset($corporate->corp_mail) ? $corporate->corp_mail : old('corp_mail') }}"
                                    class="form-control form-control-sm rounded"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                @error('corp_mail')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-2">
                                <label>Phone <sup class="text-danger">*</sup></label>
                                <input type="text" name="corp_phone"
                                    value="{{ isset($corporate->corp_phone) ? $corporate->corp_phone : old('corp_phone') }}"
                                    class="form-control form-control-sm rounded"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                @error('corp_phone')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-2">
                                <label>Instagram</label>
                                <input type="text" name="corp_insta"
                                    value="{{ isset($corporate->corp_insta) ? $corporate->corp_insta : old('corp_insta') }}"
                                    class="form-control form-control-sm rounded"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                @error('corp_insta')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-2">
                                <label>Website </label>
                                <input type="text" name="corp_site" placeholder="https://xxxxxx.xxxx"
                                    value="{{ isset($corporate->corp_site) ? $corporate->corp_site : old('corp_site') }}"
                                    class="form-control form-control-sm rounded"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                @error('corp_site')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-2">
                                <label>Region</label>
                                <input type="text" name="corp_region"
                                    value="{{ isset($corporate->corp_region) ? $corporate->corp_region : old('corp_region') }}"
                                    class="form-control form-control-sm rounded"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                @error('corp_region')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-2">
                                <label>Address</label>
                                <textarea name="corp_address" cols="30" rows="10">{{ isset($corporate->corp_address) ? $corporate->corp_address : old('corp_address') }}</textarea>
                            </div>

                            <div class="col-md-4 mb-2">
                                <label for="">Country Type <sup class="text-danger">*</sup></label>
                                <select name="country_type" class="select w-100"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                    <option data-placeholder="true"></option>
                                    <option value="Indonesia"
                                        {{ (isset($corporate->country_type) && $corporate->country_type == 'Indonesia') || old('country_type') == 'Indonesia' ? 'selected' : null }}>
                                        Indonesia</option>
                                    <option value="Overseas"
                                        {{ (isset($corporate->country_type) && $corporate->country_type == 'Overseas') || old('country_type') == 'Overseas' ? 'selected' : null }}>
                                        Overseas</option>
                                </select>
                                @error('country_type')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-2">
                                <label for="">Type <sup class="text-danger">*</sup></label>
                                <select name="type" class="select w-100"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                    <option data-placeholder="true"></option>
                                    @for ($i = 0; $i < count($type); $i++)
                                        <option value="{{ $type[$i] }}"
                                            {{ (isset($corporate->type) && $corporate->type == $type[$i]) || old('type') == $type[$i] ? 'selected' : null }}>
                                            {{ $type[$i] }}</option>
                                    @endfor
                                </select>
                                @error('type')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-2">
                                <label for="">Partnership Type </label>
                                <select name="partnership_type" class="select w-100"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                    <option data-placeholder="true"></option>
                                    @for ($i = 0; $i < count($partnership_type); $i++)
                                        <option value="{{ $partnership_type[$i] }}"
                                            {{ (isset($corporate->partnership_type) && $corporate->partnership_type == $partnership_type[$i]) || old('partnership_type') == $partnership_type[$i] ? 'selected' : null }}>
                                            {{ $partnership_type[$i] }}</option>
                                    @endfor
                                </select>
                                @error('partnership_type')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-2">
                                <label>Note</label>
                                <textarea name="corp_note" cols="30" rows="10">{{ isset($corporate->corp_note) ? $corporate->corp_note : old('corp_note') }}</textarea>
                                @error('corp_note')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            @if (empty($corporate) || isset($edit))
                                <div class="text-end mt-3">
                                    <button class="btn btn-sm btn-primary rounded" type="submit"><i
                                            class="bi bi-save2 me-1"></i> Submit</button>
                                </div>
                            @endif
                        </div>

                    </form>
                </div>
            </div>

            @if (isset($corporate) && empty($edit))
                @include('pages.instance.corporate.form-detail.program')
            @endif
        </div>
    </div>

    <script>
        // Select2 Modal 
        $(document).ready(function() {
            $('.modal-select').select2({
                dropdownParent: $('#programForm .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });
        });
    </script>
@endsection
