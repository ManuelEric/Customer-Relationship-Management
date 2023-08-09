{{-- Alert  --}}
<div class="row row-cols-md-4 row-cols-1 g-2 mt-4">
    
    @foreach ($alarmLeads['digital'] as $alarmTime)
        @foreach ($alarmTime as $key => $alarm)
            @if ($alarm)
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
    @endforeach

</div>