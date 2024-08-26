<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-building me-2"></i>
                Joined Event
            </h6>
        </div>
    </div>
    <div class="card-body">
        <div class="list-group">
            @forelse ($corporate->events as $event)
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <strong>{{ $event->event_title }}</strong> <br>
                            {{ date('d F Y', strtotime($event->event_startdate)) }} - {{ date('d F Y', strtotime($event->event_enddate)) }}
                        </div>
                        <div class="">
                            <a href="{{ route('event.show', ['event' => $event->event_id]) }}" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div>No Event yet</div>

            @endforelse
        </div>
    </div>
</div>
