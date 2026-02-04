<?php

namespace App\Jobs;

use App\Enums\DashboardDataType;
use App\Models\ContractHolder;
use App\Models\Country;
use App\Models\DashboardData;
use App\Traits\EapOnline\Riport;
use App\Traits\TotalRiport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateTotalRiport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, Riport, SerializesModels, TotalRiport;

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
    public function __construct(protected $type)
    {
        $this->timeout = app()->environment('production') ? 600 : 60;
        $this->tries = app()->environment('production') ? 3 : 1;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            switch ($this->type) {
                case DashboardDataType::TYPE_COUNTRY_CASE_DATA->value:

                    DashboardData::query()
                        ->where('type', DashboardDataType::TYPE_COUNTRY_CASE_DATA->value)
                        ->delete();

                    // COUNTRIES DATA
                    $countries = Country::query()->get();
                    $countries_calculated_records = [];

                    foreach ($countries as $country) {
                        $countries_calculated_records[$country->name] = $this->get_riport_data($country);
                    }

                    $countries_calculated_records['all_country'] = $this->merge_and_sum_riport_values($countries_calculated_records);

                    DashboardData::query()->create([
                        'type' => DashboardDataType::TYPE_COUNTRY_CASE_DATA->value,
                        'data' => $countries_calculated_records,
                    ]);

                    Log::info('[JOB][CreateTotalRiport] Country case data created');

                    break;

                case DashboardDataType::TYPE_COUNTRY_INVOICE_DATA->value:

                    DashboardData::query()
                        ->where('type', DashboardDataType::TYPE_COUNTRY_INVOICE_DATA->value)
                        ->delete();

                    // COUNTRIES DATA
                    $countries = Country::query()->get();
                    $countries_calculated_invoice_total = [];

                    foreach ($countries as $country) {
                        $countries_calculated_invoice_total[$country->name] = $this->get_country_expert_invoice_total($country->id);
                    }

                    DashboardData::query()->create([
                        'type' => DashboardDataType::TYPE_COUNTRY_INVOICE_DATA->value,
                        'data' => $countries_calculated_invoice_total,
                    ]);

                    Log::info('[JOB][CreateTotalRiport] Country invoice data created');

                    break;

                case DashboardDataType::TYPE_CONTRACT_HOLDER_DATA->value:

                    DashboardData::query()
                        ->where('type', DashboardDataType::TYPE_CONTRACT_HOLDER_DATA->value)
                        ->delete();

                    // CONTACT HOLDER DATA (includes CGP)
                    $contract_holders = ContractHolder::query()->get();

                    foreach ($contract_holders as $contract_holder) {
                        ContractHolderCaseData::dispatch($contract_holder);
                    }

                    Log::info('[JOB][CreateTotalRiport] Contract holders jobs created');

                    break;
            }
        } catch (Throwable $th) {
            if ($this->attempts() > $this->tries - 1) {
                Log::info("Total riport generation failed after {$this->tries} attempts!");
                throw $th;
            }

            $this->release(120);

            return;
        }
    }
}
