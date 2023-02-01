<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-calendar me-2"></i>
                Visit
            </h6>
        </div>
        <div class="">
            <button class="btn btn-sm btn-outline-primary rounded" data-bs-toggle="modal" data-bs-target="#school_visit">
                <i class="bi bi-plus"></i>
            </button>
        </div>
    </div>
    <div class="list-group list-group-flush">
        @forelse ($schoolVisits as $schoolVisit)
            <div class="list-group-item">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex flex-column text-start">
                        <div class="pb-2">
                            {{ date('D, d M Y', strtotime($schoolVisit->visit_date)) }}
                        </div>
                        <small>
                            School PIC : <b>{{ $schoolVisit->pic_from_school->schdetail_fullname }}</b>
                        </small>
                        <small>
                            ALL-In PIC : <b>{{ $schoolVisit->pic_from_allin->fullname }}</b>
                        </small>
                        <small class="border-top mt-2 pt-2">
                            Notes : {{ strip_tags($schoolVisit->notes) }}
                        </small>
                    </div>
                    @if ($schoolVisit->status == "waiting")
                        <div class="d-flex ">
                            <div>
                                <form action="{{ route('school.visit.update', ['school' => $school->sch_id, 'visit' => $schoolVisit->id]) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <a style="text-decoration: none" title="Mark as visited" href="javascript:void(0)" class="fs-6 text-success">
                                        <button type="submit" class="border-0 bg-white">
                                            <i class="bi bi-calendar-check text-success"></i>
                                        </button>
                                    </a>
                                </form>
                            </div>
                            <a href="javascript:void(0)" title="Cancel" onclick="confirmDelete('instance/school/{{ $school->sch_id }}/visit', '{{ $schoolVisit->id }}')" class="fs-6 text-danger">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="list-group-item">There's no schedule visit</div>

        @endforelse
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="school_visit">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">School Visit</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('school.visit.store', ['school' => Request::route('school')]) }}" method="POST" id="schoolVisitForm">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="" id="">
                        <div class="col-md-6 mb-2">
                            <label for="">Internal PIC <sup class="text-danger">*</sup></label>
                            <select name="internal_pic" class="modal-select-visit w-100">
                                @foreach ($employees as $internalPic)
                                    <option value="{{ $internalPic->id }}">{{ $internalPic->fullname }}</option>
                                @endforeach
                            </select>
                            @error('internal_pic')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">School PIC <sup class="text-danger">*</sup></label>
                            <select name="school_pic" class="modal-select-visit w-100">
                                @foreach ($details as $schoolPic)
                                    <option value="{{ $schoolPic->schdetail_id }}">{{ $schoolPic->schdetail_fullname }}</option>
                                @endforeach
                            </select>
                            @error('school_pic')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Visit Date <sup class="text-danger">*</sup></label>
                            <input type="date" name="visit_date" id="" class="form-control form-control-sm rounded">
                            @error('visit_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Notes</label>
                            <textarea name="notes" id="" cols="30" rows="10"></textarea>
                        </div>
                    </div>
                    <hr>
                </form>
                {{-- <div class="p-3 d-flex justify-content-between"> --}}
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="schoolVisitForm" class="btn btn-sm btn-primary float-end">Save changes</button>
                {{-- </div> --}}
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.modal-select-visit').select2({
            dropdownParent: $('#school_visit'),
            placeholder: "Select value",
            allowClear: true
        });
    });
</script>
