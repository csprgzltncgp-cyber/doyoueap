<?php

namespace App\Console\Commands;

use App\Models\Cases;
use App\Models\Company;
use App\Models\UsedConsultations;
use App\Scopes\CountryScope;
use Illuminate\Console\Command;

class GenerateConsultationUsageData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:consultation-usage-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate CGP and Affiliate expert consultation usage data.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // CGP expert cases
        $cases = Cases::query()
            ->withoutGlobalScope(CountryScope::class)
            ->with('company', 'experts')
            ->withCount('consultations')
            ->whereNotNull('confirmed_at')
            ->where('status', 'confirmed')
            ->whereHas('values', fn ($query) => $query->where('case_input_id', 7)->where('value', 1))
            ->whereHas('experts', fn ($query) => $query->whereIn('user_id', [830, 632, 999, 1039, 1040, 1056, 835, 834, 1220, 1210, 1176, 989]))
            ->get();

        // Affiliate cases
        $cases2 = Cases::query()
            ->withoutGlobalScope(CountryScope::class)
            ->with('company', 'experts')
            ->withCount('consultations')
            ->whereNotNull('confirmed_at')
            ->where('status', 'confirmed')
            ->whereHas('values', fn ($query) => $query->where('case_input_id', 7)->where('value', 1))
            ->whereHas('experts', fn ($query) => $query->whereNotIn('user_id', [830, 632, 999, 1039, 1040, 1056, 835, 834, 1220, 1210, 1176, 989]))
            ->get();

        $datas = [];

        foreach ($cases->pluck('company_id') as $company_id) {
            $datas[$company_id] = [
                'affiliate' => $this->calculate_percentage($cases2, $company_id),
                'cgp' => $this->calculate_percentage($cases, $company_id),
            ];
        }

        if ($datas !== []) {
            $cgp_sum_base = 0;
            $cgp_sum_actual = 0;

            $affiliate_sum_base = 0;
            $affiliate_sum_actual = 0;

            foreach ($datas as $item) {
                $cgp_sum_base += $item['cgp']['base_number'];
                $cgp_sum_actual += $item['cgp']['actual_number'];

                $affiliate_sum_base += $item['affiliate']['base_number'];
                $affiliate_sum_actual += $item['affiliate']['actual_number'];
            }

            $cgp_prercentage = round(($cgp_sum_actual / $cgp_sum_base) * 100);
            $afiliate_percentage = round(($affiliate_sum_actual / $affiliate_sum_base) * 100);

            $results = [
                'percentage_sum' => [
                    'cgp' => $cgp_prercentage,
                    'affiliate' => $afiliate_percentage,
                ],
                'averages' => [
                    'cgp' => $this->calculate_averages($cases),
                    'affiliate' => $this->calculate_averages($cases2),
                ],
            ];

            // Save data
            $used_consultations = new UsedConsultations([
                'cgp_employee' => true,
                'type' => UsedConsultations::TYPE_TOTAL_PERCENTAGE,
                'number_of_consultations' => '',
                'consultation_average' => '',
                'total_percentage' => $results['percentage_sum']['cgp'],
            ]);
            $used_consultations->save();

            $used_consultations = new UsedConsultations([
                'cgp_employee' => false,
                'type' => UsedConsultations::TYPE_TOTAL_PERCENTAGE,
                'number_of_consultations' => '',
                'consultation_average' => '',
                'total_percentage' => $results['percentage_sum']['affiliate'],
            ]);
            $used_consultations->save();

            foreach ($results['averages']['cgp'] as $key => $value) {
                $used_consultations = new UsedConsultations([
                    'cgp_employee' => true,
                    'type' => UsedConsultations::TYPE_AVERAGES,
                    'number_of_consultations' => $key,
                    'consultation_average' => $value,
                    'total_percentage' => '',
                ]);
                $used_consultations->save();
            }

            foreach ($results['averages']['affiliate'] as $key => $value) {
                $used_consultations = new UsedConsultations([
                    'cgp_employee' => true,
                    'type' => UsedConsultations::TYPE_AVERAGES,
                    'number_of_consultations' => $key,
                    'consultation_average' => $value,
                    'total_percentage' => '',
                ]);
                $used_consultations->save();
            }
        }

        return Command::SUCCESS;
    }

    public function calculate_percentage($cases, $company_id): array
    {
        if (! $cases->where('company_id', $company_id)->count()) {
            return [
                'actual_number' => 0,
                'base_number' => 0,
            ];
        }

        $base_number = Company::query()->where('id', $company_id)->first()->permissions->where('id', 1)->first()->getRelationValue('pivot')->number * $cases->where('company_id', $company_id)->count();
        $actual_number = $cases->where('company_id', $company_id)->sum('consultations_count');

        return ['actual_number' => $actual_number, 'base_number' => $base_number];
    }

    public function calculate_averages($cases)
    {
        $avg_data = [];

        // Company averages
        foreach ($cases->pluck('company') as $company) {
            $available_consultation = $company->permissions->where('id', 1)->first()->pivot->number;
            $used_consultations = $cases->where('company_id', $company->id)->pluck('consultations_count');
            $total = array_sum($used_consultations->toArray());
            $average = $total / (is_countable($used_consultations) ? count($used_consultations) : 0);
            $avg_data[$available_consultation][] = $average;
        }

        // SUM company averages by allowed consultation number
        foreach ($avg_data as $max_consultation => $averages) {
            $total_avg = array_sum($averages) / count($averages);
            $avg_data[$max_consultation] = round($total_avg, 1);
        }

        return collect($avg_data)->sortKeys()->toArray();
    }
}
