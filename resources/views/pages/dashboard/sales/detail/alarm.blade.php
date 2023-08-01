  {{-- Alert  --}}
  <div class="row row-cols-md-4 row-cols-1 g-2">
    {{-- Day 1-14 --}}
    @foreach ($salesAlarm['mid'] as $key => $alarmMid)
        @if ($alarmMid)
            <div class="col">
                <div class="alert bg-danger text-white d-flex align-items-center py-2 border-alert" role="alert">
                    <i class="bi bi-exclamation-circle"></i>
                    <div class="">
                        The number of {{ str_replace('_', ' ', $key) }} <b
                            class="bg-white px-2 rounded text-primary">{{ $actualLeadsSales[$key] }}</b> is
                        less than the target
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    {{-- Day 15-30 --}}
    @if (isset($salesAlarm['end']))
        @foreach ($salesAlarm['end'] as $key => $alarmEnd)
            @if ($alarmEnd)
                <div class="col">
                    <div class="alert bg-danger text-white d-flex align-items-center py-2 border-alert" role="alert">
                        <i class="bi bi-exclamation-circle"></i>
                        <div class="">
                            The number of {{ str_replace('_', ' ', $key) }} <b
                                class="bg-white px-2 rounded text-primary">{{ $actualLeadsSales[$key] }}</b> is
                            less than the target
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif

</div>