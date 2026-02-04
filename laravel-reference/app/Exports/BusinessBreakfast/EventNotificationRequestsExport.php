<?php

namespace App\Exports\BusinessBreakfast;

use App\Models\BusinessBreakfast\Event;
use App\Models\BusinessBreakfast\NotificationRequest;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventNotificationRequestsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(
        public Event $event,
    ) {}

    public function headings(): array
    {
        return [
            'First Name',
            'Last Name',
            'Email',
            'Phone Number',
        ];
    }

    public function map($notification_request): array
    {
        return [
            $notification_request->first_name,
            $notification_request->last_name,
            $notification_request->email,
            $notification_request->phone_number,
        ];
    }

    public function query(): Builder
    {
        return NotificationRequest::query()->where('event_id', $this->event->id);
    }
}
