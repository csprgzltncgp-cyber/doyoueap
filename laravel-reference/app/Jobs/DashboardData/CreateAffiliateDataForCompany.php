<?php

namespace App\Jobs\DashboardData;

use App\Enums\DashboardDataType;
use App\Enums\InvoicingType;
use App\Helpers\CurrencyCached;
use App\Models\Cases;
use App\Models\Company;
use App\Models\Country;
use App\Models\CrisisCase;
use App\Models\DashboardData;
use App\Models\OtherActivity;
use App\Models\User;
use App\Models\WorkshopCase;
use App\Scopes\CountryScope;
use App\Traits\InvoiceHelper\ContractHolderTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class CreateAffiliateDataForCompany implements ShouldQueue
{
    use ContractHolderTrait, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    public $timeout;

    /**
     * @var int
     */
    public $tries;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Company $company, public Carbon $from, public Carbon $to)
    {
        $this->timeout = app()->environment('production') ? 120 : 60;
        $this->tries = app()->environment('production') ? 3 : 1;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // try {
        Cases::query()
            ->withoutGlobalScope(CountryScope::class)
            ->where('company_id', $this->company->id)
            ->has('consultations')
            ->with('company', 'country', 'values', 'experts', 'consultations', 'consultations.expert', 'consultations.expert.invoice_datas')
            ->whereNotIn('status', ['assigned_to_expert', 'employee_contacted', 'opened'])
            ->whereBetween('confirmed_at', [$this->from->startOfDay(), $this->to->endOfDay()])
            ->get()->each(function ($case): void {
                $this->generate_case_data($case);
            });

        WorkshopCase::query()
            ->with(['workshop', 'workshop.company', 'workshop.country'])
            ->where('company_id', $this->company->id)
            ->whereNotNull('expert_price')
            ->whereNotNull('expert_currency')
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [$this->from->startOfDay(), $this->to->endOfDay()])
            ->get()->each(function ($workshop_case): void {
                $this->generate_workshop_data($workshop_case);
            });

        CrisisCase::query()
            ->with(['crisis_intervention', 'crisis_intervention.company', 'crisis_intervention.country'])
            ->where('company_id', $this->company->id)
            ->whereNotNull('expert_price')
            ->whereNotNull('expert_currency')
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [$this->from->startOfDay(), $this->to->endOfDay()])
            ->get()->each(function ($crisis_case): void {
                $this->generate_crisis_data($crisis_case);
            });

        OtherActivity::query()
            ->with(['company', 'country'])
            ->where('company_id', $this->company->id)
            ->whereNotNull('user_price')
            ->whereNotNull('user_currency')
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [$this->from->startOfDay(), $this->to->endOfDay()])
            ->get()->each(function ($other_activity): void {
                $this->generate_other_activity_data($other_activity);
            });
        // } catch (Throwable $th) {
        //     if ($this->attempts() > $this->tries - 1) {
        //         throw $th;
        //     }

        //     $this->release($this->timeout);
        // }
    }

    private function generate_other_activity_data(OtherActivity $other_activiy): void
    {
        $converter = new CurrencyCached(60 * 60 * 24);

        try {
            $amount = $converter->convert($other_activiy->user_price, 'EUR', strtoupper((string) $other_activiy->user_currency));
        } catch (Exception) {
            $amount = 0;
        }

        $contract_holder = $this->get_contract_holder($other_activiy->company, $other_activiy->country);

        if (! $contract_holder || $amount === 0) {
            return;
        }

        DashboardData::query()->create([
            'type' => DashboardDataType::TYPE_AFFILIATE_DATA,
            'data' => [
                'company' => $other_activiy->company->id,
                'country' => $other_activiy->country->id,
                'contract_holder' => $contract_holder,
                'amount' => $amount,
                'from' => $this->from->format('Y-m-d'),
                'to' => $this->to->format('Y-m-d'),
            ],
        ]);
    }

    private function generate_crisis_data(CrisisCase $crisis_case): void
    {
        $converter = new CurrencyCached(60 * 60 * 24);

        try {
            $amount = $converter->convert($crisis_case->expert_price, 'EUR', strtoupper((string) $crisis_case->expert_currency));
        } catch (Exception) {
            $amount = 0;
        }

        $contract_holder = $this->get_contract_holder($crisis_case->crisis_intervention->company, $crisis_case->crisis_intervention->country);

        if (! $contract_holder || $amount === 0) {
            return;
        }

        DashboardData::query()->create([
            'type' => DashboardDataType::TYPE_AFFILIATE_DATA,
            'data' => [
                'company' => $crisis_case->crisis_intervention->company->id,
                'country' => $crisis_case->crisis_intervention->country->id,
                'contract_holder' => $contract_holder,
                'amount' => $amount,
                'from' => $this->from->format('Y-m-d'),
                'to' => $this->to->format('Y-m-d'),
            ],
        ]);
    }

    private function generate_workshop_data(WorkshopCase $workshop_case): void
    {
        $converter = new CurrencyCached(60 * 60 * 24);

        try {
            $amount = $converter->convert($workshop_case->expert_price, 'EUR', strtoupper($workshop_case->expert_currency));
        } catch (Exception) {
            $amount = 0;
        }

        $contract_holder = $this->get_contract_holder($workshop_case->workshop->company, $workshop_case->workshop->country);

        if (! $contract_holder || $amount === 0) {
            return;
        }

        DashboardData::query()->create([
            'type' => DashboardDataType::TYPE_AFFILIATE_DATA,
            'data' => [
                'company' => $workshop_case->workshop->company->id,
                'country' => $workshop_case->workshop->country->id,
                'contract_holder' => $contract_holder,
                'amount' => $amount,
                'from' => $this->from->format('Y-m-d'),
                'to' => $this->to->format('Y-m-d'),
            ],
        ]);

    }

    private function generate_case_data(Cases $case): void
    {

        $contract_holder = $this->get_contract_holder($case->company, $case->country);

        if ($contract_holder === 0) {
            return;
        }

        $expert = optional($case->consultations->first())->expert;

        if (empty($expert)) {
            return;
        }

        [$unit_price, $qty, $amount] = $this->get_invoicing_data($expert, $case);

        DashboardData::query()->create([
            'type' => DashboardDataType::TYPE_AFFILIATE_DATA,
            'data' => [
                'company' => $case->company->id,
                'country' => $case->country->id,
                'contract_holder' => $contract_holder,
                'expert' => $expert->id,
                'unit_price' => $unit_price,
                'qty' => $qty,
                'amount' => $amount,
                'from' => $this->from->format('Y-m-d'),
                'to' => $this->to->format('Y-m-d'),
            ],
        ]);
    }

    private function get_invoicing_data(User $expert, Cases $case): array
    {

        if ($expert->invoice_datas === null) {
            return [0, 0, 0];
        }

        return match ($expert->invoice_datas->invoicing_type->value) {
            InvoicingType::TYPE_NORMAL->value => $this->get_data_with_normal_invoicing($expert, $case),
            InvoicingType::TYPE_FIXED->value => $this->get_data_with_fixed_invoicing($expert, $case),
            InvoicingType::TYPE_CUSTOM->value => $this->get_data_with_custom_invoicing($expert, $case),
        };
    }

    private function get_data_with_fixed_invoicing(User $expert, Cases $case): array
    {
        $consultations_in_month = $expert->consultations()
            ->whereHas('case', fn ($query) => $query->whereNotIn('status', ['assigned_to_expert', 'employee_contacted', 'opened'])
                ->whereBetween('confirmed_at', [Carbon::parse($this->from)->startOfDay(), Carbon::parse($this->to)->endOfDay()]))
            ->count();

        if ($consultations_in_month === 0) {
            return [0, 0, 0];
        }

        if (! $expert->invoice_datas->fixed_wage) {
            return [0, 0, 0];
        }

        $unit_price = $this->convert_amount_to_eur((int) str_replace(' ', '', (string) $expert->invoice_datas->fixed_wage) / $consultations_in_month, $expert->invoice_datas->currency);

        $custom_invoice_items_amount = $this->get_custom_invoice_items_amount($expert, $case);

        return [
            $unit_price,
            $case->consultations->count(),
            round($unit_price * $case->consultations->count(), 2) + $custom_invoice_items_amount,
        ];
    }

    private function get_data_with_normal_invoicing(User $expert, Cases $case): array
    {
        $consultation_minute = (int) optional($case->values->where('case_input_id', 22)->first())->value; // 22 is a case_input_id for consultation_minute

        if ($consultation_minute === 116 || $consultation_minute === 117) {
            $expert_price = (int) str_replace(' ', '', (string) $expert->invoice_datas->hourly_rate_50);
        } else {
            $expert_price = (int) str_replace(' ', '', (string) $expert->invoice_datas->hourly_rate_30);
        }

        $expert_currency = $expert->invoice_datas->currency;

        if ($expert_currency === '' || $expert_currency === '0') {
            return [0, $case->consultations->count(), 0];
        }

        if ($expert_price === 0) {
            return [0, $case->consultations->count(), 0];
        }

        $expert_price = $this->convert_amount_to_eur((float) $expert_price, $expert->invoice_datas->currency);

        $custom_invoice_items_amount = $this->get_custom_invoice_items_amount($expert, $case);

        return [
            $expert_price,
            $case->consultations->count(),
            round($expert_price * $case->consultations->count(), 2) + $custom_invoice_items_amount,
        ];
    }

    private function get_data_with_custom_invoicing(User $expert, Cases $case): array
    {
        $custom_invoice_items_amount = $this->get_custom_invoice_items_amount($expert, $case);

        return [0, $case->consultations->count(), $custom_invoice_items_amount];
    }

    private function get_custom_invoice_items_amount(User $expert, Cases $case): float
    {
        if ($expert->custom_invoice_items->count() === 0) {
            return 0;
        }

        $consultations_in_month = $expert->consultations()
            ->whereHas('case', fn ($query) => $query->where('country_id', $case->country_id)
                ->whereNotIn('status', ['assigned_to_expert', 'employee_contacted', 'opened'])
                ->whereBetween('confirmed_at', [Carbon::parse($this->from)->startOfDay(), Carbon::parse($this->to)->endOfDay()]))
            ->count();

        $custom_invoice_amount = $expert->custom_invoice_items()->where('country_id', $case->country_id)->sum('amount');

        $converted_price = $this->convert_amount_to_eur($custom_invoice_amount, $expert->invoice_datas->currency);

        return round(($converted_price / $consultations_in_month) * $case->consultations->count(), 2);
    }

    private function convert_amount_to_eur(float $amount, string $valuta): float
    {
        $converter = new CurrencyCached(60 * 60 * 24);

        return $converter->convert($amount, 'EUR', strtoupper($valuta));
    }

    private function get_contract_holder(Company $company, Country $country): int
    {
        return (int) optional($company->org_datas()->where('country_id', $country->id)->first())->contract_holder_id;
    }
}
