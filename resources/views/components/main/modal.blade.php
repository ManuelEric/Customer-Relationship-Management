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

<!-- Birthday -->
<div class="modal modal-lg fade" id="birthday" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Mentee's Birthday</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ">
                <div class="row justify-content-end mb-2">
                    <div class="col-md-3">
                    </div>
                </div>
                <div class="overflow-auto" style="height: 400px">
                    <table class="table table-striped table-hover" id="menteesBirthdayTable">
                        <thead class="text-center">
                            <tr class="text-white">
                                <th class='bg-secondary rounded border border-white'>No</th>
                                <th class='bg-secondary rounded border border-white'>Mentee's Name</th>
                                <th class='bg-secondary rounded border border-white'>Birthday</th>
                                <th class='bg-secondary rounded border border-white'>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($birthDay as $mentee)
                                <tr class="text-left">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $mentee->full_name }}</td>
                                    <td>{{ date('D, d M Y', strtotime($mentee->dob)) }}</td>
                                    <td>{{ strip_tags($mentee->address) }}</td>
                                </tr>
                            @empty
                                <tr class="text-left">
                                    <td class="text-center" col-span="3">No data yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Follow Up -->
<div class="modal modal-lg fade" id="follow_up" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Follow Up Reminder</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-body">
                @foreach ($followUp as $key => $detail)
                    <h6>
                        @php
                            $opener = '(';
                            $closer = ')';
                        @endphp
                        @switch(date('d', strtotime($key))-date('d'))
                            @case(0)
                                Today
                            @break

                            @case(1)
                                Tomorrow
                            @break

                            @case(2)
                                The day after tomorrow
                            @break

                            @default
                                @php
                                    $opener = null;
                                    $closer = null;
                                @endphp
                        @endswitch
                        {{ $opener . date('D, d M Y', strtotime($key)) . $closer }}
                    </h6>
                    <div class="overflow-auto mb-3">
                        <ul class="list-group">
                            @foreach ($detail as $info)
                            @if ($info['type'] == 'followup-client-program')
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ url('client/student/' . $info['clientProgram']->client->id . '/program/' . $info['clientProgram']->clientprog_id) }}"
                                        class="text-decoration-none" target="_blank">
                                        <p class="m-0 p-0 lh-1">{{ $info['clientProgram']->client->full_name }}</p>
                                        <small
                                            class="m-0">{{ $info['clientProgram']->program->program_name }}</small>
                                    </a>
                                    <div class="">
                                        <input class="form-check-input me-1" type="checkbox" value="1"
                                            @checked($info['status'] == 1) id="mark_{{ $loop->index }}"
                                            data-student="{{ $info['clientProgram']->client->id }}"
                                            data-program="{{ $info['clientProgram']->clientprog_id }}"
                                            data-followup="{{ $info['id'] }}"
                                            onchange="marked({{ $loop->index }})">
                                        <label class="form-check-label"
                                            for="mark_{{ $loop->index }}">Done</label>
                                    </div>
                                </li>
                            @else
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ url('client/student/'.$info['client']->id.'/') }}" class="text-decoration-none" target="_blank">
                                        <p class="m-0 p-0 lh-1">{{ ucwords($info['client']->full_name) }}</p>
                                    </a>
                                </li>
                            @endif
                            @endforeach
                        </ul>
                    </div>
                    <hr>
                @endforeach

            </div>
        </div>
    </div>
</div>

{{-- Follow Up Notes  --}}
<div class="modal modal-md fade" id="follow_up_notes" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="exampleModalLabel">Follow Up Notes</h5>
            </div>
            <div class="modal-body ">
                <form action="" method="POST" id="followUpForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" class="marked_id">
                    <textarea name="new_notes" id="" cols="30" rows="10"></textarea>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="cancelMarked()">Cancel</button>
                        <button type="submit" id="btn-submit-followup"
                            class="btn btn-sm btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Cancel Follow Up  --}}
<div class="modal modal-md fade" id="cancel_follow_up_notes" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="exampleModalLabel">Cancel Follow Up Mark</h5>
            </div>
            <div class="modal-body ">
                <form action="" method="POST" id="cancelFollowUpForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" class="marked_id">
                    Are you sure, you want to cancel this follow up?
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="backMarked()">No</button>
                        <button type="submit" id="btn-cancel-followup" class="btn btn-sm btn-primary">Yes,
                            Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>