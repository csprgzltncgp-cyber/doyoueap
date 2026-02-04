<?php

namespace App\Jobs;

use App\Enums\DashboardDataType;
use App\Models\DashboardData;
use App\Traits\TotalRiport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContractHolderCaseData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TotalRiport;

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
    public function __construct(protected $contract_holder)
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
            $contract_holder_calculated_records = [];
            $contract_holder_calculated_records[$this->contract_holder->name] = $this->get_riport_data(null, $this->contract_holder->id);

            DashboardData::query()->create([
                'type' => DashboardDataType::TYPE_CONTRACT_HOLDER_DATA->value,
                'data' => $contract_holder_calculated_records,
            ]);
        } catch (Throwable $th) {
            if ($this->attempts() > $this->tries - 1) {
                Log::info("Contract holder data generation with id: {$this->contract_holder} failed after {$this->tries} attempts!");
                throw $th;
            }

            $this->release(120);

            return;
        }
    }
}
