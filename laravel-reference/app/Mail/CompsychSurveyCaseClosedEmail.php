<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompsychSurveyCaseClosedEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(protected string $username, protected array $links, protected string $language_code) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (view()->exists('emails.'.$this->language_code.'.compsych_survey_case_closed')) {
            return $this->view('emails.'.$this->language_code.'.compsych_survey_case_closed')
                ->from(config('mail.from.address'), 'CGP Europe')
                ->subject(__('email.compsych_survey.case_closed.subject', [], $this->language_code))
                ->with(['username' => $this->username, 'links' => $this->links]);
        }

        return $this->view('emails.en.compsych_survey_case_closed')
            ->from(config('mail.from.address'), 'CGP Europe')
            ->subject(__('email.case24hours', [], 'en'))
            ->with(['username' => $this->username, 'links' => $this->links]);
    }
}
