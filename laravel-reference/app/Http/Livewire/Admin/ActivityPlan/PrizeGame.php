<?php

namespace App\Http\Livewire\Admin\ActivityPlan;

use App\Models\ActivityPlan;
use App\Services\PrizeGameService;
use Livewire\Component;

class PrizeGame extends Component
{
    public ActivityPlan $activity_plan;

    private PrizeGameService $prize_game_service;

    public function boot(
        PrizeGameService $prize_game_service
    ): void {
        $this->prize_game_service = $prize_game_service;
    }

    public function render()
    {
        $games = $this->prize_game_service->get_prize_games($this->activity_plan->company);

        return view('livewire.admin.activity-plan.prizegame', ['games' => $games]);
    }
}
