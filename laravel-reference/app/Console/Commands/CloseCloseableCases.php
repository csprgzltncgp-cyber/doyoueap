<?php

namespace App\Console\Commands;

use App\Models\Cases;
use App\Models\InvoiceCaseData;
use App\Scopes\CountryScope;
use App\Traits\CaseCloseTrait;
use App\Traits\EapOnline\OnlineTherapyTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloseCloseableCases extends Command
{
    use CaseCloseTrait;
    use OnlineTherapyTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cases:close-closeable-cases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close cases that are closeable and have not been closed yet.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Cases::query()
            ->withoutGlobalScope(CountryScope::class)
            ->with('experts')
            ->where('status', '!=', 'confimrmed')
            ->whereNull('confirmed_at')
            ->whereNull('confirmed_by')
            ->where('id', 90792)
            ->get()->filter(function ($case): bool {
                $user = $case->case_accepted_expert();

                /**
                 * Ignore LPP SA (843) company cases
                 */
                if ($case->company_id === 843) {
                    return false;
                }

                return $case->isCloseable($user)['closeable'] && Carbon::parse($case->updated_at)->addDay()->isPast();
            })->each(function ($case): void {
                $user = $case->case_accepted_expert();

                $case->closed_by_expert = $user->id;
                $case->status = 'confirmed';
                $case->confirmed_by = $user->id;
                $case->confirmed_at = Carbon::now('Europe/Budapest');
                $case->updated_at = Carbon::now('Europe/Budapest');
                $case->save();

                $this->exclude_client_from_online_therapy($case->id);
                $this->set_intake_colsed_at_date($case->id);

                $this->send_pulso_outtake_email($case);

                // Az LPP cég esetén az elégedettségi pontszámhoz tartozó e-mail / SMS küldése
                if ($case->company_id == 843) {
                    $this->lpp_customer_satisfaction($case);
                }

                // Prezero Iberia esetén az elégedettségi pontszámhoz tartozó e-mail küldése
                if ($case->company_id === 1173) {
                    $this->prezero_iberia_customer_satisfaction($case);
                }

                $duration = optional($case->values->where('case_input_id', 22)->first())->input_value->value;

                InvoiceCaseData::query()->firstOrCreate([
                    'case_identifier' => $case->case_identifier,
                    'consultations_count' => $case->consultations->count(),
                    'expert_id' => $user->id,
                    'duration' => (int) $duration,
                    'permission_id' => (int) $case->case_type->value,
                ]);
            });

        return Command::SUCCESS;
    }
}
