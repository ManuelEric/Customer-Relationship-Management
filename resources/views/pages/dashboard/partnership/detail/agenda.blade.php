@push('styles')
<style>
    .fc .fc-button-primary:disabled {
        text-transform: capitalize;
    }

    .fc .fc-col-header-cell-cushion {
        text-decoration: none;
        color: #e57e18f5;
    }

    .fc a[data-navlink] {
        cursor: pointer;
        text-decoration: none;
        border-radius: 0 0 0 9px;
        background: #1f3bb3;
        padding: 5px;
        color: white;
    }
</style>
@endpush

<div class="card mb-3">
    <div class="card-body">
        <div class="row gap-md-0 gap-2">
            <div class="col-md-7">
                <div id='calendar'></div>
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="m-0 p-0">
                            Speaker Agenda Detail
                        </h6>
                        <h6 class="m-0 p-0" id="agenda_date">

                        </h6>
                    </div>
                    <div class="card-body overflow-auto {{!isset($speakerToday) ? 'd-none' : ''}}" id="speaker_list" style="height: 370px">
                        @foreach ($speakerToday as $speaker)
                            
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="">
                                        {{ $speaker->speaker_name }} <br>
                                        <small>
                                            {{ $speaker->event_name }}
                                        </small>
                                    </div>
                                    <div class="">
                                        {{ date("M d, Y H.i", strtotime($speaker->start_time)) }} - {{ date("M d, Y H.i", strtotime($speaker->end_time)) }}
                                    </div>
                                </li>
                            </ul>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let data_speaker = new Array();

    @foreach ($speakers as $key => $speaker)
        data_speaker.push({
            id: '{{$key + 1}}',
            title: '{{$speaker->event_name}} - {{ $speaker->speaker_name }}',
            start: moment('{{$speaker->start_time}}').format(),
            end: moment('{{$speaker->end_time}}').format(),
        });
    @endforeach
    
    // console.log(data_speaker)

    let date = new Date;
    $('#agenda_date').html(moment(date).format('LL'))

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            themeSystem: 'bootstrap5',
            height: 400,
            headerToolbar: {
                start: 'title', // will normally be on the left. if RTL, will be on the right
                center: '',
                end: 'today prev,next' // will normally be on the right. if RTL, will be on the left
            },
            titleFormat: {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            },
            dayHeaderFormat: {
                weekday: 'long'
            },
            slotLabelFormat: {
                hour: '2-digit',
                minute: '2-digit',
                omitZeroMinute: false,
                meridiem: 'lowercase'
            },
            dayMaxEventRows: true, // for all non-TimeGrid views
            events: data_speaker,
            
            eventDisplay: 'block',
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: true
            },
            eventMouseEnter: function(info) {
                let title = info.event.title
                let type = info.view.type

                // change the border color just for fun
                info.el.style.background = 'green';
                // let d = moment(date).format('YYYY-MM-DD')
                // $('#agenda_date').html(moment(info.event.start).format('LL'))
                // checkAgenda(d)
            },
            eventMouseLeave: function(info) {
                // change the border color just for fun
                info.el.style.background = '';
            },
            navLinks: true,
            navLinkDayClick: function(date, jsEvent) {
                let d = moment(date).format('YYYY-MM-DD')
                $('#agenda_date').html(moment(date).format('LL'))
                checkAgenda(d)
            }

        });
        calendar.render();
    });

    function checkAgenda(date) {
        Swal.showLoading()

        // Axios here...
        axios.get('{{ url("api/partner/agenda/") }}/' + date)
            .then((response) => {
                var result = response.data.data.allSpeaker
                var start_listgroup = '<ul class="list-group">' +
                            '<li class="list-group-item d-flex justify-content-between align-items-center" style="margin-bottom:15px">';
                
                var end_listgroup =  '</li>' +
                                    '</ul>';
                var html;
                if(result.length > 0){
                    $('#speaker_list').removeClass('d-none')
                    $('#speaker_list').empty()
                    result.forEach(function(item, index, arr) {
                        html = start_listgroup 
                        html += '<div class=""><p>' + item.speaker_name + '</p>'
                        html += '<small>' + item.event_name + '</small></div>'
                        html += '<div class="">' + moment(item.start_time).format('MMM DD, YYYY HH.mm') + ' - ' + moment(item.end_time).format('MMM DD, YYYY HH.mm'); + '</div>'
                        html += end_listgroup
                        $('#speaker_list').append(html)
                        })
                }else{
                    $('#speaker_list').addClass('d-none')
                }
                // console.log(result)
                swal.close()
            }, (error) => {
                console.log(error)
                swal.close()
        })
            
    }
</script>
@endpush