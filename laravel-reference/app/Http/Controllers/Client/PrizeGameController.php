<?php

namespace App\Http\Controllers\Client;

use App\Exports\PrizeGame\ResultsExport;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\PrizeGame\Game;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PrizeGameController extends Controller
{
    public function show(?Country $country = null)
    {
        $user = Auth::user();
        $company = $user->companies()->first();
        $current_country = $country ?? $user->country;

        if (! $company) {
            return redirect()->route('client.customer_satisfaction');
        }

        $games = Game::query()->with('content')->whereHas('content', function ($query) use ($company): void {
            $query->where('company_id', $company->id)->whereNotNull('country_id');
        })->where('is_viewable', true)->get();

        if ($games->count() <= 0) {
            return redirect()->route('client.customer_satisfaction');
        }

        if (! in_array($current_country->id, $games->pluck('content.country_id')->toArray())) {
            return redirect()->route('client.customer_satisfaction');
        }

        $game = $games->where('content.country_id', $current_country->id)->sortBy('created_at', SORT_REGULAR, true)->first();

        if ($game === null) {
            return redirect()->route('client.customer_satisfaction');
        }

        $winners = $game->winners()->get();
        $total_guess_count = $game->guesses()->count();

        return view('client.prizegame', [
            'game' => $game,
            'winners' => $winners,
            'total_guess_count' => $total_guess_count,
            'games' => $games->sortBy('created_at', SORT_REGULAR, true)->groupBy('content.country_id'),
        ]);
    }

    public function store(Game $game)
    {
        $game->load('content');

        $guesses = $game->guesses()->get()->unique('phone')->filter(function ($guess): bool {
            if (! $guess->valid) {
                return false;
            }

            return empty($guess->winner);
        });

        if ($guesses->count() > 0) {
            $guesses->random()->winner()->create();

            $game->update([
                'status' => Game::STATUS_DRAWN,
            ]);
        }

        return redirect()->route('client.prizegame.show', ['country' => $game->content->country]);
    }

    public function export(Game $game)
    {
        $company = $game->content->company->name;
        $winners = $game->winners;

        return Excel::download(new ResultsExport($winners), ucfirst(preg_replace('/[^a-zA-Z0-9_ -]/s', ' ', (string) $company)).'PrizeGameResultsExport.xlsx');
    }
}
