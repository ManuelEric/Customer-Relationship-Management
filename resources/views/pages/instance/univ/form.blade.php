@extends('layout.main')

@section('title', 'University Detail')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Universities</a></li>
    <li class="breadcrumb-item active" aria-current="page">Form University</li>
@endsection
@section('content')
    @php
        $error_pic = false;
    @endphp
    @if (
        $errors->has('name') ||
            $errors->has('email') ||
            $errors->has('phone') ||
            $errors->has('title') ||
            $errors->has('other_title') ||
            $errors->has('is_pic'))
        @php
            $error_pic = true;
        @endphp
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <img loading="lazy"  src="{{ asset('img/school.webp') }}" alt="" class="w-75">

                    @if (isset($university))
                        <div class="mt-3 d-flex justify-content-center">
                            @if (isset($edit))
                                <a href="{{ url('instance/university/' . strtolower($university->univ_id)) }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>
                            @else
                                <a href="{{ url('instance/university/' . strtolower($university->univ_id) . '/edit') }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            @endif
                            <button type="button"
                                onclick="confirmDelete('instance/university', '{{ $university->univ_id }}')"
                                class="btn btn-sm btn-outline-danger rounded mx-1">
                                <i class="bi bi-trash2"></i> Delete
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            @if (isset($university) && empty($edit))
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
                            @forelse ($university->events as $event)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="">
                                            <strong>{{ $event->event_title }}</strong> <br>
                                            {{ date('M d, Y', strtotime($event->event_startdate)) }}
                                            ({{ date('H:i', strtotime($event->event_startdate)) }})
                                            -
                                            {{ date('M d, Y', strtotime($event->event_enddate)) }}
                                            ({{ date('H:i', strtotime($event->event_enddate)) }})
                                        </div>
                                        <div class="">
                                            <a href="{{ route('event.show', ['event' => $event->event_id]) }}"
                                                class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                No Event
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-building me-2"></i>
                            University Detail
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <form
                        action="{{ url(isset($university) ? 'instance/university/' . $university->univ_id : 'instance/university') }}"
                        method="POST" id="formUniv">
                        @csrf
                        @if (isset($university))
                            @method('put')
                        @endif
                        <div class="put"></div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        University Name <i class="text-danger font-weight-bold">*</i>
                                    </label>
                                    <input type="text" name="univ_name" class="form-control form-control-sm rounded"
                                        value="{{ isset($university) ? $university->univ_name : old('univ_name') }}"
                                        {{ empty($university) || isset($edit) ? '' : 'disabled' }}>
                                    @error('univ_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Email
                                    </label>
                                    <input type="text" name="univ_email" class="form-control form-control-sm rounded"
                                        value="{{ isset($university) ? $university->univ_email : old('univ_email') }}"
                                        {{ empty($university) || isset($edit) ? '' : 'disabled' }}>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Phone Number
                                    </label>
                                    <input type="text" name="univ_phone" class="form-control form-control-sm rounded"
                                        value="{{ isset($university) ? $university->univ_phone : old('univ_phone') }}"
                                        {{ empty($university) || isset($edit) ? '' : 'disabled' }}>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="mb-2">
                                    <label for="">
                                        Country <i class="text-danger font-weight-bold">*</i>
                                    </label>
                                    <div class="w-100">
                                        <select name="univ_country" id="univ_country" class="select w-100"
                                            {{ empty($university) || isset($edit) ? '' : 'disabled' }}>
                                            <option data-placeholder="true"></option>
                                            @foreach ($countries as $item)
                                                <option value="{{ $item->name }}"
                                                    {{ (isset($university) ? $university->univ_country : old('univ_country')) == $item->name ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('univ_country')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-2">
                                    <label for="">Tags <i class="text-danger font-weight-bold">*</i></label>
                                    <select name="tag" class="select w-100"
                                        {{ empty($university) || isset($edit) ? '' : 'disabled' }}>
                                        <option data-placeholder="true"></option>
                                        @forelse ($tags as $tag)
                                            <option value="{{ $tag->id }}"
                                                @if (isset($university)) {{ $university->tag == $tag->id ? 'selected' : null }}
                                                @elseif (old('tag'))
                                                    {{ old('tag') == $tag->id ? 'selected' : null }} @endif>
                                                {{ $tag->name }}</option>
                                        @empty
                                            <option>No Tag</option>
                                        @endforelse
                                    </select>
                                    @error('tag')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Address
                                    </label>
                                    <textarea name="univ_address" id="univ_address" class="form-control form-control-sm rounded" style="height: 300px">{{ isset($university) ? $university->univ_address : old('univ_address') }}</textarea>
                                    @error('univ_address')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            @if (empty($university) || isset($edit))
                                <div class="col-md-12 mt-2">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="bi bi-save2 me-1"></i>
                                            Submit</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            @if (isset($university) && empty($edit))
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="">
                            <h6 class="m-0 p-0">
                                <i class="bi bi-building me-2"></i>
                                Contact Person
                            </h6>
                        </div>
                        <div class="">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#picForm" onclick="resetForm()">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Position</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($pics) && count($pics) > 0)
                                    @php
                                        $no = 1;
                                    @endphp
                                    @foreach ($pics as $pic)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $pic->name }}</td>
                                            <td>{{ $pic->email }}</td>
                                            <td>{{ $pic->phone }}</td>
                                            <td>{{ $pic->title }}</td>
                                            <td class="text-end">
                                                @if ($pic->is_pic == true)
                                                    <i class="bi bi-star-fill me-2 my-2 text-warning" title="PIC"></i>
                                                @endif
                                                <button class="btn btn-sm btn-outline-warning" data-bs-target="#picForm"
                                                    onclick="getPIC('{{ url('instance/university/' . $pic->univ_id . '/detail/' . $pic->id . '/edit') }}')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDelete('instance/university/{{ $pic->univ_id }}/detail', '{{ $pic->id }}')">
                                                    <i class="bi bi-trash2"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr align="center">
                                        <td colspan="6">No Data</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>


                {{-- Modal Add & Update Contact Person --}}
                <div class="modal modal-md fade" id="picForm" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="m-0 p-0">
                                    <i class="bi bi-plus me-2"></i>
                                    Contact Person
                                </h4>
                            </div>
                            <div class="modal-body">
                                <form
                                    action="{{ route('university.detail.store', ['university' => $university->univ_id]) }}"
                                    method="POST" id="picAction">
                                    @csrf
                                    <div class="put"></div>
                                    <div class="row mb-2">
                                        <div class="col-md-12 mb-2">
                                            <label>Fullname <sup class="text-danger">*</sup></label>
                                            <input type="text" name="name"
                                                class="form-control form-control-sm rounded" id="cp_fullname">
                                            @error('name')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <label>E-mail <sup class="text-danger">*</sup></label>
                                            <input type="email" name="email"
                                                class="form-control form-control-sm rounded" id="cp_mail">
                                            @error('email')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <label>Phone Number <sup class="text-danger">*</sup></label>
                                            <input type="text" name="phone"
                                                class="form-control form-control-sm rounded" id="cp_phone">
                                            @error('phone')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <label>Position <sup class="text-danger">*</sup></label>
                                            <div class="classPosition">
                                                <select name="title" class="modal-select w-100"
                                                    style="display: none !important" id="selectPosition"
                                                    onchange="changePosition($(this).val())">
                                                    <option data-placeholder="true"></option>
                                                    <option value="Admissions Advisor">
                                                        Admissions Advisor</option>
                                                    <option value="Former Admission Officer">
                                                        Former Admission Officer</option>
                                                    <option value="new">
                                                        New Position</option>
                                                </select>
                                            </div>

                                            <div class="d-flex align-items-center d-none" id="inputPosition">
                                                <input type="text" name="other_title"
                                                    class="form-control form-control-sm rounded">
                                                <div class="float-end cursor-pointer" onclick="resetPosition()">
                                                    <b>
                                                        <i class="bi bi-x text-danger"></i>
                                                    </b>
                                                </div>
                                            </div>

                                            @error('title')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                            @error('other_title')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-2">
                                            <label for="">Is he/she is a PIC?</label>
                                            <input type="hidden" value="false" id="is_pic" name="is_pic">
                                            <div class="form-check ms-4">
                                                <input class="form-check-input" type="radio" name="pic_status"
                                                    value="1" @checked(old('pic_status') == 1)>
                                                <label class="form-check-label">Yes</label>
                                            </div>
                                            <div class="form-check ms-4">
                                                <input class="form-check-input" type="radio" name="pic_status"
                                                    value="0" @checked(old('pic_status') == 0)
                                                    @checked(old('pic_status') !== null)>
                                                <label class="form-check-label">No</label>
                                            </div>
                                            @error('is_pic')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-2">
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                                data-bs-dismiss="modal">
                                                <i class="bi bi-x me-1"></i>
                                                Cancel
                                            </button>
                                            <button type="submit" class="btn btn-sm btn-primary rounded-3">
                                                <i class="bi bi-save2"></i>
                                                Save
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <script>
        function selectModal() {
            $('.modal-select').select2({
                dropdownParent: $('#picForm .modal-content'),
                placeholder: "Select value",
                containerCssClass: "show-hide",
                allowClear: true
            });
        }

        function changePosition(value) {

            if (value == 'new') {
                $('.classPosition').addClass('d-none')
                $('#inputPosition').removeClass('d-none')
                $('#inputPosition input').focus()
            } else {
                $('#inputPosition').addClass('d-none')
                $('.classPosition').removeClass('d-none')
            }
        }

        function resetPosition() {
            $('.classPosition').removeClass('d-none')
            $('#selectPosition').val(null).trigger('change')
            $('#inputPosition').addClass('d-none')
            $('#inputPosition input').val(null)
        }

        $(document).ready(function() {
            @if ($error_pic === true)
                $("#picForm").modal('show')
            @endif
            selectModal()

            $("input[type=radio][name=pic_status]").change(function() {
                var val = $(this).val();
                $("#is_pic").val(val);
            })
        });
    </script>
    <script>
        @if (isset($university))
            function resetForm() {
                $("#picAction").trigger('reset');
                $("#selectPosition").val('').trigger('change')
                $('.put').html('');

                $('#picAction').attr('action',
                    "{{ isset($university) ? url('instance/university/' . $university->univ_id . '/detail') : url('instance/university/') }}"
                )
            }
        @endif

        function getPIC(link) {

            Swal.showLoading()
            axios.get(link)
                .then(function(response) {
                    // handle success
                    let id = response.data.univ_id
                    let cp = response.data.picDetail
                    $('#cp_fullname').val(cp.name)
                    $('#cp_mail').val(cp.email)
                    $('#cp_phone').val(cp.phone)
                    $('#selectPosition').val(cp.title).trigger('change')
                    $('#is_pic').val(cp.is_pic)
                    $('input[type=radio][name=pic_status][value=' + cp.is_pic + ']').prop('checked', true);

                    let array = ["Admissions Advisor", "Former Admission Officer"]
                    if ($.inArray(cp.title, array) == -1) {
                        changePosition('new')
                        $("#selectPosition").val('new').trigger('change')
                        $("input[name=other_title]").val(cp.title)
                    }

                    let url = "{{ url('instance/university/') }}/" + id + "/detail/" + cp.id
                    $('#picAction').attr('action', url)

                    let html =
                        '@method('put')' +
                        '<input type="hidden" readonly name="schdetail_id" value="' + cp.id + '">'
                    $('.put').html(html);

                    Swal.close()
                    $("#picForm").modal('show')

                    // console.log(url)
                })
                .catch(function(error) {
                    // handle error
                    Swal.close()
                    notification(error.response.data.success, error.response.data.message)
                })
        }
    </script>
@endsection
