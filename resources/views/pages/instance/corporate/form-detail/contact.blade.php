<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-person me-2"></i>
                PIC
            </h6>
        </div>
        <div class="">
            <button class="btn btn-sm btn-outline-primary rounded mx-1" data-bs-toggle="modal" data-bs-target="#picForm">
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
                    <form action="{{ route('corporate.detail.store', ['corporate' => $corporate->corp_id]) }}"
                        id="detailForm" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">Full Name</label>
                                    <input type="text" name="pic_name" id="pic_name"
                                        class="form-control form-control-sm rounded">
                                    @error('name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">Email</label>
                                    <input type="email" name="pic_mail" id="pic_mail"
                                        class="form-control form-control-sm rounded">
                                    @error('email')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">Phone Number</label>
                                    <input type="text" name="pic_phone" id="pic_phone"
                                        class="form-control form-control-sm rounded">
                                    @error('phone')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">Linkedin</label>
                                    <input type="text" name="pic_linkedin" id="pic_linkedin"
                                        class="form-control form-control-sm rounded">
                                    @error('linkedin')
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
    function returnData(corporate_id, pic_id) {

        $("#detailForm").append('<input type="hidden" name="_method" value="PUT">');
        Swal.showLoading()
        let link = "{{ url('instance/corporate') }}/" + corporate_id + '/detail/' + pic_id

        axios.get(link)
            .then(function(response) {
                // handle success
                let data = response.data.data
                $('#pic_name').val(data.pic_name)
                $('#pic_mail').val(data.pic_mail)
                $('#pic_phone').val(data.pic_phone)
                $('#pic_linkedin').val(data.pic_linkedin)

                $('#detailForm').attr('action', '{{ url('instance/corporate') }}/' + corporate_id + '/detail/' +
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
            $("#detailForm").trigger('reset');
            $("#detailForm").attr('action',
                "{{ route('corporate.detail.store', ['corporate' => $corporate->corp_id]) }}")
            $("#detailForm").find('input[name=_method]').remove()
        }
    @endif
</script>
