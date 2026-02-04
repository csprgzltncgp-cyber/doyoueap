<?php

namespace App\Traits\InvoiceHelper;

use App\Models\Cases;
use App\Models\Company;
use App\Models\Consultation;
use Carbon\Carbon;

trait ContractHolderTrait
{
    public function get_optum_consultations_number(Carbon $from, Carbon $to, int $permission_id)
    {
        $optum_company_ids = Company::query()
            ->whereHas('org_datas', function ($query) {
                return $query->where('contract_holder_id', 4); // 4 is contact holder id for Optum
            })->pluck('id'); // 4 is contact holder id for Optum

        return Cases::query()
            ->withCount('consultations')
            ->whereNotIn('status', ['assigned_to_expert', 'employee_contacted', 'opened'])
            ->whereIn('company_id', $optum_company_ids)
            ->whereBetween('confirmed_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()])
            ->whereHas('values', function ($query) use ($permission_id) {
                return $query->where('case_input_id', 7)->where('value', $permission_id); // 7 is case input id for permission
            })
            ->get()->reduce(fn ($carry, $case): int => (int) $case->consultations_count + (int) $carry, 0);
    }

    public function get_compsych_consultations_number(Carbon $from, Carbon $to, int $permission_id): int
    {
        $compsych_company_ids = Company::query()
            ->whereHas('org_datas', function ($query) {
                return $query->where('contract_holder_id', 3); // 3 is contact holder id for Compsych
            })->pluck('id');

        return Consultation::query()
            ->whereHas('case', function ($query) use ($compsych_company_ids, $permission_id): void {
                $query
                    ->whereIn('company_id', $compsych_company_ids)
                    ->whereHas('values', function ($query) use ($permission_id) {
                        return $query->where('case_input_id', 7)->where('value', $permission_id); // 7 is case input id for permission
                    });
            })->where('created_at', '>=', Carbon::parse($from)->startOfDay())
            ->where('created_at', '<=', Carbon::parse($to)->endOfDay())
            ->count();
    }

    public function get_compsych_well_being_consultations_number(Carbon $from, Carbon $to, bool $first_only): int
    {
        $compsych_company_ids = Company::query()
            ->whereHas('org_datas', function ($query) {
                return $query->where('contract_holder_id', 3); // 3 is contact holder id for Compsych
            })->pluck('id');

        $consultations = Consultation::query()
            ->whereHas('case', function ($query) use ($compsych_company_ids): void {
                $query
                    ->whereIn('company_id', $compsych_company_ids)
                    ->whereHas('values', function ($query) {
                        return $query->where('case_input_id', 7)->where('value', 16); // 7 is case input id for permission
                    });
            })->where('created_at', '>=', Carbon::parse($from)->startOfDay())
            ->where('created_at', '<=', Carbon::parse($to)->endOfDay())
            ->get();

        $consultations_count = [
            '30' => [],
            '15' => [],
        ];

        $consultations->each(function (Consultation $consultation) use (&$consultations_count): void {

            // Get first consultation in the case
            $first_consultation = $consultation->case->consultations()->orderBy('created_at')->first();

            // IF the first consultation happened in the invoicing period, count it as 30 min, ELSE 15 min
            if ($first_consultation->created_at->isSameMonth($consultation->created_at) && ! in_array($first_consultation->case_id, $consultations_count['30'])) {
                $consultations_count['30'][] = $first_consultation->case_id;
            } else {
                $consultations_count['15'][] = $consultation->case_id;
            }
        });

        return ($first_only) ? count($consultations_count['30']) : count($consultations_count['15']);
    }
}
