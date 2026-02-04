<?php

namespace App\Http\Controllers\BusinessBreakfast;

use App\Enums\BusinessBreakfast\InteractionType;
use App\Exports\BusinessBreakfast\EventBookingsExport;
use App\Exports\BusinessBreakfast\EventNotificationRequestsExport;
use App\Http\Controllers\Controller;
use App\Models\BusinessBreakfast\Event;
use Maatwebsite\Excel\Facades\Excel;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::query()->withCount([
            'bookings',
            'notification_requests',
            'interactions',
            'interactions as later_date_count' => fn ($query) => $query->where('type', InteractionType::A_LATER_DATE_WOULD_BE_SUITABLE->value),
            'interactions as not_interested_count' => fn ($query) => $query->where('type', InteractionType::I_AM_NOT_INTERESTED->value),
            'interactions as next_2_4_months_count' => fn ($query) => $query->where('type', InteractionType::IN_THE_NEXT_2_4_MONTHS->value),
            'interactions as next_5_6_months_count' => fn ($query) => $query->where('type', InteractionType::IN_THE_NEXT_5_6_MONTHS->value),
            'interactions as next_7_8_months_count' => fn ($query) => $query->where('type', InteractionType::IN_THE_NEXT_7_8_MONTHS->value),
        ])->get();

        $events = $events->sortBy('date')->groupBy(fn ($event) => $event->date->format('Y'))->map(fn ($year) => $year->groupBy(fn ($event): string => $event->date->format('Y').'-'.$event->date->format('m'))->sortKeysDesc())->sortKeysDesc();

        return view('admin.business-breakfast.index', ['events' => $events]);
    }

    public function export_notification_requests(Event $event)
    {
        return Excel::download(new EventNotificationRequestsExport($event), 'event-notification-requests-'.$event->date->format('Y-m-d').'.xlsx');
    }

    public function export_bookings(Event $event)
    {
        return Excel::download(new EventBookingsExport($event), 'event-bookings-'.$event->date->format('Y-m-d').'.xlsx');
    }
}
