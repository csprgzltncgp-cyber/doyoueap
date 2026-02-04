<?php

namespace App\Services;

use App\Models\Company;
use App\Models\PrizeGame\Game;
use Illuminate\Database\Eloquent\Builder;

class PrizeGameService
{
    public function get_prize_games(?Company $company = null)
    {
        return Game::query()
            ->with('content')
            ->when($company, function ($query) use ($company): void {
                $query->whereHas('content', function (Builder $query) use ($company): void {
                    $query->where('company_id', $company->id);
                });
            })
            ->whereNotIn('status', [Game::STATUS_DRAWN])
            ->get();
    }
}
