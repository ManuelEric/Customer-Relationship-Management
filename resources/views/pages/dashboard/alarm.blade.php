<div class="row  g-3 mb-1">
    @foreach ($alarmLeads['general']['mid'] as $key => $alarm)
        
        @if($alarm)
            
            <div class="col-12">
                <div class="alert bg-danger text-white d-flex align-items-center mb-0 py-2" role="alert">
                    <i class="bi bi-exclamation-circle"></i>
                    <small class="">
                        @if($key == 'event')
                            There are no events this month.
                        @else
                            There are no sales target this month.
                        @endif
                    </small>
                </div>
            </div>
        @endif
        
    @endforeach
</div>