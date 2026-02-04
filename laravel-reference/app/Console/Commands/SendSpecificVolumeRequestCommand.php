<?php

namespace App\Console\Commands;

use App\Enums\VolumeRequestStatusEnum;
use App\Mail\VolumeRequestEmail;
use App\Models\VolumeRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SendSpecificVolumeRequestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-specific-volume-request-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Log::info('[COMMAND][SendSpecificVolumeRequestCommand]: fired!');

        VolumeRequest::query()
            ->where('status', VolumeRequestStatusEnum::PENDING)
            ->where('date', Carbon::now()->subMonthWithNoOverflow()->startOfMonth())
            ->whereIn('id', [829, 830, 831, 832])
            ->get()->each(function (VolumeRequest $request): void {
                try {
                    if (! $request->volume->invoice_item || ! $request->volume->invoice_item->data_request_email) {
                        return;
                    }

                    $company_account_admin = optional($request->volume->invoice_item->company->activity_plans->first())->user;
                    $company_user = $request
                        ->volume
                        ->invoice_item
                        ->company
                        ->clientUsers()
                        ->when($request->volume->invoice_item->country_id, function ($query) use ($request): void {
                            $query->where('country_id', $request->volume->invoice_item->country_id);
                        })
                        ->first();

                    // Signed route to enter the client dashboard sedn data page without login
                    $signed_link = URL::temporarySignedRoute(
                        'client.login',
                        Carbon::now()->endOfMonth(),
                        [
                            'client_id' => $company_user->id,
                            'page' => 'volume_request',
                            'month' => Carbon::now()->subMonthNoOverflow()->month,
                        ]
                    );

                    Mail::to($request->volume->invoice_item->data_request_email)
                        ->send(new VolumeRequestEmail(
                            $company_user->languageWithOutScope->code,
                            $request->volume->invoice_item->data_request_salutation,
                            ($company_account_admin) ? $company_account_admin->name : 'CGP Europe',
                            $signed_link
                        ));

                    $request->update([
                        'email_sent_at' => Carbon::now(),
                    ]);
                } catch (Exception $e) {
                    Log::info('Failed to send volume request email for company ID:'.optional($request->volume->invoice_item->company)->id.' ERROR:'.$e);
                }
            });
    }
}
