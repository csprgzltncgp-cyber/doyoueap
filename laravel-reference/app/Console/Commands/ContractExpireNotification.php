<?php

namespace App\Console\Commands;

use App\Mail\ContractExpireMail;
use App\Models\OrgData;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContractExpireNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract-expire-notification:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Contract expire notification (2 months)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $add_month = Carbon::today()->addMonths(2)->format('Y-m-d');

        $org_datas = OrgData::query()
            ->where('contract_holder_id', 2)
            ->whereDate('contract_date_end', $add_month)
            ->get();

        foreach ($org_datas as $org_data) {
            try {
                Mail::to('peter.janky@cgpeu.com')
                    ->cc('barbara.kiss@cgpeu.com')
                    ->send(new ContractExpireMail($org_data->company->name, $org_data->country->name, $org_data->contract_date_end));
            } catch (Exception $e) {
                Log::error('Error sending contract expire notification email: '.$e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
