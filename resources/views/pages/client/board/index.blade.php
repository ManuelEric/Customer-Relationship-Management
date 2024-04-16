@extends('layout.main')

@section('title', 'Sales Board')

@push('styles')
    <link rel="stylesheet" href="{{ asset('library/dashboard/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">
    <style>
        .btn-download span,
        .btn-import span {
            display: none;
        }

        .btn-download:hover>span,
        .btn-import:hover>span {
            display: inline-block;
        }
    </style>
@endpush

@section('content')
<div class="modal fade" id="followup-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- body here -->
            </div>
            <div class="modal-footer">
                <button type="button" id="submit-btn" class="btn btn-primary btn-sm" onclick="showLoading()">Save</button>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
    {{ implode('', $errors->all('<div>:message</div>')) }}
@endif

<div class="rounded bg-secondary mb-1 p-2">
    <div class="row align-items-center justify-content-between">
        <div class="col-md-6">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Sales Board - Follow Up
            </h5>
        </div>

        <div class="col-md-2">
            <div class="dropdown">
                <button href="#" class="btn btn-sm btn-light text-dark dropdown-toggle w-100"
                    data-bs-toggle="dropdown" data-bs-auto-close="false" id="filter">
                    <i class="bi bi-funnel me-2"></i> Filter
                </button>
                <form action="{{ url('client/board') }}" method="GET" class="dropdown-menu dropdown-menu-end pt-0 advance-filter shadow" style="width: 400px;">
                    <div class="dropdown-header bg-info text-dark py-2 d-flex justify-content-between">
                        Advanced Filter
                        <i class="bi bi-search"></i>
                    </div>
                    <div class="row p-3">
                        <div class="col-md-12 mb-2">
                            <label for="">Client Name</label>
                            <input type="text" name="name" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-12 mb-2">
                            <div class="row g-2">
                                <label>Follow-up Date</label>
                                <div class="col-md-6 mb-2">
                                    <input type="date" name="start_followup_date" id="start_followup_date"
                                        class="form-control form-control-sm rounded">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <input type="date" name="end_followup_date" id="end_followup_date"
                                        class="form-control form-control-sm rounded">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-2">
                            <div class="row g-2">
                                <label>Followed-up Date</label>
                                <div class="col-md-6 mb-2">
                                    <input type="date" name="start_followedup_date" id="start_followedup_date"
                                        class="form-control form-control-sm rounded">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <input type="date" name="end_followedup_date" id="end_followedup_date"
                                        class="form-control form-control-sm rounded">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    id="cancel">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-outline-success">Submit</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

        </div>

    </div>
</div>

<div class="d-flex overflow-scroll mt-4">
    <div>
        <label for="" class="pb-2">
            <h4>Fresh Lead</h4>
            <span>{{ $fresh_lead->count() }} Leads</span>
        </label>
        <div class="border-top border-warning border-3 px-2 py-3 me-2 overflow-scroll" style="background: #e0dfdf; height: 75vh;">

            @forelse ($fresh_lead as $lead)
                <div @class([
                    'card',
                    'mt-2' => $loop->index > 0
                ]) style="width: 18rem;">
                    <div class="card-body">

                        <a href="{{ route('student.show', ['student' => $lead->id]) }}" class="text-decoration-none" target="_blank">
                            <h5 class="card-title" data-name="{{ $lead->full_name }}">{{ $lead->full_name }}</h5>
                        </a>
                        <label for="">Details:</label>
                        <div class="d-flex flex-column">
                            <div>
                                <a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a>
                            </div>
                            <div>
                                Register as: {{ $lead->register_as ?? 'unknown' }}
                            </div>
                        </div>
                    
                        <hr>

                        <label for="">Parent Details:</label>
                        @foreach ($lead->parents as $parent)
                        <div class="d-flex flex-column">
                            <div class="text-success">
                                <b>{{ $parent->full_name }}</b>
                            </div>
                            <div>
                                <a href="tel:{{ $parent->phone }}">{{ $parent->phone }}</a>
                            </div>
                        </div>
                        @endforeach
                        <div class="d-flex justify-content-end">
                            <a href="#" data-column="new-opportunity" data-id="{{ $lead->id }}" class="fs-6 act-btn" title="Set Up Appointment" data-bs-toggle="modal" data-bs-target="#followup-modal">
                                <i class="bi bi-calendar"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="card mt-2 hidden" style="width: 18rem;"></div>

            @endforelse
        </div>
    </div>
    <div>
        <label for="" class="pb-2">
            <h4>Scheduled Appointment</h4>
            <span>{{ $scheduled->count() }} Leads</span>
        </label>
        <div class="border-top border-info border-3 px-2 py-3 me-2 overflow-scroll" style="background: #e0dfdf; height: 75vh;">
            @forelse ($scheduled as $lead)
            <div @class([
                'card',
                'mt-2' => $loop->index > 0
            ]) style="width: 18rem;" data-followup-key="{{ $lead->id }}">
                <div class="card-body">
                    <a href="{{ route('student.show', ['student' => $lead->client->id]) }}" class="text-decoration-none" target="_blank">
                        <h5 class="card-title" data-name="{{ $lead->client->full_name }}">{{ $lead->client->full_name }}</h5>
                    </a>
                    <div class="d-flex justify-content-between align-items-end">
                        <span class="pe-2 text-left">
                            Appointment: <b class="text-success">{{ date('d M Y, H:i', strtotime($lead->followup_date)) }}</b>
                            
                            <p>
                                Topic: <a href="javascript:void(0)" class="read-btn" data-bs-toggle="modal" data-bs-target="#followup-modal" data-notes="{{ $lead->notes }}" data-name="{{ $lead->client->full_name }}">Read here</a>
                            </p>
                        </span>
                        <a href="#" class="fs-6 act-btn" data-id="{{ $lead->client->id }}" data-column="scheduled-appointment" title="Set Follow-up Date" data-bs-toggle="modal" data-bs-target="#followup-modal">
                            <i class="bi bi-calendar-week"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="card mt-2 hidden" style="width: 18rem;"></div>

            @endforelse
        </div>
    </div>
    <div>
        <label for="" class="pb-2">
            <h4>Followed Up</h4>
            <span>{{ $followed_up->count() }} Leads</span>
        </label>
        <div class="border-top border-primary border-3 px-2 py-3 me-2 overflow-scroll" style="background: #e0dfdf; height: 75vh;">
            @forelse ($followed_up as $lead)
            <div @class([
                'card',
                'mt-2' => $loop->index > 0
            ]) style="width: 18rem;">
                <div class="card-body">
                    <a href="{{ route('student.show', ['student' => $lead->client->id]) }}" class="text-decoration-none" target="_blank">
                        <h5 class="card-title">{{ $lead->client->full_name }}</h5>
                    </a>
                    
                    <div class="d-flex">
                        <i class="bi bi-calendar"></i>
                        <div class="ms-2">
                            Last meet:
                            {{ date('d M Y, H:i', strtotime($lead->followup_date)) }}
                        </div>
                    </div>
                    <div class="d-flex">
                        <i class="bi bi-file-earmark-text"></i>
                        <div class="ms-2">
                            <a href="javascript:void(0)" class="read-mom-btn" data-bs-toggle="modal" data-bs-target="#followup-modal" data-followup-date="{{ date('d M Y, H:i', strtotime($lead->followup_date)) }}" data-mom="{{ $lead->minutes_of_meeting }}" data-name="{{ $lead->client->full_name }}">Meeting Minutes</a>
                        </div>
                    </div>
                    {{-- <div class="d-flex justify-content-end">
                        <a href="#" class="fs-6" title="Set Follow-up Date">
                            <i class="bi bi-calendar-check"></i>
                        </a>
                    </div> --}}
                </div>
            </div>
            @empty
            <div class="card mt-2 hidden" style="width: 18rem;"></div>

            @endforelse
        </div>
    </div>
    {{-- <div>
        <label for="" class="pb-2">
            <h4>To Be Invoiced</h4>
            <span>{{ $delayed->count() }} Leads</span>
        </label>
        <div class="border-top border-success border-3 px-2 py-3 me-2" style="background: #e0dfdf">
            @foreach ($delayed as $lead)
            <div @class([
                'card',
                'mt-2' => $loop->index > 0
            ]) style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="{{ route('student.show', ['student' => $lead->id]) }}" class="text-decoration-none" target="_blank">
                            <h5 class="card-title">{{ $lead->full_name }}</h5>
                        </a>
                    </h5>
                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    <div class="d-flex justify-content-end">
                        <a href="#" class="fs-6" title="Set Follow-up Date">
                            <i class="bi bi-calendar-x"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div>
        <label for="" class="pb-2">
            <h4>Awaiting Payment</h4>
            <span>{{ $delayed->count() }} Leads</span>
        </label>
        <div class="border-top border-danger border-3 px-2 py-3 me-2" style="background: #e0dfdf">
            @foreach ($delayed as $lead)
            <div @class([
                'card',
                'mt-2' => $loop->index > 0
            ]) style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="{{ route('student.show', ['student' => $lead->id]) }}" class="text-decoration-none" target="_blank">
                            <h5 class="card-title">{{ $lead->full_name }}</h5>
                        </a>
                    </h5>
                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    <div class="d-flex justify-content-end">
                        <a href="#" class="fs-6" title="Set Follow-up Date">
                            <i class="bi bi-calendar-x"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div> --}}
</div>

@endsection

@push('scripts')
<script>
    
    var modal_title = $("#followup-modal .modal-title");
    var modal_content = $('#followup-modal .modal-body');

    // set up appointment action
    $(".act-btn").each(function (index) {
        $(this).click(function () {

            var column = $(this).data('column');
            var identifier = $(this).data('id');
            var submit_btn = $("#submit-btn");
            var client_name = $('.card').eq(index).find('.card-title').data('name');
    
            switch (column) {
    
                case "new-opportunity":
                    var form_id = 'setup-appointment-form';
                    var route = '{{ url("/") }}/client/student/'+ identifier +'/followup';
                    var content = '<form action="'+ route +'" method="POST" id="'+ form_id +'">'+
                            '<input type="hidden" name="_token" value="{{ csrf_token() }}" />'+
                            '<div class="mb-3">'+
                                '<label class="form-label">For</label>'+
                                '<input type="text" class="form-control" value="'+ client_name +'" readonly>'+
                            '</div>'+
                            '<div class="mb-3">'+
                                '<label class="form-label">Follow-up Date</label>'+
                                '<input type="datetime-local" class="form-control" name="followup_date">'+
                            '</div>'+
                            '<div class="mb-3">'+
                                '<label class="form-label">Notes to remember</label>'+
                                '<textarea name="notes" style="height:18rem;" class="form-control"></textarea>'+
                                '<input type="hidden" name="status" value="0" />' +
                            '</div>'+
                            '</form>';
                            
                    modal_content.html(content);
                    modal_title.html("Let's schedule a follow-up to discuss this further.");
                    submit_btn.attr('type', 'submit').attr('form', form_id);
                    break;

                case "scheduled-appointment":

                    var form_id = 'setup-appointment-form';
                    var followup_key = $(".card").eq(index).data('followup-key');
                    var route = '{{ url("/") }}/client/student/'+ identifier +'/followup/'+ followup_key;
                    var default_status =  1;

                    var content = '<form action="'+ route +'" method="POST" id="'+ form_id +'">' +
                                '<input type="hidden" name="_method" value="PUT" />'+
                                '<input type="hidden" name="_token" value="{{ csrf_token() }}" />'+
                                '<input type="hidden" name="status" value="'+ default_status +'" />' +
                                '<div class="mb-3">' +
                                    '<label class="form-label">For</label>'+
                                    '<input type="text" class="form-control" value="'+ client_name +'" readonly>'+
                                '</div>' +
                                '<div class="mb-3 minutes-of-meeting-container">' +
                                    '<label class="form-label">Minutes of Meeting</label>'+
                                    '<textarea name="minutes_of_meeting" style="height:18rem;" class="form-control"></textarea>'+
                                '</div>' +
                                '<div class="mb-3">' +
                                    '<label class="form-label">Would you like to schedule another meeting to discuss this further?</label>'+
                                    '<div class="form-check ms-3 ps-2">' +
                                        '<input class="form-check-input" onchange="changeInputRadio(this.value, '+ identifier +', '+ followup_key +')" type="radio" value="yes" name="next_followup" id="radio-continue-yes">' +
                                        '<label class="form-check-label" for="radio-continue-yes">' + 
                                            'Yes, I would like to schedule another meeting.' +
                                        '</label>' +
                                    '</div>' +
                                    '<div class="form-check ms-3 ps-2">' +
                                        '<input class="form-check-input" onchange="changeInputRadio(this.value, '+ identifier +', '+ followup_key +')" checked type="radio" value="no" name="next_followup" id="radio-continue-no">' +
                                        '<label class="form-check-label" for="radio-continue-no">' + 
                                            'No, I think we covered everything for now.' +
                                        '</label>' +
                                    '</div>' +
                                '</div>'+
                                '<div class="next-followup-appointment">' +
                                    
                                '</div>';
                                '</form>';

                    modal_content.html(content);
                    modal_title.html('How did your meeting with the client go?')
                    submit_btn.attr('type', 'submit').attr('form', form_id);
                    break;
    
            }
    
        })

    });

    // read the notes for scheduled appointment action
    $(".read-btn").each(function (index) {
        $(this).click(function () {
            var notes = $(this).data('notes');
            var client_name = $(this).data('name');

            var content = '<div class="mb-3">' +
                            '<label class="form-label">For</label>'+
                            '<input type="text" class="form-control" value="'+ client_name +'" readonly>'+
                        '</div>' +
                        '<div class="mb-3">' +
                            '<label class="form-label">Notes</label>'+
                            '<textarea name="notes" style="height:18rem;" class="form-control">'+ notes +'</textarea>'+
                        '</div>';

            modal_content.html(content);
            modal_title.html('These are your appointment notes for your reference.')
        });
    });


    $(".read-mom-btn").each(function (index) {
        $(this).click(function () {
            var mom = $(this).data('mom');
            var client_name = $(this).data('name');
            var followup_date = $(this).data('followup-date');

            var content = '<div class="mb-3">' +
                            '<label class="form-label">For</label>'+
                            '<input type="text" class="form-control" value="'+ client_name +'" readonly>'+
                        '</div>' +
                        '<div class="mb-3">' +
                            '<label class="form-label">Minutes of Meeting</label>'+
                            '<textarea name="notes" style="height:18rem;" class="form-control">'+ mom +'</textarea>'+
                        '</div>';

            modal_content.html(content);
            modal_title.html('Here are the minutes from our meeting on <br> <b>' + followup_date + '</b>')
        });
    });


    function changeInputRadio(value, identifier, followup_key) {

        if (value == "yes") {

            $(".minutes-of-meeting-container").slideUp(function () {
                $(".next-followup-appointment").hide();

                
                var route = '{{ url("/") }}/client/student/'+ identifier +'/followup';
                $("#setup-appointment-form").attr('action', route);

                var additional_content = '<div class="mb-3">'+
                                '<label class="form-label">Follow-up Date</label>'+
                                '<input type="datetime-local" class="form-control" name="followup_date">'+
                            '</div>'+
                            '<div class="mb-3">'+
                                '<label class="form-label">Notes to remember</label>'+
                                '<textarea name="notes" style="height:18rem;" class="form-control"></textarea>'+
                            '</div>';
                $('input[name=status]').val(0);
                $('input[name=_method]').val('POST');

                $(".next-followup-appointment").html(additional_content).slideDown();

                
            });

        } else {
            
            $(".next-followup-appointment").slideUp(function () {
                $(".minutes-of-meeting-container").slideDown();

                var route = '{{ url("/") }}/client/student/'+ identifier +'/followup/' + followup_key;
                $("#setup-appointment-form").attr('action', route);
                $('input[name=status]').val(1);
                $('input[name=_method]').val('PUT');

            });

        }
    }
    
</script>
@endpush