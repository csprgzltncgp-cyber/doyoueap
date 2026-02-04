<?php

namespace App\Console\Commands;

use App\Enums\InvoicingType;
use App\Models\ExpertConsultationCount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CountFixedPriceExpertConsultations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:count-fixed-price-expert-consultations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count and store the consultations of fixed price experts that were held in the current month';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $experts = User::query()
            ->whereHas('invoice_datas', fn ($q) => $q->where('invoicing_type', InvoicingType::TYPE_FIXED))
            ->whereHas('consultations', fn ($q) => $q->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]))
            ->get();

        // Count consultation for additinal experts who are not fixed price.
        $other_experts = User::query()
            ->whereIn('id', config('count-expert-consultations'))
            ->whereNotIn('id', $experts->pluck('id')->toArray())
            ->get();

        $experts->merge($other_experts)->each(function (User $expert): void {
            $count = $expert->consultations->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();

            (new ExpertConsultationCount)->create([
                'user_id' => $expert->id,
                'count' => $count,
                'month' => Carbon::now()->format('Y-m'),
            ]);
        });
    }
}
