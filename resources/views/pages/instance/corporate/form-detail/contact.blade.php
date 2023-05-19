<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-person me-2"></i>
                Contact
            </h6>
        </div>
        <div class="">
            <button class="btn btn-sm btn-outline-primary rounded mx-1" onclick="resetForm()" data-bs-toggle="modal" data-bs-target="#picForm">
                <i class="bi bi-plus"></i>
            </button>
        </div>
    </div>
    {{-- <div class="card-body"> --}}
        <div class="list-group list-group-flush overflow-auto">
            @if (isset($pics))
                @forelse ($pics as $pic)
                    <div class="list-group-item">
                        <div class="d-flex align-items-center mb-1">
                            @if ($pic->is_pic == true)
                                <i class="bi bi-star-fill me-2 text-warning"></i>
                            @endif
                            <strong class="text-muted me-2">
                                {{ $pic->pic_name }}
                            </strong>
                            <div class="">
                                <a href="#" onclick="returnData('{{ $corporate->corp_id }}','{{ $pic->id }}')"
                                    class="text-decoration-none" data-bs-target="#picForm">
                                    <i class="bi bi-pencil text-warning"></i>
                                </a>
                                <a href="#"
                                    onclick="confirmDelete('instance/corporate/{{ $corporate->corp_id }}/detail', '{{ $pic->id }}')"
                                    class="text-decoration-none">
                                    <i class="bi bi-trash2 text-danger"></i>
                                </a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            @if ($pic->pic_mail)
                            <div class="badge badge-success me-2">
                                <i class="bi bi-envelope me-1"></i> {{ $pic->pic_mail }}
                            </div>
                            @endif
                            @if ($pic->pic_phone)
                            <div class="badge badge-info me-2">
                                <i class="bi bi-phone me-1"></i> {{ $pic->pic_phone }}
                            </div>
                            @endif
                            @if ($pic->pic_linkedin)
                            <a href="{{ $pic->pic_linkedin }}" class="btn btn-sm btn-outline-primary rounded-circle">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="m-3">
                        No PIC yet
                    </div>

                @endforelse
            @endif
        </div>
    {{-- </div> --}}
</div>


@if (isset($corporate))
    <div class="modal modal-md fade" id="picForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0 p-0">
                        <i class="bi bi-plus me-2"></i>
                        Contact Person
                    </h4>
                </div>
                <div class="modal-body">
                    <form action="{{ route('corporate.detail.store', ['corporate' => $corporate->corp_id]) }}" id="picDetailForm" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">Full Name <sup class="text-danger">*</sup></label>
                                    <input type="text" name="pic_name" id="pic_fullname"
                                        class="form-control form-control-sm rounded" value="{{ old('pic_name') }}">
                                    @error('pic_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">Email</label>
                                    <input type="email" name="pic_mail" id="pic_mail"
                                        class="form-control form-control-sm rounded" value="{{ old('pic_mail') }}">
                                    @error('pic_mail')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">Phone Number <sup class="text-danger">*</sup></label>
                                    <input type="text" name="pic_phone" id="pic_phone" value="{{ old('pic_phone') }}"
                                        class="form-control form-control-sm rounded">
                                    @error('pic_phone')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">Linkedin</label>
                                    <input type="text" name="pic_linkedin" id="pic_linkedin" value="{{ old('pic_linkedin') }}"
                                        class="form-control form-control-sm rounded">
                                    @error('pic_linkedin')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">Is he/she is a PIC?</label>
                                    <input type="hidden" value="false" id="is_pic" name="is_pic">
                                    <div class="form-check ms-4">
                                        <input class="form-check-input" type="radio" name="pic_status" value="1" @checked(old('pic_status') == 1)>
                                        <label class="form-check-label">Yes</label>
                                    </div>
                                    <div class="form-check ms-4">
                                        <input class="form-check-input" type="radio" name="pic_status" value="0" @checked(old('pic_status') == 0) @checked(old('pic_status') !== null)>
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
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

<script type="text/javascript">
    $(document).ready(function() {

        @if ($errors->has('pic_name') || $errors->has('pic_phone') || $errors->has('pic_mail'))
            $("#picForm").modal('show');
        @endif

        $("input[type=radio][name=pic_status]").change(function() {
            var val = $(this).val();
            $("#is_pic").val(val);
        })
    })

    function resetForm()
    {
        $("#picDetailForm").reset();
    }

    function returnData(corporate_id, pic_id) {

        $("#picDetailForm").append('<input type="hidden" name="_method" value="PUT">');
        Swal.showLoading()
        let link = "{{ url('instance/corporate') }}/" + corporate_id + '/detail/' + pic_id

        axios.get(link)
            .then(function(response) {
                // handle success
                let data = response.data.data
                $('#pic_fullname').val(data.pic_name)
                $('#pic_mail').val(data.pic_mail)
                $('#pic_phone').val(data.pic_phone)
                $('#pic_linkedin').val(data.pic_linkedin)
                $('#is_pic').val(data.is_pic)
                $('input[type=radio][name=pic_status][value=' + data.is_pic + ']').prop('checked', true);

                $('#picDetailForm').attr('action', '{{ url('instance/corporate') }}/' + corporate_id + '/detail/' +
                    data.id)
                Swal.close()
                $("#picForm").modal('show')
            })
            .catch(function(error) {
                // handle error
                Swal.close()
                notification(error.response.data.success, error.response.data.message)
            })
    }

    @if (isset($corporate))
        function resetForm() {
            $("#picDetailForm").trigger('reset');
            $("#picDetailForm").attr('action',
                "{{ route('corporate.detail.store', ['corporate' => $corporate->corp_id]) }}")
            $("#picDetailForm").find('input[name=_method]').remove()
        }
    @endif
</script>
