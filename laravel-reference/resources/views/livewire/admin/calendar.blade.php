@push('livewire_js')
<script src="{{asset('assets/js/fullcalendar-5.9.0/lib/main.js')}}"></script>
<script src="{{asset('assets/js/datetime.js')}}"></script>
<script src="{{asset('js/client/master.js')}}?v={{time()}}"></script>

<script>

    const events = JSON.parse(@json($formated_tasks_data));

    const minTime = events.map(item => item.startTime).sort().shift()
    const maxTime = events.map(item => item.endTime).sort().pop()

    let calendar;

    document.addEventListener('DOMContentLoaded', function () {
        let calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            nowIndicator: true,
            hiddenDays: [6, 0],
            firstDay: 1,
            slotMinTime: minTime,
            slotMaxTime: maxTime,
            allDaySlot: false,
            locale: '{{app()->getLocale()}}',
            events: events,
            eventColor: '#59c6c6',
            contentHeight: 600,
            expandRows: true,
            displayEventTime: false,
            displayEventEnd: true,
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: false
            },
            dayHeaderFormat: {weekday: 'long'},
            buttonText: {
                today: '{{__('eap-online.video_therapy.today')}}'
            },
            dateClick: function(info) { // Bring up new task modal
                Livewire.emit('openModal', 'admin.todo.create-modal', {'start_date':info.dateStr} );
            },
            dayCellContent: function (info, create) {
                return create('span', { id: "fc-day-num_"+info.dayNumberText }, info.dayNumberText+'.');

            }
        });
        calendar.render();
    });

    Livewire.on('task_refresh', function(data) {
        Swal.fire({
            title: '{{ __('task.task_created') }}',
            text: '',
            icon: 'success',
            confirmButtonText: 'Ok'
        }).then(function (result) {
            calendar.addEvent({
                title: data.title,
                start: data.start,
                end: data.end,
                url: data.url,
                allDay: true
            });
        });
    });

</script>
@endpush
<div>
    <link rel="stylesheet" href="{{asset('assets/js/fullcalendar-5.9.0/lib/main.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/form.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/cases/datetime.css')}}">
    <style>
        .fc-button-primary {
            background-color: rgb(89,198,198)!important;
            border: 0px!important;
        }

        .fc-today-button {
            border-radius: 12px !important;
        }

        .fc-prev-button {
            border-top-left-radius: 12px !important;
            border-bottom-left-radius: 12px !important;
        }

        .fc-next-button {
            border-top-right-radius: 12px !important;
            border-bottom-right-radius: 12px !important;
        }

        .fc-direction-ltr .fc-daygrid-event.fc-event-end,
        .fc-event-main-frame {
            flex-direction: column;
            text-align: center;
        }

        .fc-event-desc,
        .fc-event-title {
            white-space: break-spaces;
        }

        a {
            color: inherit!important;
        }

        /* Make the swal modal window appear behind the connected user modal */
        .swal2-container {
            z-index:10!important;
        }

        .fc-daygrid-day-frame {
            cursor: pointer;
        }
    </style>
    <div class="w-100" wire:ignore>
        <div wire:ignore id='calendar'></div>
    </div>
</div>
