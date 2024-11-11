<div class="card mb-3">
    <div class="card-header">
        <h6 class="p-0 m-0">Conversion Lead</h6>
    </div>
    <div class="card-body overflow-auto" style="max-height: 50vh;">
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="text-center bg-secondary py-2 text-white">
                    <strong>Lead Source</strong>
                </div>
                <ul class="list-group list-group-flush" style="font-size:11px;">
                    @forelse ($list_of_lead_source_being_used as $detail)
                        <a href="#"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center lead-source-item cursor-pointer"
                            data-leadname="{{ $detail->lead_source }}" data-lead="{{ $detail->lead_id }}" data-sublead="{{ $detail->sub_lead_id }}"
                            data-sdate="{{ $date_details['start'] }}" data-edate="{{ $date_details['end'] }}">
                            <div class="">
                                {{ $detail->lead_source }}
                            </div>
                            <span class="badge bg-primary rounded-pill"
                                style="font-size:11px;">{{ $detail->lead_source_count }}</span>
                        </a>

                        @empty
                            <div class="my-2 text-center">No Data</div>
                    @endforelse
                </ul>
            </div>
            <div class="col-md-6">
                <div class="text-center bg-secondary py-2 text-white">
                    <strong>Conversion Lead</strong>
                </div>
                <ul class="list-group list-group-flush" style="font-size:11px;">
                    @forelse ($list_of_conversion_lead_being_used as $detail)
                        <a href="#"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center conversion-lead-item cursor-pointer"
                            data-leadname="{{ $detail->conversion_lead }}" data-lead="{{ $detail->lead_id }}" data-sublead="{{ $detail->sub_lead_id }}"
                            data-sdate="{{ $date_details['start'] }}" data-edate="{{ $date_details['end'] }}">
                            <div class="">
                                {{ $detail->conversion_lead }}
                            </div>
                            <span class="badge bg-primary rounded-pill"
                                style="font-size:11px;">{{ $detail->conversion_lead_count }}</span>
                        </a>

                        @empty
                            <div class="my-2 text-center">No Data</div>

                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
