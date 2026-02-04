<?php

namespace App\Jobs;

use App\Enums\CompsychSurveyType;
use App\Models\Cases;
use App\Services\CompsychSurveyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCompsychSurveyEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $email;

    private string $username;

    private int $case_identifier;

    /**
     * Create a new job instance.
     */
    public function __construct(private Cases $case)
    {
        $this->username = $case->values->where('case_input_id', 4)->first()->value; // name of the client
        $this->email = $case->values->where('case_input_id', 18)->first()->value;  // email
        $this->case_identifier = (int) $case->case_identifier;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $compsych_survey_form_service = new CompsychSurveyService(CompsychSurveyType::AFTER_90_DAY);
            $compsych_survey_form_service->send_mail(
                $this->username,
                $this->email,
                $this->case_identifier,
            );

            $this->case->email_sent_compsych_survey_3month = true;

            $this->case->save();
        }
    }
}
