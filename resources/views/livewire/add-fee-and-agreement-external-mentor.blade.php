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
                        <td>Active Period</td>
                        <td>To</td>
                        <td>Stream</td>
                        <td>Engagement Type</td>
                        <td>Package</td>
                        <td>Grade</td>
                        <td>Fee Individual</td>
                        <td>Agreement</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse($user->user_streams as $role_stream)
                        @php
                            $file_path = 'project/crm/user/'.$user->id.'/'.$role_stream->agreement;
                            $url = Storage::disk('s3')->temporaryUrl($file_path, now()->addMinutes(5));
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}.</td>
                            <td class="text-center">{{ $role_stream->start_date ? date('d, M Y', strtotime($role_stream->start_date)) : 'n/a' }}</td>
                            <td class="text-center">{{ $role_stream->end_date ? date('d, M Y', strtotime($role_stream->end_date)) : 'n/a' }}</td>
                            <td>{{ $role_stream->stream->stream_name ?? 'n/a' }}</td>
                            <td>{{ $role_stream->engagement_type->phase_detail_name }}</td>
                            <td>{{ $role_stream->package ?? 'n/a' }}</td>
                            <td class="text-center">{{ $role_stream->grade ?? 'n/a' }}</td>
                            <td>{{ $role_stream->fee_individual ? "Rp. ".number_format($role_stream->fee_individual) : 'n/a' }}</td>
                            <td class="text-center">{!! $role_stream->agreement ? "<a href='{$url}' target='_blank'>view</a>" : 'n/a' !!}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-warning" wire:click="edit({{ $role_stream->id }})" data-bs-toggle="modal" data-bs-target="#agreementForm"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-danger ms-1" 
                                    x-data
                                    @click.prevent="if (confirm('Are you sure you want to delete? This cannot be undone.')) { $wire.delete({{ $role_stream->id }}) }"
                                ><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No data</td>
                        </tr>
                
                    @endforelse
                </tbody>
            </table>

            <div wire:loading class="text-center">Loading...</div>
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
                            <div class="col-md-12 mb-2">
                                <label for="">Stream <sup class="text-danger">*</sup></label>
                                <select wire:model="stream_id" class="form-select form-select-sm w-100">
                                    <option data-placeholder="true">Select Stream</option>
                                    @forelse ($streams as $stream)
                                        <option value="{{ $stream->id }}">{{ $stream->stream_name }}</option>
                                    
                                    @empty
                                        <option>No streams fetched</option>
                                    @endforelse
                                </select>
                                @error('subject_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="">Engagement Type <sup class="text-danger">*</sup></label>
                                <select wire:model="engagement_type_id" class="form-select form-select-sm w-100">
                                    <option data-placeholder="true">Select Engagement Type</option>
                                    @forelse ($engagement_types as $engagement_type)
                                        <option value="{{ $engagement_type->id }}">{{ $engagement_type->phase_detail_name }}</option>
                                    
                                    @empty
                                        <option>No engagement type fetched</option>
                                    @endforelse
                                </select>
                                @error('engagement_type_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-2">
                                <label for="">Package <sup class="text-danger">*</sup></label>
                                <select wire:model="package" class="form-select form-select-sm w-100">
                                    <option data-placeholder="true">Select Package</option>
                                    @forelse ($packages as $key => $package)
                                        <option value="{{ $package }}">{{ $package }}</option>
                                    
                                    @empty
                                        <option>No streams fetched</option>
                                    @endforelse
                                </select>
                                @error('subject_id')
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

                            
                            
                            <div class="col-md-4 mb-2">
                                <label for="">Grade</label>
                                <select wire:model="grade" class="form-select form-select-sm" disabled>
                                    <option data-placeholder="true"></option>
                                    <option value="9-12">All</option>
                                    <option value="9-10">9-10</option>
                                    <option value="11-12">11-12</option>
                                </select>
                                @error('grade')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="">Fee Individual (Gross) <sup class="text-danger">*</sup></label>
                                <input class="form-control form-control-sm rounded" type="number" wire:model="fee_individual" placeholder="230625">
                                @error('fee_individual')
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
                                        <div wire:loading>
                                            Loading...
                                        </div>
                                        <button type="submit" wire:loading.attr="disabled" class="btn btn-sm btn-primary rounded-3">
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
</script>
@endscript