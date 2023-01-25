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

<div class="card mb-3">
    <div class="card-body">
        <div class="row">
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
                    <div class="card-body overflow-auto" style="height: 370px">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="">
                                    Speaker Name <br>
                                    <small>
                                        Event Name / Program Name
                                    </small>
                                </div>
                                <div class="">
                                    10.00 - 14.00
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
            events: [{
                    id: 1,
                    title: 'Event 1 - Speaker 1',
                    start: '2023-01-25'
                },
                {
                    id: 2,
                    title: 'Event 2 - Speaker 2',
                    start: '2023-01-26',
                    end: '2023-01-27'
                },
                {
                    id: 3,
                    title: 'Event 3 - Speaker 4',
                    start: '2023-01-28T12:30:00',
                    end: '2023-01-28T13:30:00',
                },
                {
                    id: 4,
                    title: 'Event 3 - Speaker 5',
                    start: '2023-01-28T13:30:00',
                    end: '2023-01-28T14:30:00',
                }
            ],
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
        // axios here .. 
    }
</script>
