<?php

namespace App\Jobs;

use App\Models\Cases;
use App\Models\CustomerSatisfaction;
use App\Models\CustomerSatisfactionValue;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateCustomerSatisfactionForCompany implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $company, public $from, public $to) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $customer_satisfaction = CustomerSatisfaction::query()->create([
            'from' => $this->from,
            'to' => $this->to,
            'company_id' => $this->company->id,
            'is_active' => false,
        ]);

        if ((is_countable($cases = $this->get_cases_between_dates($this->from, $this->to, $this->company)) ? count($cases = $this->get_cases_between_dates($this->from, $this->to, $this->company)) : 0) > 0) {
            foreach ($cases as $case) {
                CustomerSatisfactionValue::query()->create([
                    'customer_satisfaction_id' => $customer_satisfaction->id,
                    'country_id' => $case->country_id,
                    'value' => $case->customer_satisfaction,
                ]);
            }
        }

        Log::info('[JOB][CreateCustomerSatisfactionForCompany] New customer satisfaction created for: '.$this->company->name);
    }

    private function get_cases_between_dates($from, $to, $company)
    {
        return Cases::query()
            ->where('company_id', $company->id)
            ->whereNotNull(['customer_satisfaction'])
            ->whereBetween('confirmed_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()])
            ->get();
    }
}
