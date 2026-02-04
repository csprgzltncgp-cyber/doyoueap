<?php

namespace App\Mail;

use App\Models\WorkshopCase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Workshop48Hours extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(protected WorkshopCase $workshop_case)
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.'.$this->workshop_case->user->languageWithOutScope->code.'.workshop_reminder')
            ->from(config('mail.from.address'), 'CGP Europe')
            ->subject(__('email.workshop_reminder_subject', [], $this->workshop_case->user->languageWithOutScope->code))
            ->with([
                'workshop_case' => $this->workshop_case,
            ]);
    }
}
