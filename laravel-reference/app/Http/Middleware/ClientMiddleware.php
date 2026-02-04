<?php

namespace App\Http\Middleware;

use App\Models\PrizeGame\Game;
use Closure;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClientMiddleware
{
    protected $has_in_progress_prizegame = true;

    protected $has_usage_greaters_than_zero = true;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):((Response | RedirectResponse))  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user !== null) {
            $company = $user->companies()->first();
            $current_country = $user->country;

            if (! $company) {
                return redirect()->route('client.customer_satisfaction');
            }

            $games = Game::query()->with('content')->whereHas('content', function ($query) use ($company): void {
                $query->where('company_id', $company->id)->whereNotNull('country_id');
            })->where('is_viewable', true)->get();

            if ($games->count() <= 0) {
                $this->has_in_progress_prizegame = false;
            }

            if (! in_array($current_country->id, $games->pluck('content.country_id')->toArray())) {
                $this->has_in_progress_prizegame = false;
            }

            $game = $games->where('content.country_id', $current_country->id)->sortBy('created_at', SORT_REGULAR, true)->first();

            if ($game === null) {
                $this->has_in_progress_prizegame = false;
            }

            $org_data = $company->org_datas->where('country_id', $current_country->id)->first();

            try {
                $this->has_usage_greaters_than_zero = calculate_program_usage($company, $current_country, $org_data) > 0;
            } catch (Exception $e) {
                $this->has_usage_greaters_than_zero = false;

                Log::error('Error while calculating program usage: '.$e->getMessage());
            }
        }

        view()->share('has_in_progress_prizegame', $this->has_in_progress_prizegame);
        view()->share('has_usage_greaters_than_zero', $this->has_usage_greaters_than_zero);

        return $next($request);
    }
}
