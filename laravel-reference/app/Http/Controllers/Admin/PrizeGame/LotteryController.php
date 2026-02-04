<?php

namespace App\Http\Controllers\Admin\PrizeGame;

use App\Exports\PrizeGame\ResultsExport;
use App\Http\Controllers\Controller;
use App\Models\PrizeGame\Game;
use Maatwebsite\Excel\Facades\Excel;

class LotteryController extends Controller
{
    public function show(Game $game)
    {
        $winners = $game->winners()->get();
        $total_guess_count = $game->guesses()->count();

        return view('admin.prizegame.lottery.show', ['game' => $game, 'winners' => $winners, 'total_guess_count' => $total_guess_count]);
    }

    public function store(Game $game)
    {
        $guesses = $game->guesses()->get()->unique('email')->filter(function ($guess): bool {
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

        return redirect()->route('admin.prizegame.lottery.show', ['game' => $game]);
    }

    public function archive($id)
    {
        if ($game = Game::onlyTrashed()->where('id', $id)->first()) {
            $winners = $game->winners()->get();
            $total_guess_count = $game->guesses()->count();

            return view('admin.prizegame.lottery.archive', ['game' => $game, 'winners' => $winners, 'total_guess_count' => $total_guess_count]);
        }

        return redirect()->back();
    }

    public function export($id)
    {
        $game = Game::withTrashed()->where('id', $id)->first();
        $company = $game->content->company->name;
        $winners = $game->winners;

        return Excel::download(new ResultsExport($winners), ucfirst(preg_replace('/[^a-zA-Z0-9_ -]/s', ' ', (string) $company)).'PrizeGameResultsExport.xlsx');
    }
}
