<div class="card rounded mb-2">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="m-0 p-0">Parents Information</h5>
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addParent"><i
                    class="bi bi-plus"></i></button>
        </div>
    </div>
    <div class="card-body" style="overflow: auto;">
        @if ($student->parents()->count() > 0)
            <table class="table table-bordered" id="list-parent">
                <thead>
                    <tr class="text-center">
                        <th>No</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @foreach ($student->parents as $parent)
                        <tr align="center" style="cursor: pointer;" class="detail" data-parentid="{{ $parent->id }}">
                            <td>{{ $no++ }}</td>
                            <td>{{ $parent->fullname }}</td>
                            <td>{{ $parent->mail }}</td>
                            <td>{{ $parent->phone }}</td>
                            <td>
                                <button class="btn btn-light text-danger" data-bs-toggle="tooltip"
                                    data-bs-title="Disconnect Parent"
                                    onclick="confirmDelete('client/student/{{ $student->id }}/parent', '{{ $parent->id }}')">
                                    <i class="bi bi-dash-circle-fill"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            There's no parent information yet
        @endif
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addParent" tabindex="-1" aria-labelledby="addParentLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addParentLabel">Add Parent</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('student.add.parent', ['student' => $student->id]) }}" method="post">
                    @csrf
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label>Existing Parent <sup class="text-danger">*</sup></label>
                            <div class="d-flex align-items-center" id="div-exist">
                                <div class="form-check ms-4">
                                    <input class="form-check-input exist" type="radio" name="existing_parent"
                                        id="exist1" value="1" checked onchange="checkExist(this)">
                                    <label class="" for="exist1">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check ms-5">
                                    <input class="form-check-input exist" type="radio" name="existing_parent"
                                        id="exist2" value="0" onchange="checkExist(this)">
                                    <label class="" for="exist2">
                                        No
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="existParent">
                            <label for="">Parent's Name</label>
                            <select class="modal-select3 w-100" name="pr_id" id="prName">
                                <option data-placeholder="true"></option>
                                @if (isset($parents))
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}"
                                            {{ old('pr_id') == $parent->id ? 'selected' : null }}>
                                            {{ $parent->first_name . ' ' . $parent->last_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('pr_id')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div id="newParent" class="d-none">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="">First Name</label>
                                    <input name="first_name" id="" type="text"
                                        value="{{ old('first_name') }}" class="form-control form-control-sm">
                                    @error('first_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="">Last Name</label>
                                    <input name="last_name" id="" type="text"
                                        value="{{ old('last_name') }}" class="form-control form-control-sm">
                                    @error('last_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">Email</label>
                                    <input type="email" name="mail" value="{{ old('mail') }}"
                                        class="form-control form-control-sm">
                                    @error('email')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="">Phone Number</label>
                                    <input type="text" name="phone" {{ old('phone') }}
                                        class="form-control form-control-sm">
                                    @error('phone')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <button class="btn btn-sm btn-primary">
                            <i class="bi bi-save"></i>
                            Save changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function checkExist(radio) {
        let exist = radio.value
        if (exist == 1) {
            $('#existParent').removeClass('d-none')
            $('#newParent').addClass('d-none')
        } else {
            $('#existParent').addClass('d-none')
            $('#newParent').removeClass('d-none')
        }
    }

    $("#list-parent .detail").each(function() {
        $(this).click(function() {
            
            var link = "{{ url('client/parent') }}/" + $(this).data('parentid')
            window.open(link, "_blank")
        })
    })
</script>

@if ((string) old('existing_parent') == '0')
    <script>
        $(document).ready(function() {
            $('input[name=existing_parent][value="{{ old('existing_parent') }}"]').prop('checked', true).trigger(
                'change')
        })
    </script>
@endif


@if (
    $errors->has('existing_parent') ||
        $errors->has('pr_id') ||
        $errors->has('first_name') ||
        $errors->has('last_name') ||
        $errors->has('mail') ||
        $errors->has('phone'))
    <script>
        $(document).ready(function() {
            $('#addParent').modal('show');
        })
    </script>
@endif
