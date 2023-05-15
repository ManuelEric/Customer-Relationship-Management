<div class="card rounded mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-building me-2"></i>
                University
            </h6>
        </div>
        <div class="">
            <button class="btn btn-sm btn-outline-primary rounded mx-1" data-bs-toggle="modal" data-bs-target="#univ-collaborator">
                <i class="bi bi-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        @if ($collaborators_univ->count() > 0)
            @foreach ($collaborators_univ as $university)
                <div class="list-group">
                    <div class="d-flex list-group-item justify-content-between">
                        <div class="">
                            {{ $university->univ_name }}
                        </div>
                        <div class="" style="cursor:pointer" onclick="confirmDelete('program/corporate/{{ $corpId }}/detail/{{ $corp_ProgId }}/collaborators/school', '{{ $university->univ_id }}')">
                            <i class="bi bi-trash2 text-danger"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            No university collaborator yet
        @endif
    </div>
</div>

<div class="modal fade" id="univ-collaborator" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <span>
                    University
                </span>
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="modal-body w-100 text-start">
                <form action="{{ route('corporate_prog.collaborators.store', [
                    'corp' => $corpId,
                    'corp_prog' => $corp_ProgId,
                    'collaborators' => 'university'
                ]) }}" method="POST" id="formPosition">
                    @csrf
                    <div class="put"></div>
                    <div class="row g-2">
                        <div class="col-md-12">
                            <div class="mb-2">
                                <label for="">
                                    University Name <sup class="text-danger">*</sup>
                                </label>
                                <select name="univ_id" class="modal-select-univ w-100">
                                    <option data-placeholder="true"></option>
                                    @if (isset($universities))
                                        @foreach ($universities as $university)
                                            <option value="{{ $university->univ_id }}">{{ $university->univ_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('univ_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
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
