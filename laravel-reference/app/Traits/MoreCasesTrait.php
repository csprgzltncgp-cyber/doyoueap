<?php

namespace App\Traits;

use App\Enums\CaseExpertStatus;
use App\Models\Cases;
use Illuminate\Database\Eloquent\Builder;

trait MoreCasesTrait
{
    public function setCanAcceptMoreCases($expert): void
    {
        if (empty(optional($expert->expert_data)->max_inprogress_cases)) {
            return;
        }

        $inprogress_number = Cases::query()
            ->whereNotIn('status', ['confirmed', 'client_unreachable_confirmed', 'interrupted_confirmed', 'opened'])
            ->whereHas('experts', fn (Builder $query) => $query->where('user_id', $expert->id)->whereNotIn('accepted', [CaseExpertStatus::REJECTED->value]))
            ->get()->filter(fn ($case): bool => $case->case_accepted_expert()->id == $expert->id)->count();

        if ($inprogress_number >= $expert->expert_data->max_inprogress_cases && $expert->expert_data->can_accept_more_cases) {
            $expert->expert_data()->update([
                'can_accept_more_cases' => false,
            ]);

            return;
        }

        if ($inprogress_number < $expert->expert_data->max_inprogress_cases && ! $expert->expert_data->can_accept_more_cases) {
            $expert->expert_data()->update([
                'can_accept_more_cases' => true,
            ]);

            return;
        }
    }
}
