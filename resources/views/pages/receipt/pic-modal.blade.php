<div class="modal fade" id="requestSignModal" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <span>
                    Request Sign
                </span>
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="modal-body w-100 text-start">
                Please direct your request for a signature to these people, whichever is available :

                <div class="mb-4">
                    <div class="d-flex justify-content-around">
                        @foreach ($invRecPics as $pic)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pic_sign" id="pic-{{ $pic['name'] }}" data-name="{{ $pic['name'] }}" value="{{ $pic['email'] }}">
                            <label class="form-check-label" for="pic-{{ $pic['name'] }}">{{ $pic['name'] }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" href="#" class="btn btn-outline-danger btn-sm"
                        data-bs-dismiss="modal">
                        <i class="bi bi-x-square me-1"></i>
                        Cancel</button>
                    <button type="submit" id="sendToChoosenPic" class="btn btn-primary btn-sm">
                        <i class="bi bi-save2 me-1"></i>
                        Send</button>
                </div>
            </div>
        </div>
    </div>
</div>