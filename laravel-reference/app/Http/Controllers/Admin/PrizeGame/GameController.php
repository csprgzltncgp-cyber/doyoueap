<?php

namespace App\Http\Controllers\Admin\PrizeGame;

use App\Http\Controllers\Controller;
use App\Models\PrizeGame\Game;
use App\Services\PrizeGameService;
use App\Traits\Prizegame\ContentTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class GameController extends Controller
{
    use ContentTrait;

    public function index(PrizeGameService $prize_game_service)
    {
        $games = $prize_game_service->get_prize_games();

        return view('admin.prizegame.games.index', ['games' => $games]);
    }

    public function archived()
    {
        $games = Game::query()->where('status', Game::STATUS_DRAWN)->get();

        return view('admin.prizegame.games.archived', ['games' => $games]);
    }

    public function create_from_normal()
    {
        request()->validate([
            'company_id' => ['required'],
            'country_id' => ['required'],
            'content_id' => ['required'],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date'],
        ]);

        $content_id = $this->recreate_content(
            request()->input('content_id'),
            request()->input('company_id'),
            request()->input('country_id')
        );

        Game::query()->create([
            'from' => request()->input('from_date'),
            'to' => request()->input('to_date'),
            'status' => $this->get_status(request()->input('from_date'), request()->input('to_date')),
            'content_id' => $content_id,
        ]);

        return response('Content created!');
    }

    public function create_from_specific()
    {
        request()->validate([
            'content_id' => ['required'],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date'],
        ]);

        Game::query()->create([
            'from' => request()->input('from_date'),
            'to' => request()->input('to_date'),
            'status' => $this->get_status(request()->input('from_date'), request()->input('to_date')),
            'content_id' => request()->input('content_id'),
        ]);

        return response('Content created!');
    }

    public function set_viewable()
    {
        request()->validate([
            'game_id' => ['required'],
            'viewable' => ['required'],
        ]);

        Game::query()->where('id', request()->input('game_id'))->update([
            'is_viewable' => filter_var(request()->input('viewable'), FILTER_VALIDATE_BOOLEAN),
        ]);

        return response('Game updated!');
    }

    public function set_date()
    {
        request()->validate([
            'from' => ['required'],
            'to' => ['required'],
            'game_id' => ['required'],
        ]);

        $game = Game::query()->where('id', request()->input('game_id'))->first();

        $game->update([
            'from' => request()->input('from'),
            'to' => request()->input('to'),
        ]);

        $start = Carbon::parse($game->from);
        $end = Carbon::parse($game->to)->addDay();

        if (Carbon::parse(now())->between($start, $end)) {
            $game->update([
                'status' => Game::STATUS_ACTIVE,
            ]);
        }

        if (Carbon::parse(now())->lt($start)) {
            $game->update([
                'status' => Game::STATUS_PENDING,
            ]);
        }

        if (Carbon::parse(now())->gt($end)) {
            $game->update([
                'status' => Game::STATUS_CLOSED,
            ]);
        }

        return redirect()->route('admin.prizegame.games.index');
    }

    public function delete(Game $game)
    {
        $game = Game::query()->where('id', request()->input('game_id'))->first();

        if ($game) {
            $game->forceDelete();
        }

        return response('ok!');
    }

    public function is_creatable()
    {
        request()->validate([
            'company_id' => ['required'],
            'country_id' => ['required'],
        ]);

        $company_id = request()->input('company_id');
        $country_id = request()->input('country_id');

        $game = Game::query()->whereHas('content', function (Builder $query) use ($company_id, $country_id): void {
            $query->where(['country_id' => $country_id, 'company_id' => $company_id]);
        })->where('status', '<>', Game::STATUS_DRAWN)->exists();

        // archive games with drawn status when trying to create new game
        $games_to_archive = Game::query()->whereHas('content', function (Builder $query) use ($company_id, $country_id): void {
            $query->where(['country_id' => $country_id, 'company_id' => $company_id]);
        })->where('status', Game::STATUS_DRAWN)->get();

        foreach ($games_to_archive as $game_to_archive) {
            $game_to_archive->update([
                'deleted_at' => now(),
            ]);
        }

        return response()->json(! $game);
    }

    private function get_status($from, $to): ?int
    {
        $start = Carbon::parse($from);
        $end = Carbon::parse($to);

        if (Carbon::parse(now())->between($start, $end)) {
            return Game::STATUS_ACTIVE;
        }

        if (Carbon::parse(now())->lt($start)) {
            return Game::STATUS_PENDING;
        }

        if (Carbon::parse(now())->gt($end)) {
            return Game::STATUS_CLOSED;
        }

        return null;
    }
}
