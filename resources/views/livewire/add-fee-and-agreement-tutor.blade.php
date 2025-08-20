<div>
    @if (session()->has('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @elseif (session()->has('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="">
                <h6 class="m-0 p-0">
                    <i class="bi bi-building me-2"></i>
                    Agreement
                </h6>
            </div>
            <div class="">
                <button class="btn btn-sm btn-outline-primary rounded mx-1" data-bs-toggle="modal" data-bs-target="#agreementForm">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body overflow-auto">
            <table class="table table-bordered table-hover nowrap align-middle w-100">
                <thead class="bg-dark text-white">
                    <tr class="text-center">
                        <td>#</td>
                        <td>Active Date</td>
                        <td>To</td>
                        <td>Subject</td>
                        <td>Curriculum</td>
                        <td>Grade</td>
                        <td>Fee Individual</td>
                        <td>Fee Group</td>
                        {{-- <td>Head Count <i class="bi bi-question-circle" alt="group fee will be activated when total minimum of head count achieved"></i></td> --}}
                        <td>Agreement</td>
                        <td>Last Update</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse($user->user_subjects as $role_subject)
                        @php
                            $file_path = 'project/crm/user/'.$user->id.'/'.$role_subject->agreement;
                            $url = Storage::disk('s3')->temporaryUrl($file_path, now()->addMinutes(5));
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}.</td>
                            <td class="text-center">{{ $role_subject->start_date ? date('d, M Y', strtotime($role_subject->start_date)) : 'n/a' }}</td>
                            <td class="text-center">{{ $role_subject->end_date ? date('d, M Y', strtotime($role_subject->end_date)) : 'n/a' }}</td>
                            <td>{{ $role_subject->subject->name ?? 'n/a' }}</td>
                            <td>{{ $role_subject->curriculum }}</td>
                            <td class="text-center">{{ $role_subject->grade ?? 'n/a' }}</td>
                            <td>{{ $role_subject->fee_individual ? "Rp. ".number_format($role_subject->fee_individual) : 'n/a' }}</td>
                            <td>{{ $role_subject->fee_group ? "Rp. ".number_format($role_subject->fee_group) : 'n/a' }}</td>
                            {{-- <td class="text-center">{{ $role_subject->head ?? 'n/a' }}</td> --}}
                            <td class="text-center">{!! $role_subject->agreement ? "<a href='{$url}' target='_blank'>view</a>" : 'n/a' !!}</td>
                            <td>{{ date('d F Y H:i:s', strtotime($role_subject->updated_at)) }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-warning" wire:click="edit({{ $role_subject->id }})" data-bs-toggle="modal" data-bs-target="#agreementForm"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-danger ms-1"
                                x-data
                                @click.prevent="if (confirm('Are you sure you want to delete? This cannot be undone.')) { $wire.delete({{ $role_subject->id }}) }"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No data</td>
                        </tr>

                    @endforelse
                </tbody>
            </table>

            <div wire:loading wire:target="delete" class="text-center">Loading...</div>
        </div>
    </div>

    <div class="modal modal-xl fade" id="agreementForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0 p-0">
                        <i class="bi bi-plus me-2"></i>
                        Add an Agreement
                    </h4>
                </div>
                <div class="modal-body">
                    <form id="live" wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="">Subject <sup class="text-danger">*</sup></label>
                                <select wire:model="subject_id" @if(!$isEdit) multiple @endif class="form-select form-select-sm w-100">
                                    @forelse ($tutor_subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>

                                    @empty
                                        <option>No subjects fetched</option>
                                    @endforelse
                                </select>
                                @error('subject_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="">Curriculum <sup class="text-danger">*</sup></label>
                                <select wire:model="selectedCurriculums" @if(!$isEdit) multiple @endif class="form-select form-select-sm w-100">
                                    <option value="null">No Curriculum</option>
                                    @forelse ($curriculums as $key => $curriculum)
                                        <option value="{{ $curriculum }}">{{ $curriculum }}</option>

                                    @empty
                                        <option>No curriculum fetched</option>
                                    @endforelse
                                </select>
                                @error('selectedCurriculums')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div>
                                <div class="row px-3 py-2">
                                    <fieldset class="border p-3">
                                        <legend  class="float-none ps-2" style="width:80px; font-size: 14px; font-weight:500">Duration</legend>
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <label for="">From <sup class="text-danger">*</sup></label>
                                                <input type="date" wire:model="start_date" class="form-control form-control-sm rounded" />
                                                @error('start_date')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label for="">To <sup class="text-danger">*</sup></label>
                                                <input type="date" wire:model="end_date" class="form-control form-control-sm rounded" />
                                                @error('end_date')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>



                            <div class="col-md-2 mb-2">
                                <label for="">Grade</label>
                                <select wire:model="grade" class="form-select form-select-sm">
                                    <option data-placeholder="true"></option>
                                    <option value="9-12">All</option>
                                    <option value="1-6">1-6</option>
                                    <option value="9-10">9-10</option>
                                    <option value="11-12">11-12</option>
                                </select>
                                @error('grade')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="">Fee Individual (Gross) <sup class="text-danger">*</sup></label>
                                <input class="form-control form-control-sm rounded" type="number" wire:model="fee_individual" placeholder="230625">
                                @error('fee_individual')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="">Fee Group (Gross) </label>
                                <input class="form-control form-control-sm rounded" type="number" wire:model="fee_group" placeholder="256250">
                                @error('fee_group')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            {{-- <div class="col-md-6 mb-2">
                                <label for="">Additional Fee </label>
                                <input class="form-control form-control-sm rounded" type="text" name="additional_fee[]">
                                @error('additional_fee.0')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div> --}}
                            {{-- <div class="col-md-2 mb-2">
                                <label for="">Head Count <i class="bi bi-question-circle" alt="group fee will be activated when total minimum of head count achieved"></i></label>
                                <input class="form-control form-control-sm rounded" type="number" wire:model="head">
                                @error('head')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div> --}}


                            <div class="col-md-4 mb-2">
                                <label for="">Agreement File @if (!$isEdit)<sup class="text-danger">*</sup>@endif</label>
                                <div class="file-agreement">
                                    <input type="file" wire:model.defer="agreement" class="form-control form-control-sm rounded">
                                </div>
                                @error('agreement')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>


                            <div class="col-md-12 mt-2">
                                <div class="d-flex justify-content-between">
                                    <button type="button" wire:click="resetFields" class="btn btn-sm btn-outline-danger rounded-3"
                                        data-bs-dismiss="modal">
                                        <i class="bi bi-x me-1"></i>
                                        Cancel
                                    </button>
                                    <div>
                                        <div wire:loading wire:target="edit,submit,update">
                                            Loading...
                                        </div>
                                        <div wire:loading wire:target="agreement">
                                            Uploading file...
                                        </div>
                                        <button type="submit" wire:loading.attr="disabled" wire:target="agreement" class="btn btn-sm btn-primary rounded-3">
                                            <i class="bi bi-save2"></i>
                                            Save
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@script
<script>
$wire.on('agreement-updated', () => {
    $("#agreementForm").modal('hide')
});
$wire.on('agreement-created', () => {
    $("#agreementForm").modal('hide')
});
$wire.on('agreement-failed', () => {
    $("#agreementForm").modal('hide')
});
</script>
@endscript
