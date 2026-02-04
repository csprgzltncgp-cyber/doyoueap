<?php

namespace App\Jobs\VolumeRequest;

use App\Enums\VolumeRequestStatusEnum;
use App\Mail\VolumeRequestEmail;
use App\Models\Company;
use App\Models\VolumeRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SendVolumeRequestsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Company $company) {}

    /**
     * This function will be called every 2 days at the last week of the month and the first week of the month
     */
    public function handle(): void
    {
        Log::info('[JOB][SendVolumeRequestsJob] fired!');

        $volumes = $this->company->invoice_items()->whereHas('volume')->get()->pluck('volume');

        VolumeRequest::query()
            ->where('status', VolumeRequestStatusEnum::PENDING)
            ->whereNull('email_sent_at')
            ->where('date', Carbon::now()->subMonthWithNoOverflow()->startOfMonth())
            ->whereIn('volume_id', $volumes->pluck('id'))
            ->get()->each(function (VolumeRequest $request): void {
                try {
                    if ($request->volume->invoice_item && $request->volume->invoice_item->data_request_email) {

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
                    }
                } catch (Exception $e) {
                    Log::info('Failed to send volume request email for company ID:'.optional($request->volume->invoice_item->company)->id.' ERROR:'.$e);
                }
            });
    }
}
