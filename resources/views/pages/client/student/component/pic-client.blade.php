<div class="card rounded mb-2">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="m-0 p-0">PIC</h5>
            @if($isSalesAdmin || $isSuperAdmin)
                @if (!isset($student->pic_client))
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalPICclient"><i
                            class="bi bi-plus"></i></button>
                @else
                    <button class="btn btn-warning btn-sm" id="btn-edit-pic"><i class="bi bi-pencil"></i></button>
                @endif
            @endif
        </div>
    </div>
    <div class="card-body overflow-auto" style="max-height: 150px;">

        @if (isset($student->pic_client))
            <div class="d-flex align-items-center justify-content-between w-100 ">
                <a href="" class="text-decoration-none text-info" style="font-size:12px;">
                    <div>
                        <i class="bi bi-arrow-right"></i>
                        {{ $student->pic_client->full_name }}
                    </div>
                </a>
            </div>
        @else
            There's no PIC yet
        @endforelse
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalPICclient" tabindex="-1" aria-labelledby="modalPICclientLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalPICclientLabel">PIC</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="formAssign">
                    @csrf
                    <label for="">Assign</label>
                    <select name="pic" id="select-pic" class="modal-select w-100">
                        <option data-placeholder="true"></option>
                        @foreach ($salesTeams as $salesTeam)
                            <option value="{{ $salesTeam->id }}" {{ old('pic') == $salesTeam->id ? 'selected' : null }}>
                                {{ $salesTeam->full_name }}</option>
                        @endforeach
                    </select>
                    @error('pic')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                    <div class="mt-3 text-center">
                        <button type="button" id="btnSubmit" class="btn btn-sm btn-primary">
                            <i class="bi bi-save"></i>
                            Save changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if ($errors->has('pic'))
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#modalPICclient').modal('show');
            })
        </script>
    @endpush
@endif

<script>
    @if (isset($student->pic_client))
        $("#btn-edit-pic").on('click', function(e) {
            $('#modalPICclient').modal('show');
            $('#select-pic option[value="{{ $student->pic_client->id }}"]').prop('selected', 'selected')
                .change();

        })
    @endif

    $("#btnSubmit").click(function() {
        showLoading();

        var pic_id = $('#select-pic').val();
        var student = ['{{ $student->id }}'];

        var link = '{{ route('client.bulk.assign') }}';
        axios.post(link, {
                choosen: student,
                pic_id: pic_id
            })
            .then(function(response) {
                swal.close();
                notification('success', response.data.message);
                $('#modalPICclient').modal('hide');
                setTimeout(function() {
                    location.reload();
                }, 3000);
            })
            .catch(function(error) {
                swal.close();
                notification('error', error.message);
                $('#modalPICclient').modal('hide');
            })
    });
</script>
