<div class="card rounded mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-building me-2"></i>
                Partner
            </h6>
        </div>
        <div class="">
            <button class="btn btn-sm btn-outline-primary rounded mx-1" data-bs-toggle="modal" data-bs-target="#partner">
                <i class="bi bi-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        @if (isset($partnerEvent))
            @forelse ($partnerEvent as $partner)
                <div class="list-group">
                    <div class="d-flex list-group-item justify-content-between">
                        <div class="">
                            {{ $partner->corp_name }}
                        </div>
                        <div class="" style="cursor:pointer" onclick="confirmDelete('master/event/{{ $event->event_id }}/partner', '{{ $partner->corp_id }}')">
                            <i class="bi bi-trash2"></i>
                        </div>
                    </div>
                </div>

                @empty
                There's no partner
            @endforelse
        @endif
    </div>
</div>

<div class="modal fade" id="partner" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <span>
                    Partner
                </span>
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="modal-body w-100 text-start">
                <form action="{{ route('event.partner.store', ['event' => $event->event_id]) }}" method="POST" id="formPosition">
                    @csrf
                    <div class="put"></div>
                    <div class="row g-2">
                        <div class="col-md-12">
                            <div class="mb-2">
                                <label for="">
                                    Partner Name <sup class="text-danger">*</sup>
                                </label>
                                <select name="corp_id" class="modal-select-partner w-100">
                                    <option data-placeholder="true"></option>
                                    @if (isset($partners))
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->corp_id }}">{{ $partner->corp_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="#" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-save2 me-1"></i>
                            Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
