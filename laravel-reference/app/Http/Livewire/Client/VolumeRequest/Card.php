<?php

namespace App\Http\Livewire\Client\VolumeRequest;

use App\Enums\VolumeRequestStatusEnum;
use App\Models\VolumeRequest;
use App\Services\DateService;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class Card extends Component
{
    public VolumeRequest $volume_request;

    public bool $volume_request_closed = false;

    public $rules = [
        'volume_request.headcount' => 'numeric',
    ];

    public function render(): View
    {
        return view('livewire.client.volume-request.card');
    }

    public function mount(VolumeRequest $volume_request): void
    {
        $this->volume_request = $volume_request;

        // IF volume request is more then 1 month before the current month OR is if the data submission for the previous month's period is over
        if (Carbon::now()->diffInMonths($this->volume_request->date->addMonth()) !== 0
        || (DateService::is_day_of_month(day: 4, preceding_day: 3, is_past: true) && Carbon::now()->gte(Carbon::parse('12:00')))) {
            $this->volume_request_closed = true;
        }
    }

    public function save(): void
    {
        $this->validate();

        $this->volume_request->update([
            'status' => VolumeRequestStatusEnum::COMPLETED,
        ]);
    }

    public function auto_completed_warning(): void
    {
        $this->emit('modelCannotDeleted');
    }
}
