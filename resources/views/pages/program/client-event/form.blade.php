@extends('layout.main')

@section('title', 'Client Event ')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Client Event</a></li>
    <li class="breadcrumb-item active" aria-current="page">Form Client Event</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-4 text-center">
            <div class="card rounded mb-3">
                <div class="card-body">
                    <img loading="lazy"  src="{{ asset('img/program.webp') }}" alt="" class="w-75">
                    @if (isset($clientEvent))
                        <div class="mt-3 d-flex flex-column justify-content-center">
                            <div class="mb-2">
                                <a href="{{ url($clientEvent->client->roles[0]->role_name == 'Parent' ? 'client/parent/' . $clientEvent->client->id : 'client/student/' . $clientEvent->client->id . '/program/create') }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-plus"></i> {{ $clientEvent->client->roles[0]->role_name == 'Student' ? 'Add Program' : 'Detail Parent'}}
                                </a>
                            </div>
                            <div>
                                @if (isset($edit))
                                    <a href="{{ url('program/event/' . $clientEvent->clientevent_id) }}"
                                        class="btn btn-sm btn-outline-info rounded mx-1">
                                        <i class="bi bi-arrow-left"></i> Back
                                    </a>
                                @else
                                    <a href="{{ url('program/event/' . $clientEvent->clientevent_id . '/edit') }}"
                                        class="btn btn-sm btn-outline-info rounded mx-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                @endif
                                <button type="button"
                                    onclick="confirmDelete('{{ 'program/event' }}', {{ $clientEvent->clientevent_id }})"
                                    class="btn btn-sm btn-outline-danger rounded mx-1">
                                    <i class="bi bi-trash2"></i> Delete
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-tags me-2"></i>
                            Client Event
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <form
                        action="{{ url(isset($edit) ? 'program/event/' . $clientEvent->clientevent_id : 'program/event') }}"
                        method="POST">
                        @csrf
                        @if (isset($edit))
                            @method('put')
                        @endif
                        <div class="row">
                            @if (!isset($clientEvent))
                                <div class="col-md-4 mb-2">
                                    <label>Existing Client <sup class="text-danger">*</sup></label>
                                    <div class="d-flex align-items-center" id="div-exist">
                                        <div class="form-check ms-4">
                                            <input class="form-check-input exist" type="radio" name="existing_client"
                                                id="exist1" value="1" checked onchange="checkExist(this)">
                                            <label class="" for="exist1">
                                                Yes
                                            </label>
                                        </div>
                                        <div class="form-check ms-5">
                                            <input class="form-check-input exist" type="radio" name="existing_client"
                                                id="exist2" value="0" onchange="checkExist(this)">
                                            <label class="" for="exist2">
                                                No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-8 mb-2 client" id="existing_client">
                                <label>Client Name <sup class="text-danger">*</sup></label>
                                <select name="client_id" class="select w-100"
                                    {{ empty($clientEvent) || isset($edit) ? '' : 'disabled' }}>
                                    <option data-placeholder="true"></option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" @selected((isset($clientEvent) && $clientEvent->client_id == $client->id) || old('client_id') == $client->id)>
                                            {{ $client->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3 d-none client" id="new_client">
                                <div class="card">
                                    <div class="card-header">
                                        Client Detail
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <label>First Name <sup class="text-danger">*</sup></label>
                                                <input type="text" name="first_name" value="{{ old('first_name') }}"
                                                    class="form-control form-control-sm rounded">
                                                @error('first_name')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label>Last Name <sup class="text-danger">*</sup></label>
                                                <input type="text" name="last_name" value="{{ old('last_name') }}"
                                                    class="form-control form-control-sm rounded">
                                                @error('last_name')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label>Email <sup class="text-danger">*</sup></label>
                                                <input type="email" name="mail" value="{{ old('mail') }}"
                                                    class="form-control form-control-sm rounded">
                                                @error('mail')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label>Phone Number <sup class="text-danger">*</sup></label>
                                                <input type="text" name="phone" value="{{ old('phone') }}"
                                                    class="form-control form-control-sm rounded">
                                                @error('phone')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label>Date of Birth <sup class="text-danger">*</sup></label>
                                                <input type="date" name="dob" value="{{ old('dob') }}"
                                                    class="form-control form-control-sm rounded">
                                                @error('dob')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label>State / Region <sup class="text-danger">*</sup></label>
                                                <input type="text" name="state" value="{{ old('state') }}"
                                                    class="form-control form-control-sm rounded">
                                                @error('state')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label>Status <sup class="text-danger">*</sup></label>
                                                <select name="status_client" id="status_client" class="select w-100"
                                                    onchange="checkStatus()">
                                                    <option data-placeholder="true"></option>
                                                    <option value="Student">Student</option>
                                                    <option value="Parent">Parent</option>
                                                    <option value="Teacher/Counsellor">Teacher/Counsellor</option>
                                                </select>
                                                @error('status_client')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            {{-- <div class="col-md-6 mb-2">
                                                <label>Notes</label>
                                                <input type="text" name="notes" value="{{ old('notes') }}">
                                                @error('notes')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div> --}}
                                        </div>
                                        <div class="status-mentee d-none">
                                            <div class="row">

                                                <div class="col-md-6 mb-2">
                                                    <label>School Name <sup class="text-danger">*</sup></label>
                                                    <select name="sch_id" id="schoolName" class="select w-100"
                                                        onChange="addSchool();">
                                                        <option data-placeholder="true"></option>
                                                        @foreach ($schools as $school)
                                                            <option value="{{ $school->sch_id }}">{{ $school->sch_name }}
                                                            </option>
                                                        @endforeach
                                                        <option value="add-new"
                                                            {{ old('sch_id') == 'add-new' ? 'selected' : null }}>Add New
                                                            School</option>
                                                    </select>
                                                    @error('sch_id')
                                                        <small class="text-danger fw-light">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-2" id="studentYear">
                                                    <label>School Grade <sup class="text-danger">*</sup></label>
                                                    <select class="select w-100" id="grade" name="st_grade">
                                                        <option data-placeholder="true"></option>
                                                        @for ($grade = 1; $grade <= 12; $grade++)
                                                            <option value="{{ $grade }}"
                                                                @if (isset($student->st_grade)) {{ $student->st_grade == $grade ? 'selected' : null }}
                                                        @else
                                                        {{ old('st_grade') == $grade ? 'selected' : null }} @endif>
                                                                {{ $grade }}</option>
                                                        @endfor
                                                        <option value="13"
                                                            @if (isset($student->st_grade)) {{ $student->st_grade == 13 ? 'selected' : null }}
                                                        @else
                                                        {{ old('st_grade') == 13 ? 'selected' : null }} @endif>
                                                            Not High School</option>

                                                    </select>
                                                    @error('st_grade')
                                                        <small class="text-danger fw-light">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-2" id="studentYear">
                                                    <label>Graduation Year</label>
                                                    <select class="select w-100" id="graduation_year"
                                                        name="graduation_year">
                                                        <option data-placeholder="true"></option>
                                                        @for ($year = date('Y') + 1; $year >= 1998; $year--)
                                                            <option value="{{ $year }}"
                                                                @if (isset($student->graduation_year)) {{ $student->graduation_year == $year ? 'selected' : null }}
                                                        @else
                                                        {{ old('graduation_year') == $year ? 'selected' : null }} @endif>
                                                                {{ $year }}</option>
                                                        @endfor
                                                    </select>
                                                    @error('graduation_year')
                                                        <small class="text-danger fw-light">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label>Level of Interest <sup class="text-danger">*</sup></label>
                                                    <select class="select w-100" name="st_levelinterest"
                                                        id="levelInterest">
                                                        <option data-placeholder="true"></option>
                                                        <option value="High"
                                                            @if (isset($student->st_levelinterest)) {{ $student->st_levelinterest == 'High' ? 'selected' : null }}
                                                            @else
                                                                {{ old('st_levelinterest') == 'High' ? 'selected' : null }} @endif>
                                                            High</option>
                                                        <option value="Medium"
                                                            @if (isset($student->st_levelinterest)) {{ $student->st_levelinterest == 'Medium' ? 'selected' : null }}
                                                            @else
                                                                {{ old('st_levelinterest') == 'Medium' ? 'selected' : null }} @endif>
                                                            Medium</option>
                                                        <option value="Low"
                                                            @if (isset($student->st_levelinterest)) {{ $student->st_levelinterest == 'Low' ? 'selected' : null }}
                                                            @else
                                                            {{ old('st_levelinterest') == 'Low' ? 'selected' : null }} @endif>
                                                            Low</option>
                                                    </select>
                                                    @error('st_levelinterest')
                                                        <small class="text-danger fw-light">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row school d-none">
                                                <div class="col-md-6 mb-2">
                                                    <label>Other School Name <sup class="text-danger">*</sup></label>
                                                    <input name="sch_name" type="text"
                                                        class="form-control form-control-sm"
                                                        placeholder="Other School Name" autofocus
                                                        value="{{ old('sch_name') }}">
                                                    @error('sch_name')
                                                        <small class="text-danger fw-light">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label>School Type <sup class="text-danger">*</sup></label>
                                                    <select class="select w-100" name="sch_type">
                                                        <option data-placeholder="true"></option>
                                                        <option value="International"
                                                            {{ old('sch_type') == 'International' ? 'selected' : null }}>
                                                            International</option>
                                                        <option value="National"
                                                            {{ old('sch_type') == 'National' ? 'selected' : null }}>
                                                            National</option>
                                                    </select>
                                                    @error('sch_type')
                                                        <small class="text-danger fw-light">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label>Curriculum <sup class="text-danger">*</sup></label>
                                                    <select class="select w-100" name="sch_curriculum[]" multiple
                                                        id="schCurriculum">
                                                        <option data-placeholder="true"></option>
                                                        @foreach ($curriculums as $curriculum)
                                                            <option value="{{ $curriculum->id }}"
                                                                {{ old('sch_curriculum') == $curriculum->name ? 'selected' : null }}>
                                                                {{ $curriculum->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('sch_curriculum')
                                                        <small class="text-danger fw-light">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label>School Market <sup class="text-danger">*</sup></label>
                                                    <select class="select w-100" name="sch_score">
                                                        <option data-placeholder="true"></option>
                                                        <option value="2"
                                                            {{ old('sch_score') == 2 ? 'selected' : null }}>Low Market
                                                        </option>
                                                        <option value="4"
                                                            {{ old('sch_score') == 4 ? 'selected' : null }}>Mid Market
                                                        </option>
                                                        <option value="5"
                                                            {{ old('sch_score') == 5 ? 'selected' : null }}>Up Market
                                                        </option>
                                                    </select>
                                                    @error('sch_score')
                                                        <small class="text-danger fw-light">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2 program">
                                <label>Event Name <sup class="text-danger">*</sup></label>
                                <select name="event_id" class="select w-100" id="eventName"
                                    {{ empty($clientEvent) || isset($edit) ? '' : 'disabled' }}>
                                    <option data-placeholder="true"></option>
                                    @if (isset($events) && count($events) > 0)
                                        @foreach ($events as $event)
                                            <option value="{{ $event->event_id }}"
                                                {{ old('event_id') == $event->event_id ? 'selected' : null }}>
                                                {{ $event->event_title }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('event_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Conversion Lead <sup class="text-danger">*</sup></label>
                                <select name="lead_id" id="leadSource" class="select w-100"
                                    {{ empty($clientEvent) || isset($edit) ? '' : 'disabled' }}>
                                    <option data-placeholder="true"></option>
                                    @if (isset($leads) && count($leads) > 0)
                                        @foreach ($leads as $lead)
                                            <option data-lead="{{ $lead->main_lead }}" value="{{ $lead->lead_id }}"
                                                {{ old('lead_id') == $lead->lead_id ? 'selected' : null }}>
                                                {{ $lead->main_lead == 'KOL' ? $lead->sub_lead : $lead->main_lead }}
                                            </option>
                                        @endforeach
                                        {{-- <option data-lead="KOL" value="kol" {{ old('lead_id') == "kol" ? "selected" : null }}>KOL</option> --}}
                                    @endif
                                </select>
                                @error('lead_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2 edufair d-none">
                                <label>Edufair Name <sup class="text-danger">*</sup></label>
                                <select name="eduf_id" class="select w-100"
                                    {{ empty($clientEvent) || isset($edit) ? '' : 'disabled' }}>
                                    <option data-placeholder="true"></option>
                                    @if (isset($ext_edufair) && count($ext_edufair) > 0)
                                        @foreach ($ext_edufair as $edufair)
                                            <option value="{{ $edufair->id }}"
                                                @if (isset($clientEvent->edufLead->id)) {{ $clientEvent->edufLead->id == $edufair->id ? 'selected' : null }}
                                                @else
                                                    {{ old('eduf_id') == $edufair->id ? 'selected' : null }} @endif>
                                                @if ($edufair->title != null)
                                                    {{ $edufair->title }}
                                                @else
                                                    {{ $edufair->organizer_name }}
                                                @endif
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('eduf_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            {{-- <div class="col-md-6 mb-2 kol d-none">
                                <label>KOL Name <sup class="text-danger">*</sup></label>
                                <select name="kol_lead_id" class="select w-100" {{ empty($clientEvent) || isset($edit) ? '' : 'disabled' }}>
                                    <option data-placeholder="true"></option>
                                    @if (isset($kols) && count($kols) > 0)
                                    @foreach ($kols as $kol)
                                        <option value="{{ $kol->lead_id }}"
                                            @if (isset($clientEvent->lead_id) && $clientEvent->lead_id == 'LS017')
                                                {{ $clientEvent->lead_id == $kol->lead_id ? "selected" : null }}
                                            @else
                                                {{ old('kol_lead_id') == $kol->lead_id ? "selected" : null }}
                                            @endif
                                            >{{ $kol->sub_lead }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('kol_lead_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div> --}}
                            <div class="col-md-6 mb-2 partner d-none">
                                <label>Partner Name <sup class="text-danger">*</sup></label>
                                <select name="partner_id" id="partner_id" class="select w-100"
                                    {{ empty($clientEvent) || isset($edit) ? '' : 'disabled' }}>
                                    <option data-placeholder="true"></option>
                                    @forelse ($partners as $partner)
                                        <option value="{{ $partner->corp_id }}"
                                            @if (old('partner_id') == $partner->corp_id) {{ 'selected' }}
                                            @elseif(isset($clientEvent) && $clientEvent->partner_id == $partner->corp_id) {{ 'selected' }} @endif>
                                            {{ $partner->corp_name }} </option>
                                    @empty
                                        <option>There's no data</option>
                                    @endforelse
                                </select>
                                @error('partner_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Joined Date <sup class="text-danger">*</sup></label>
                                <input type="date" name="joined_date"
                                    value="{{ isset($clientEvent) ? date('Y-m-d', strtotime($clientEvent->joined_date)) : date('Y-m-d') }}"
                                    class="form-control form-control-sm rounded"
                                    {{ empty($clientEvent) || isset($edit) ? '' : 'disabled' }}>
                                @error('joined_date')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Status <sup class="text-danger">*</sup></label>
                                <select name="status" id="" class="select w-100"
                                    {{ empty($clientEvent) || isset($edit) ? '' : 'disabled' }}>
                                    @if (isset($clientEvent))
                                        <option value="0" {{ $clientEvent->status == '0' ? 'selected' : '' }}>Join
                                        </option>
                                        <option value="1" {{ $clientEvent->status == '1' ? 'selected' : '' }}>Attend
                                        </option>
                                    @else
                                        <option value="0">Join</option>
                                        <option value="1">Attend</option>
                                    @endif
                                </select>
                                @error('status')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Notes </label>
                                <select name="notes" id="" class="select w-100"
                                    {{ empty($clientEvent) || isset($edit) ? '' : 'disabled' }}>
                                    @if (isset($clientEvent) && isset($clientEvent->notes))
                                        <option value="VVIP" {{ $clientEvent->notes == 'VVIP' ? 'selected' : '' }}>VVIP
                                        </option>
                                        <option value="VIP" {{ $clientEvent->notes == 'VIP' ? 'selected' : '' }}>VIP
                                        </option>
                                    @else
                                        <option value="">-</option>
                                        <option value="VVIP">VVIP</option>
                                        <option value="VIP">VIP</option>
                                    @endif
                                </select>
                                @error('status')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            @if (empty($clientEvent) || isset($edit))
                                <div class="mt-3 text-end">
                                    <button class="btn btn-sm btn-primary rounded" type="submit"><i
                                            class="bi bi-save2 me-1"></i> Submit</button>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Client Detail  --}}
            @if (isset($clientEvent))
            @php
                $role = $clientEvent->client->roles->first()->role_name;
                if($clientEvent->child_id != null && $role == 'Parent'){
                    $client = $clientEvent->children;
                }else if($clientEvent->child_id == null && $role == 'Parent'){
                    $client = null;
                }else if($role == 'Student' || $role == 'Teacher/Counselor'){
                    $client = $clientEvent->client;
                }
            @endphp
                @if(isset($client))
                    
                    <div class="card rounded mb-3">
                        <div class="card-header">
                            <div class="">
                                <h6 class="m-0 p-0">
                                    <i class="bi bi-person me-2"></i>
                                    {{ $role == 'Parent' || $role == 'Student' ? 'Student' : 'Teacher' }} Detail
                                </h6>
                            </div>
                        </div>
                        <div class="card-body px-3">
                            <table class="table table-striped" border="0">
                                <tr>
                                    <td width="30%">Name</td>
                                    <td width="1%">:</td>
                                    <td>{{ $client->full_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td width="30%">Email</td>
                                    <td width="1%">:</td>
                                    <td>{{ $client->mail ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Phone Number</td>
                                    <td width="1%">:</td>
                                    <td>{{ $client->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td width="30%">School Name</td>
                                    <td width="1%">:</td>
                                    <td>{{ $client->school->sch_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td width="30%">Graduation Year</td>
                                    <td width="1%">:</td>
                                    <td>{{ $client->graduation_year_real ?? '-'}}</td>
                                </tr>
                                <tr>
                                    <td>Register As</td>
                                    <td width="1%">:</td>
                                    <td>{{ $client->register_as }}</td>
                                </tr>
                                <tr>
                                    <td>Have you ever participated ALL-in Event/Program?</td>
                                    <td width="1%">:</td>
                                    <td>{{ $client->participated ?? '-' }} </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Parent Detail  --}}
            @if (isset($clientEvent))
            @php
                if($clientEvent->parent_id != null){
                    $parent = $clientEvent->parent;
                }else if($clientEvent->client->roles->first()->role_name == 'Parent'){
                    $parent = $clientEvent->client;
                }else{
                    $parent = null;
                }
            @endphp
            @if(isset($parent))
                <div class="card rounded mb-3">
                    <div class="card-header">
                        <div class="">
                            <h6 class="m-0 p-0">
                                <i class="bi bi-person me-2"></i>
                                Parent Detail
                            </h6>
                        </div>
                    </div>
                    <div class="card-body px-3">
                        <table class="table table-striped" border="0">
                            <tr>
                                <td width="30%">Name</td>
                                <td width="1%">:</td>
                                <td>{{ $parent->full_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td width="30%">Email</td>
                                <td width="1%">:</td>
                                <td>{{ $parent->mail }}</td>
                            </tr>
                            <tr>
                                <td>Phone Number</td>
                                <td width="1%">:</td>
                                <td>{{ $parent->phone }}</td>
                            </tr>                        
                        </table>
                    </div>
                </div>
            @endif
            @endif

        </div>
    </div>

    @if (isset($clientEvent->lead_id))
        <script>
            $(document).ready(function() {
                $('#leadSource').val('{{ $clientEvent->lead_id }}').trigger('change')
            })
        </script>
        @if (isset($clientEvent->event_id))
            <script>
                $(document).ready(function() {
                    $('#eventName').val('{{ $clientEvent->event_id }}').trigger('change')
                })
            </script>
        @endif
    @endif

    @if ((string) old('existing_client') == '0')
        <script>
            $(document).ready(function() {
                $('input[name=existing_client][value="{{ old('existing_client') }}"]').prop('checked', true).trigger(
                    'change')
            })
        </script>
    @endif

    @if (!empty(old('status_client')))
        <script>
            $(document).ready(function() {
                $('#status_client').val("{{ old('status_client') }}").trigger('change')
            })
        </script>
    @endif

    @if (!empty(old('sch_id')))
        <script>
            $(document).ready(function() {
                $('#schoolName').val("{{ old('sch_id') }}").trigger('change')
            })
        </script>
    @endif

    @if (!empty(old('lead_id')))
        <script>
            $(document).ready(function() {
                $('#leadSource').val("{{ old('lead_id') }}").trigger('change')
            })
        </script>
    @endif



    <script>
        function checkExist(radio) {
            let exist = radio.value
            $('.client').addClass('d-none')
            if (exist == 1) {
                $('#existing_client').removeClass('d-none')
            } else {
                $('#new_client').removeClass('d-none')
            }
        }

        function checkStatus() {
            let status = $('#status_client').val();
            if (status == 'Student' || status == 'Teacher/Counsellor') {
                $('.status-mentee').removeClass('d-none')
            } else if (status == 'Parent') {
                $('.status-mentee').addClass('d-none')
            }
        }

        function addSchool() {
            var s = $('#schoolName').val();

            if (s == 'add-new') {
                $(".school").removeClass("d-none");
            } else {
                $(".school").addClass("d-none");
            }
        }

        $("#grade").on('change', async function() {

            var grade = $(this).val()
            var html = ''
            $("#graduation_year").html('')
            var current_year = new Date().getFullYear()

            if (grade == 13) {

                for (var i = current_year; i > 2009; i--) {

                    html += "<option value='" + i + "'>" + i + "</option>"
                }

            } else {

                var max = 13
                var min = 1
                for (var i = current_year; i <= current_year + (max - grade); i++) {

                    html += "<option value='" + i + "'>" + i + "</option>"
                }

            }

            $("#graduation_year").append(html)

        })

        $("#leadSource").on('change', function() {
            var lead = $(this).select2().find(":selected").data('lead')
            if (lead.includes('All-In Event')) {

                // $(".program").removeClass("d-none")
                $(".edufair").addClass("d-none")
                $(".kol").addClass("d-none")
                $(".partner").addClass("d-none")


            } else if (lead.includes('External Edufair')) {

                // $(".program").addClass("d-none")
                $(".edufair").removeClass("d-none")
                $(".kol").addClass("d-none")
                $(".partner").addClass("d-none")

            } else if (lead.includes('KOL')) {

                // $(".program").addClass("d-none")
                $(".edufair").addClass("d-none")
                $(".kol").removeClass("d-none")
                $(".partner").addClass("d-none")

            } else if (lead.includes('All-In Partners')) {

                // $(".program").addClass("d-none")
                $(".edufair").addClass("d-none")
                $(".kol").addClass("d-none")
                $(".partner").removeClass("d-none")

            } else {

                $(".edufair").addClass("d-none")
                $(".kol").addClass("d-none")
                $(".partner").addClass("d-none")
            }
        })
    </script>
@endsection
