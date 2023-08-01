{{-- Alert  --}}
<div class="row row-cols-md-4 row-cols-1 g-2 mt-4">
    {{-- Day 1-14 --}}
    @foreach ($digitalAlarm['mid'] as $key => $alarmMid)
        @if ($alarmMid)
            <div class="col">
                <div class="alert bg-danger text-white d-flex align-items-center py-2 border-alert" role="alert">
                    <i class="bi bi-exclamation-circle"></i>
                    <div class="">
                        The number of {{ str_replace('_', ' ', $key) }} <b
                            class="bg-white px-2 rounded text-primary">{{ $actualLeadsDigital[$key] }}</b> is
                        less than the target
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    {{-- Day 15-30 --}}
    @if (isset($digitalAlarm['end']))
        @foreach ($digitalAlarm['end'] as $key => $alarmEnd)
            @if ($alarmEnd)
                <div class="col">
                    <div class="alert bg-danger text-white d-flex align-items-center py-2 border-alert" role="alert">
                        <i class="bi bi-exclamation-circle"></i>
                        <div class="">
                            The number of {{ str_replace('_', ' ', $key) }} <b
                                class="bg-white px-2 rounded text-primary">{{ $actualLeadsDigital[$key] }}</b> is
                            less than the target
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif

     {{-- Always On --}}
    @if ($digitalAlarm['always_on'])
        <div class="col">
            <div class="alert bg-danger text-white d-flex align-items-center py-2 border-alert" role="alert">
                <i class="bi bi-exclamation-circle"></i>
                <div class="">
                    is there any event?
                </div>
            </div>
        </div>
    @endif
</div>