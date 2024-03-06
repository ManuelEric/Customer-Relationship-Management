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
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Let's schedule a follow-up to discuss this further.</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- body here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm">Set up</button>
            </div>
        </div>
    </div>
</div>

<div class="d-flex overflow-scroll">
    <div>
        <label for="" class="pb-2">
            <h4>Fresh Lead</h4>
            <span>{{ $new_opportunity->count() }} Leads</span>
        </label>
        <div class="border-top border-warning border-3 px-2 py-3 me-2 overflow-scroll" style="background: #e0dfdf; height: 75vh;">

            @foreach ($new_opportunity as $lead)
                <div @class([
                    'card',
                    'mt-2' => $loop->index > 0
                ]) style="width: 18rem;">
                    <div class="card-body">
                        <a href="{{ route('student.show', ['student' => $lead->id]) }}" class="text-decoration-none" target="_blank">
                            <h5 class="card-title" data-name="{{ $lead->full_name }}">{{ $lead->full_name }}</h5>
                        </a>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                        <div class="d-flex justify-content-end">
                            <a href="#" data-column="new-opportunity" data-id="{{ $lead->id }}" class="fs-6 act-btn" title="Set Follow-up Date" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                <i class="bi bi-calendar"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div>
        <label for="" class="pb-2">
            <h4>Scheduled Appointment</h4>
            <span>{{ $scheduled->count() }} Leads</span>
        </label>
        <div class="border-top border-info border-3 px-2 py-3 me-2" style="background: #e0dfdf">
            @foreach ($scheduled as $lead)
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
                            <i class="bi bi-calendar-week"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div>
        <label for="" class="pb-2">
            <h4>Followed Up</h4>
            <span>{{ $followed_up->count() }} Leads</span>
        </label>
        <div class="border-top border-primary border-3 px-2 py-3 me-2" style="background: #e0dfdf">
            @foreach ($followed_up as $lead)
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
                            <i class="bi bi-calendar-check"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div>
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
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(".act-btn").each(function (index) {

        $(this).click(function () {

            var column = $(this).data('column');
            var identifier = $(this).data('id');
            var modal_content = $('.modal-body');
            var client_name = $('.card-title').eq(index).data('name');
    
            switch (column) {
    
                case "new-opportunity":
                    var route = 'client/student/'+ identifier +'/followup';
                    var content = '<form action="'+ route +'" method="POST">'+
                            '<div class="mb-3">'+
                                '<label class="form-label">For</label>'+
                                '<input type="text" class="form-control" value="'+ client_name +'" readonly>'+
                            '</div>'+
                            '<div class="mb-3">'+
                                '<label class="form-label">Follow-up Date</label>'+
                                '<input type="date" class="form-control" name="followup_date">'+
                            '</div>'+
                            '<div class="mb-3">'+
                                '<label class="form-label">Notes to remember</label>'+
                                '<textarea name="notes" style="height:18rem;" class="form-control"></textarea>'+
                            '</div>'+
                            '</form>';
                    break;
    
            }
    
            modal_content.html(content);
        })

    });
</script>
@endpush