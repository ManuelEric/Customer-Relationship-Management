<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 p-0">School Visit</h6>
        <button class="btn btn-sm btn-outline-primary rounded" data-bs-toggle="modal" data-bs-target="#school_visit">
            <i class="bi bi-plus"></i>
        </button>
    </div>
    <div class="card-body p-2 overflow-auto" style="max-height: 200px">
        <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div class="">
                    <p class="p-0" style="margin-bottom:-5px">
                        Internal PIC - School PIC
                    </p>
                    <small class="p-0 text-success">Meeting Date</small>
                </div>
                <div class="cursor-pointer" onclick="confirmDelete()">
                    <i class="bi bi-trash2 text-danger"></i>
                </div>
            </li>
        </ul>
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
                <form action="" method="POST">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="" id="">
                        <div class="col-md-6 mb-2">
                            <label for="">Internal PIC</label>
                            <select name="" class="modal-select w-100"></select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">School PIC</label>
                            <select name="" class="modal-select w-100"></select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Visit Date</label>
                            <input type="date" name="" id=""
                                class="form-control form-control-sm rounded">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Notes</label>
                            <textarea name="" id="" cols="30" rows="10"></textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="p-3 d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-sm btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.modal-select').select2({
            dropdownParent: $('#school_visit .modal-content'),
            placeholder: "Select value",
            allowClear: true
        });
    });
</script>
