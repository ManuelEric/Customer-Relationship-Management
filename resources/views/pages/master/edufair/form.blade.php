@extends('layout.main')

@section('title', 'Edufair')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('master/edufair') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Edufair
        </a>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <img loading="lazy"  src="{{ asset('img/icon/edufair.webp') }}" alt="" class="w-25">
                    @if (isset($edufair))
                        <div class="card mt-2">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-1">
                                    <div class="">Prospective Client</div>
                                    <div class="text-primary">{{ $edufair->client()->where('st_statuscli', 0)->count() }}</div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-1">
                                    <div class="">Potential Client</div>
                                    <div class="text-primary">{{ $edufair->client()->where('st_statuscli', 1)->count() }}</div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-1">
                                    <div class="">Current Client</div>
                                    <div class="text-primary">{{ $edufair->client()->where('st_statuscli', 2)->count() }}</div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="">Completed Client</div>
                                    <div class="text-primary">{{ $edufair->client()->where('st_statuscli', 3)->count() }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <div class="d-flex justify-content-center">
                                <a href="{{ url(isset($edit) ? 'master/edufair/' . $edufair->id : 'master/edufair/' . $edufair->id . '/edit') }}"
                                    class="btn btn-sm btn-outline-primary">
                                    @if (isset($edit))
                                        <i class="bi bi-arrow-left me-1"></i> Back
                                    @else
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    @endif
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if (isset($edufair) && empty($edit))
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="">
                            <h6 class="m-0 p-0">
                                <i class="bi bi-chat-left-text me-2"></i>
                                Review
                            </h6>
                        </div>
                        <div class="">
                            <button class="btn btn-sm btn-outline-primary rounded" data-bs-toggle="modal"
                                data-bs-target="#reviewForm" onclick="resetForm()">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="max-height: 200px; overflow:auto;">
                            @forelse ($reviews as $reviews)
                                <div class="item mb-3">
                                    <div class="d-flex justify-content-between">
                                        <div class="">
                                            <h6 class="mb-0">
                                                {{ $reviews->reviewer->first_name . ' ' . $reviews->reviewer->last_name }}
                                            </h6>
                                            <small>
                                                {{ date('M, d Y', strtotime($reviews->created_at)) }} |
                                                {{ $reviews->score }}
                                            </small>
                                        </div>
                                        <div class="">
                                            <i class="bi bi-pencil text-warning me-1 cursor-pointer" data-bs-toggle="modal"
                                                data-bs-target="#reviewForm"
                                                onclick="getReview('{{ $edufair->id }}','{{ $reviews->id }}')"></i>

                                            <i class="bi bi-trash2 text-danger cursor-pointer"
                                                onclick="confirmDelete('master/edufair/{{ $edufair->id }}/review', '{{ $reviews->id }}')"></i>
                                        </div>
                                    </div>
                                    <div class="ps-1 my-1" style="border-left: 1px solid #dedede">
                                        {!! $reviews->review !!}
                                    </div>
                                    <hr class="my-1">
                                </div>

                                @empty
                                <div class="item">
                                    No review yet
                                </div>
                            @endforelse
                    </div>
                </div>
            @endif

            @if(isset($edufair) &&  empty($edit))
                @include('pages.master.edufair.detail.speaker')
            @endif
        </div>
        <div class="col-md-9">
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-building me-2"></i>
                            Edufair Detail
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <form
                        action="{{ isset($edufair) ? route('edufair.update', ['edufair' => $edufair->id]) : route('edufair.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($edufair))
                            @method('put')
                            <input type="hidden" name="id" value="{{ $edufair->id }}">
                        @endif
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label>Organizer</label>
                                {{ old('organizer') }}
                                <div class="mt-1">
                                    <input class="form-check-input" type="radio" id="school" name="organizer"
                                        value="school"
                                        @checked(old('organizer') == 'school')
                                        {{ isset($edufair) ? (isset($edufair->sch_id) && $edufair->sch_id != null ? 'checked' : null) : 'checked' }}
                                        {{ isset($edufair) && empty($edit) ? 'disabled' : '' }}>
                                    <label for="school">School</label>

                                    <input class="form-check-input ms-2" type="radio" id="corporate" name="organizer"
                                        value="corporate"
                                        @checked(old('organizer') == 'corporate')
                                        {{ isset($edufair->corp_id) && $edufair->corp_id != null ? 'checked' : null }}
                                        {{ isset($edufair) && empty($edit) ? 'disabled' : '' }}>
                                    <label for="corporate">Corporate</label>
                                </div>
                            </div>

                            <div class="col-md-5 .mb-2" id="schoolList">
                                <label>School <sup class="text-danger">*</sup></label>
                                <select name="sch_id" class="select w-100"
                                    {{ isset($edufair) && empty($edit) ? 'disabled' : '' }}>
                                    <option data-placeholder="true"></option>
                                    @foreach ($schools as $school)
                                        <option value="{{ $school->sch_id }}"
                                            {{ isset($edufair->sch_id) && $edufair->sch_id == $school->sch_id || (old('sch_id') == $school->sch_id) ? 'selected' : null }}>
                                            {{ $school->sch_name }}</option>
                                    @endforeach
                                </select>
                                @error('sch_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-5 .mb-2" id="corporateList" style="display:none">
                                <label>Corporate <sup class="text-danger">*</sup></label>
                                <select name="corp_id" class="select w-100"
                                    {{ isset($edufair) && empty($edit) ? 'disabled' : '' }}>
                                    <option data-placeholder="true"></option>
                                    @foreach ($corporates as $corporate)
                                        <option value="{{ $corporate->corp_id }}"
                                            {{ isset($edufair->corp_id) && $edufair->corp_id == $corporate->corp_id || (old('corp_id') == $corporate->corp_id) ? 'selected' : null }}>
                                            {{ $corporate->corp_name }}</option>
                                    @endforeach
                                </select>
                                @error('corp_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label>PIC from ALL-in <sup class="text-danger">*</sup></label>
                                <select name="intr_pic" class="select w-100"
                                    {{ isset($edufair) && empty($edit) ? 'disabled' : '' }}>
                                    <option data-placeholder="true"></option>
                                    @foreach ($internal_pic as $pic)
                                        <option value="{{ $pic->id }}"
                                            {{ isset($edufair->intr_pic) && $edufair->intr_pic == $pic->id || (old('intr_pic') == $pic->id) ? 'selected' : null }}>
                                            {{ $pic->first_name . ' ' . $pic->last_name }}</option>
                                    @endforeach
                                </select>
                                @error('intr_pic')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 my-2">
                                <label>Title <sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control form-control-sm rounded" name="title" 
                                    value="{{ isset($edufair->title) ? $edufair->title : old('title') }}" {{ isset($edufair) && empty($edit) ? 'disabled' : '' }}>
                                @error('title')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 my-2">
                                <label>Location <sup class="text-danger">*</sup></label>
                                <textarea name="location" cols="30" rows="10">{{ isset($edufair->location) ? $edufair->location : old('location') }}</textarea>
                                @error('location')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>External PIC Profile</h6>
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <label>Name <sup class="text-danger">*</sup></label>
                                                <input class="form-control form-control-sm rounded" type="text"
                                                    name="ext_pic_name"
                                                    value="{{ isset($edufair->ext_pic_name) ? $edufair->ext_pic_name : old('ext_pic_name') }}"
                                                    {{ isset($edufair) && empty($edit) ? 'disabled' : '' }}>
                                                @error('ext_pic_name')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <label>Email <sup class="text-danger">*</sup></label>
                                                <input class="form-control form-control-sm rounded" type="email"
                                                    name="ext_pic_mail"
                                                    value="{{ isset($edufair->ext_pic_mail) ? $edufair->ext_pic_mail : old('ext_pic_mail') }}"
                                                    {{ isset($edufair) && empty($edit) ? 'disabled' : '' }}>
                                                @error('ext_pic_mail')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <label>Phone <sup class="text-danger">*</sup></label>
                                                <input class="form-control form-control-sm rounded" type="text"
                                                    name="ext_pic_phone"
                                                    value="{{ isset($edufair->ext_pic_phone) ? $edufair->ext_pic_phone : old('ext_pic_phone') }}"
                                                    {{ isset($edufair) && empty($edit) ? 'disabled' : '' }}>
                                                @error('ext_pic_phone')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-2 mt-4">
                                <label>First Discussion</label>
                                <input class="form-control form-control-sm rounded" type="date"
                                    name="first_discussion_date"
                                    value="{{ isset($edufair->first_discussion_date) ? $edufair->first_discussion_date : old('first_discussion_date') }}"
                                    {{ isset($edufair) && empty($edit) ? 'disabled' : '' }}
                                    {{ isset($edufair) && empty($edit) ? 'disabled' : '' }}>
                                @error('first_discussion_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-2 mt-4">
                                <label>Last Discussion</label>
                                <input class="form-control form-control-sm rounded" type="date"
                                    name="last_discussion_date"
                                    value="{{ isset($edufair->last_discussion_date) ? $edufair->last_discussion_date : old('last_discussion_date') }}"
                                    {{ isset($edufair) && empty($edit) ? 'disabled' : '' }}>
                                @error('last_discussion_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-2 mt-4">
                                <label>Event Start</label>
                                <input class="form-control form-control-sm rounded" type="date" name="event_start"
                                    value="{{ isset($edufair->event_start) ? $edufair->event_start : old('event_start') }}"
                                    {{ isset($edufair) && empty($edit) ? 'disabled' : '' }}>
                                @error('event_start')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-2 mt-4">
                                <label>Event End</label>
                                <input class="form-control form-control-sm rounded" type="date" name="event_end"
                                    value="{{ isset($edufair->event_end) ? $edufair->event_end : old('event_end') }}"
                                    {{ isset($edufair) && empty($edit) ? 'disabled' : '' }}>
                                @error('event_end')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-2">
                                <label>Status <sup class="text-danger">*</sup></label>
                                <select name="status" class="select w-100"
                                    {{ isset($edufair) && empty($edit) ? 'disabled' : '' }}>
                                    <option value="0" @selected(isset($edufair) && $edufair->status == 0 || old('status') == 0)>Pending</option>
                                    <option value="1" @selected(isset($edufair) && $edufair->status == 1 || old('status') == 1)>Success</option>
                                    <option value="2" @selected(isset($edufair) && $edufair->status == 2 || old('status') == 2)>Denied</option>
                                </select>
                                @error('status')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-2">
                                <label>Notes</label>
                                <textarea name="notes" cols="30" rows="10">{{ isset($edufair->notes) ? $edufair->notes : old('notes') }}</textarea>
                            </div>

                            @if (empty($edufair) || isset($edit))
                                <div class="text-end mt-2">
                                    <button class="btn btn-sm btn-primary rounded" type="submit">
                                        <i class="bi bi-save2 me-1"></i>
                                        Submit
                                    </button>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal  --}}
    @if (isset($edufair))
        <div class="modal modal-md fade" id="reviewForm" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="m-0 p-0">
                            <i class="bi bi-plus me-2"></i>
                            Review
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
                        @endif
                         --}}
                        <form action="{{ url('master/edufair/' . $edufair->id . '/review') }}" method="POST"
                            id="formReview">
                            @csrf
                            <div class="put"></div>
                            <input class="form-control form-control-sm rounded" type="hidden" name="eduf_id"
                                value="{{ $edufair->id }}">
                            <div class="row">
                                <div class="col-md-7 mb-2">
                                    <label>Name</label>
                                    <select name="reviewer_name" id="reviewer_name" class="modal-select w-100">
                                        <option data-placeholder="true"></option>
                                        @foreach ($internal_pic as $pic)
                                            <option value="{{ $pic->id }}">
                                                {{ $pic->first_name . ' ' . $pic->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('reviewer_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-5 mb-2">
                                    <label>Score</label>
                                    <select name="score" id="score" class="modal-select w-100">
                                        <option data-placeholder="true"></option>
                                        <option value="Excellent">Excellent</option>
                                        <option value="Good">Good</option>
                                        <option value="Fair">Fair</option>
                                        <option value="Poor">Poor</option>
                                        <option value="Bad">Bad</option>
                                    </select>
                                    @error('score')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-2">
                                    <label>Review</label>
                                    <textarea name="review" cols="30" rows="10" id="review"></textarea>
                                    @error('review')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-12 mt-2">
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                            data-bs-dismiss="modal">
                                            <i class="bi bi-x me-1"></i>
                                            Cancel
                                        </button>
                                        <button class="btn btn-sm btn-primary rounded-3">
                                            <i class="bi bi-save2"></i>
                                            Save
                                        </button>
                                    </div>
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
                dropdownParent: $('#reviewForm .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });

            $('input[name=organizer]').on('change', function() {
                change_organizer($(this).val())
            })
        })

        function change_organizer(val) {
            if (val == 'school') {
                $("#schoolList").show()
                $("#corporateList").hide()
                $('#corporateList select').val(null).trigger('change')
            } else {
                $("#corporateList").show()
                $("#schoolList").hide()
                $('#schoolList select').val(null).trigger('change')
            }
        }

        @if (isset($edufair))
            function resetForm() {
                $('#reviewer_name').val(null).trigger('change')
                $('#score').val(null).trigger('change')
                tinyMCE.get('review').setContent('');
                $('.put').html('');
                let url = "{{ url('master/edufair/' . $edufair->id . '/review') }}"
                $('#formReview').attr('action', url)
            }
        @endif

        function getReview(eduf_id, reviews_id) {
            let link = '{{ url('master/edufair') }}/' + eduf_id + '/review/' + reviews_id

            axios.get(link)
                .then(function(response) {
                    let data = response.data.review
                    // console.log(data)
                    // handle success
                    $('#reviewer_name').val(data.reviewer_name).trigger('change')
                    $('#score').val(data.score).trigger('change')
                    tinyMCE.get('review').setContent(data.review);

                    let url = "{{ url('master/edufair/') }}/" + data.eduf_id + "/review/" + data.id
                    $('#formReview').attr('action', url)

                    let html = '@method('put')'
                    $('.put').html(html);
                })
                .catch(function(error) {
                    // handle error
                    console.log(error);
                })
        }
    </script>

    @if (old('organizer'))
    <script>
        change_organizer('{{ old('organizer') }}')
    </script>
    @endif

    @if (isset($edufair))
        <script>
            $(document).ready(function() {
                $('.modal-select').select2({
                    dropdownParent: $('#reviewForm .modal-content'),
                    placeholder: "Select value",
                    allowClear: true
                });

                $('input[name=organizer]').on('change', function() {
                    change_organizer($(this).val())
                })
            })

            function change_organizer(val) {
                if (val == 'school') {
                    $("#schoolList").show()
                    $("#corporateList").hide()
                    $('#corporateList select').val(null).trigger('change')
                } else {
                    $("#corporateList").show()
                    $("#schoolList").hide()
                    $('#schoolList select').val(null).trigger('change')
                }
            }
        </script>

        <script>
            var organizer = "{{ isset($edufair->sch_id) && $edufair->sch_id != null ? 'school' : 'corporate' }}"
            change_organizer(organizer)

            function resetForm() {
                $('#reviewer_name').val(null).trigger('change')
                $('#score').val(null).trigger('change')
                tinyMCE.get('review').setContent('');
                $('.put').html('');
                let url = "{{ url('master/edufair/' . $edufair->id . '/review') }}"
                $('#formReview').attr('action', url)
            }

            function getReview(eduf_id, reviews_id) {
                let link = '{{ url('master/edufair') }}/' + eduf_id + '/review/' + reviews_id

                axios.get(link)
                    .then(function(response) {
                        let data = response.data.review
                        // console.log(data)
                        // handle success
                        $('#reviewer_name').val(data.reviewer_name).trigger('change')
                        $('#score').val(data.score).trigger('change')
                        tinyMCE.get('review').setContent(data.review);

                        let url = "{{ url('master/edufair/') }}/" + data.eduf_id + "/review/" + data.id
                        $('#formReview').attr('action', url)

                        let html = '@method('put')'
                        $('.put').html(html);
                    })
                    .catch(function(error) {
                        // handle error
                        console.log(error);
                    })
            }
        </script>
    @endif

      @if (
        $errors->has('reviewer_name') |
            $errors->has('score') |
            $errors->has('review'))
        <script>
            $(document).ready(function() {
                $('#reviewForm').modal('show');
            })
        </script>
    @endif

    @if(!empty(old('organizer')))
        <script>
            $(document).ready(function(){
                $('input[name=organizer][value="{{old('organizer')}}"]').prop('checked', true).trigger('change')
            })
        </script>
    @endif

@endsection
