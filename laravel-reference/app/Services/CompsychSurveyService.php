<?php

namespace App\Services;

use App\Enums\CompsychSurveyType;
use App\Mail\CompsychSurveyAfter90DayEmail;
use App\Mail\CompsychSurveyCaseClosedEmail;
use App\Mail\CompsychSurveyCaseCreatedEmail;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CompsychSurveyService
{
    protected array $links;

    public function __construct(protected CompsychSurveyType $type) {}

    public function construct_links(int $case_identifier): void
    {
        $this->links = [
            CompsychSurveyType::CASE_CREATED->value => [
                'gad_7' => "https://docs.google.com/forms/d/e/1FAIpQLScXrchIGHEL2d43KCyLpmT546jCG9dGOegdyEW6OMCX5OrsJA/viewform?usp=pp_url&entry.1454770035={$case_identifier}&pageHistory=0,1",
                'wos' => "https://docs.google.com/forms/d/e/1FAIpQLSd3aFVmWizXboOG7aDYMNUXocy5WHyl8RXrPPnPyTD1VhVnPg/viewform?usp=pp_url&entry.341094615={$case_identifier}&pageHistory=0,1",
                'phq9' => "https://docs.google.com/forms/d/e/1FAIpQLScQjEoF3I0Hzya2mV189KEphqxpLZVKDxei3AwnHz3Re0H0PA/viewform?usp=pp_url&entry.1676908527={$case_identifier}&pageHistory=0,1",
            ],

            CompsychSurveyType::CASE_CLOSED->value => [
                'net_promoter' => "https://docs.google.com/forms/d/e/1FAIpQLScJ3Ill4in5QRWq1uVVI5Rdw7tPtmk-rx17n6q18p_b0UBrxg/viewform?usp=pp_url&entry.1517573992={$case_identifier}&pageHistory=0,1",
                'customer_satisfaction_1' => "https://docs.google.com/forms/d/e/1FAIpQLScLJNDzks0ykyY4zLaPPXxdcaatQBiBRKfrBrk4zJLSKbnlCw/viewform?usp=pp_url&entry.959000310={$case_identifier}&pageHistory=0,1",
                'customer_satisfaction_2' => "https://docs.google.com/forms/d/e/1FAIpQLSdASw5IM_KoQruC4xbOueH1MmHpCaDOTklzg9tII0hy0FXXbg/viewform?usp=pp_url&entry.2052654045={$case_identifier}&pageHistory=0,1",
            ],

            CompsychSurveyType::AFTER_90_DAY->value => [
                'wos' => "https://docs.google.com/forms/d/e/1FAIpQLSd3aFVmWizXboOG7aDYMNUXocy5WHyl8RXrPPnPyTD1VhVnPg/viewform?usp=pp_url&entry.341094615={$case_identifier}&pageHistory=0,1",
            ],
        ];
    }

    public function send_mail(string $username, string $email, int $case_identifier): void
    {
        $this->construct_links($case_identifier);

        try {
            Mail::to($email)->send(match ($this->type) {
                CompsychSurveyType::CASE_CREATED => new CompsychSurveyCaseCreatedEmail($username, $this->links[$this->type->value], 'hu'),
                CompsychSurveyType::CASE_CLOSED => new CompsychSurveyCaseClosedEmail($username, $this->links[$this->type->value], 'hu'),
                CompsychSurveyType::AFTER_90_DAY => new CompsychSurveyAfter90DayEmail($username, $this->links[$this->type->value], 'hu'),
            });

            Log::info("['CompsychSurveyService']['send_mail']: Compsych survey successfully sent with Case identifier: {$case_identifier}, type: {$this->type->value}.");
        } catch (Exception $e) {
            Log::info("['CompsychSurveyService']['send_mail']: Failed! Case identifier: {$case_identifier}, type: {$this->type->value}. ERROR: {$e->getMessage()}");
        }
    }
}
