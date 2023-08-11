  {{-- Alert  --}}

@if($countAlarm['sales'] > 0) 
    <div class="col">
        <fieldset class="border p-2 rounded shadow">
            <legend class="float-none w-auto fs-6 mx-3">
                <small class="text-danger">
                    <i class="bi bi-alarm me-1"></i> Sales Team Alarm
                </small>
            </legend>
            <div class="row {{$isAdmin ? 'row-cols-md-2' : 'row-cols-md-4'}} row-cols-1 g-2">
                @foreach ($alarmLeads['sales'] as $alarmTime)
                    @foreach ($alarmTime as $key => $alarm)
                        @if($alarm)
                            <div class="col">
                                <div class="alert bg-danger text-white d-flex align-items-center mb-0 py-2 border-alert"
                                    role="alert">
                                    <i class="bi bi-exclamation-circle"></i>
                                    @if($key != 'failed')
                                        <small class="">
                                            The number of {{ str_replace('_', ' ', $key) }} <b class="bg-white px-2 rounded text-primary">{{ $actualLeadsSales[$key] }}</b> is less
                                            than the
                                            target
                                        </small>
                                    @else 
                                        <small class="">
                                            Failed
                                        </small>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endforeach
            </div>
        </fieldset>
    </div>
@endif