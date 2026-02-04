<?php

namespace App\Services;

use App\Http\Requests\Api\LiveWebinarCurrentRequest;
use App\Http\Requests\Api\LiveWebinarIndexRequest;
use App\Models\LiveWebinar;
use App\Services\Zoom\ZoomMeetingService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LiveWebinarService
{
    public function __construct(protected ZoomMeetingService $zoomMeetingService) {}

    public function get_live_webinars(?int $expert_id = null): Collection
    {
        return LiveWebinar::query()
            ->when($expert_id, fn (Builder $query) => $query->where('user_id', $expert_id))
            ->get();
    }

    public function get_live_webinars_for_eap_user(LiveWebinarIndexRequest $request): LengthAwarePaginator
    {
        $company_id = $request->validated('company_id');
        $country_id = $request->validated('country_id');
        $language_id = $request->validated('language_id');

        $english_language_id = 1;

        $result = $this->live_webinar_query($company_id, $country_id, $language_id)
            ->where('from', '<', Carbon::now())
            ->paginate();

        if ($result->total() === 0 && $language_id !== $english_language_id) {
            return $this->live_webinar_query($company_id, $country_id, $english_language_id)
                ->where('from', '<', Carbon::now())
                ->paginate();
        }

        return $result;
    }

    public function get_current_live_webinar_for_eap_user(LiveWebinarCurrentRequest $request): Collection
    {
        $company_id = $request->validated('company_id');
        $country_id = $request->validated('country_id');
        $language_id = $request->validated('language_id');

        $english_language_id = 1;

        $result = $this->live_webinar_query($company_id, $country_id, $language_id)
            ->where('from', '>=', Carbon::now()->subMinutes(30)->format('Y-m-d H:i:s'))
            ->orderBy('from')
            ->get();

        if ($result->isEmpty() && $language_id !== $english_language_id) {
            return $this->live_webinar_query($company_id, $country_id, $english_language_id)
                ->where('from', '>=', Carbon::now()->subMinutes(30)->format('Y-m-d H:i:s'))
                ->orderBy('from')
                ->get();
        }

        return $result;
    }

    /**
     * Base query: (company-specific for company+country) OR (non-company-specific),
     * language filtering is applied to both sides via the passed $language_id.
     */
    private function live_webinar_query(int $company_id, int $country_id, int $language_id): Builder
    {
        return LiveWebinar::query()
            ->where(function (Builder $query) use ($company_id, $country_id): void {
                $query->where(function (Builder $subQuery) use ($company_id, $country_id): void {
                    $subQuery->whereHas('companies', fn (Builder $q) => $q->where('companies.id', $company_id))
                        ->whereHas('countries', fn (Builder $q) => $q->where('countries.id', $country_id));
                })->orWhere(function (Builder $subQuery): void {
                    $subQuery->whereDoesntHave('companies');
                });
            })
            ->where('language_id', $language_id);
    }

    public function get_index_categories(): array
    {
        $live_webinars = $this->get_live_webinars();

        $months = $live_webinars
            ->whereNotNull('from')
            ->map(fn (LiveWebinar $live_webinar): string => Carbon::parse($live_webinar->from)->year.'-'.Carbon::parse($live_webinar->from)->format('m'))
            ->sort()
            ->unique()
            ->reverse();

        $years = $live_webinars
            ->whereNotNull('from')
            ->map(fn (LiveWebinar $live_webinar) => Carbon::parse($live_webinar->from)->year)
            ->sort()
            ->unique()
            ->reverse();

        return [
            'years' => $years,
            'months' => $months,
            'live_webinars' => $live_webinars,
        ];
    }

    public function update_live_webinar(LiveWebinar $live_webinar, array $data): LiveWebinar
    {
        $live_webinar->update($data);

        return $this->zoomMeetingService->syncMeeting($live_webinar);
    }

    public function delete_live_webinar(LiveWebinar $live_webinar): void
    {
        $live_webinar->companies()->detach();
        $live_webinar->countries()->detach();
        $live_webinar->delete();
    }

    public function sync_zoom_meeting(LiveWebinar $live_webinar): LiveWebinar
    {
        return $this->zoomMeetingService->syncMeeting($live_webinar);
    }
}
