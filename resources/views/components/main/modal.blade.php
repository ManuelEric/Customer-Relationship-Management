{{-- Delete Item  --}}
<div class="modal modal-sm fade" tabindex="-1" id="deleteItem" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formAction">
                @csrf
                @method('delete')
                <div class="modal-body text-center">
                    <h2>
                        <i class="bi bi-info-circle text-info"></i>
                    </h2>
                    <h4>Are you sure?</h4>
                    <h6>You want to delete this data?</h6>
                    <hr>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-square me-1"></i>
                        Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-trash3 me-1"></i>
                        Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Deactive User Item  --}}
<div class="modal modal-sm fade" tabindex="-1" id="deactiveUser" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formActionDeactive">
                @csrf
                @method('delete')
                <div class="modal-body text-center">
                    <h2>
                        <i class="bi bi-info-circle text-info"></i>
                    </h2>
                    <h4>Are you sure?</h4>
                    <h6>You want to deactive this user?</h6>
                    <hr>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-square me-1"></i>
                        Cancel</button>
                    <button type="button" id="deactivate-user--app-3103" class="btn btn-primary btn-sm">
                        <i class="bi bi-trash3 me-1"></i>
                        Yes!</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Restore Client & Instance --}}
<div class="modal modal-sm fade" tabindex="-1" id="restoreModal" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formRestore">
                @csrf
                @method('put')
                <div class="modal-body text-center">
                    <h2>
                        <i class="bi bi-info-circle text-info"></i>
                    </h2>
                    <h4>Are you sure?</h4>
                    <h6>You want to restore?</h6>
                    <hr>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-square me-1"></i>
                        Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-trash3 me-1"></i>
                        Yes!</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Request Sign  --}}
<div class="modal modal-sm fade" tabindex="-1" id="requestSign--modal" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formActionRequestSign">
                @csrf
                @method('delete')
                <div class="modal-body text-center">
                    <h2>
                        <i class="bi bi-info-circle text-info"></i>
                    </h2>
                    <h4>Are you sure?</h4>
                    <h6><!-- warning text here --></h6>
                    <hr>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-square me-1"></i>
                        Cancel</button>
                    <button type="button" id="send-request--app-2908" class="btn btn-primary btn-sm">
                        <i class="bi bi-trash3 me-1"></i>
                        Yes!</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Send Invoice / Receipt to Client  --}}
<div class="modal modal-sm fade" tabindex="-1" id="sendToClient--modal" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formActionSendToClient">
                @csrf
                @method('delete')
                <div class="modal-body text-center">
                    <h2>
                        <i class="bi bi-info-circle text-info"></i>
                    </h2>
                    <h4>Are you sure?</h4>
                    <h6><!-- warning text here --></h6>
                    <hr>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-square me-1"></i>
                        Cancel</button>
                    <button type="button" id="send-to-client--app-0604" class="btn btn-primary btn-sm">
                        <i class="bi bi-trash3 me-1"></i>
                        Yes!</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Update Lead Status  --}}
<div class="modal modal-sm fade" tabindex="-1" id="updateLeadStatus" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h2>
                    <i class="bi bi-info-circle text-info"></i>
                </h2>
                <h4>Are you sure?</h4>
                <h6>You want to update this data?</h6>
                <input type="hidden" value="" id="statusLeadOld">
                <input type="hidden" value="" id="clientLeadId">
                <hr>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="closeModalLeadConfirm()">
                    <i class="bi bi-x-square me-1"></i>
                    Cancel</button>
                <button type="button" id="btn-update-lead" class="btn btn-primary btn-sm">
                    <i class="bi bi-box-arrow-in-down me-1"></i>
                    Yes, Update</button>
            </div>
            {{-- </form> --}}
        </div>
    </div>
</div>