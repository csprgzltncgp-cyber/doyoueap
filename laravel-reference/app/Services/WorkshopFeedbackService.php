<?php

namespace App\Services;

use App\Models\Company;
use App\Models\WorkshopFeedback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class WorkshopFeedbackService
{
    public function get_workshops(?Company $company = null, ?string $search = null): Collection
    {
        return WorkshopFeedback::query()
            ->with('workshop_case')
            ->when($company, fn (Builder $query) => $query->whereHas('workshop_case', fn (Builder $query2) => $query2->where('company_id', $company->id)))
            ->when($search, function ($query) use ($search): void {
                $query->whereHas('workshop_case', function ($query2) use ($search): void {
                    $query2->where('activity_id', 'like', "%{$search}%");
                    $query2->orWhere('topic', 'like', "%{$search}%");
                    $query2->orWhereHas('company', function ($query3) use ($search): void {
                        $query3->where('name', 'like', "%{$search}%");
                    });
                    $query2->orWhereHas('user', function ($query3) use ($search): void {
                        $query3->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->orderBy('workshop_case_id')
            ->get()->groupBy('workshop_case_id')->map(function ($feedback_datas): array {
                $question_1 = round($feedback_datas->avg('question_1'), 2);
                $question_2 = round($feedback_datas->avg('question_2'), 2);
                $question_3 = round($feedback_datas->avg('question_3'), 2);
                $question_4 = round($feedback_datas->avg('question_4'), 2);
                $question_5 = round($feedback_datas->avg('question_5'), 2);
                $overall = round(($question_1 + $question_2 + $question_3 + $question_4 + $question_5) / 5, 2);

                return [
                    'question_1' => $question_1,
                    'question_2' => $question_2,
                    'question_3' => $question_3,
                    'question_4' => $question_4,
                    'question_5' => $question_5,
                    'case' => optional($feedback_datas->first())->workshop_case,
                    'overall' => $overall,
                ];
            });
    }
}
