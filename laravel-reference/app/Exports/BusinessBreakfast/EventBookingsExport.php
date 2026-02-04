<?php

namespace App\Exports\BusinessBreakfast;

use App\Models\BusinessBreakfast\Booking;
use App\Models\BusinessBreakfast\Event;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventBookingsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            'Newsletter',
        ];
    }

    public function map($booking): array
    {
        return [
            $booking->first_name,
            $booking->last_name,
            $booking->email,
            $booking->phone_number,
            $booking->newsletter ? 'Yes' : 'No',
        ];
    }

    public function query(): Builder
    {
        return Booking::query()->where('event_id', $this->event->id);
    }
}
