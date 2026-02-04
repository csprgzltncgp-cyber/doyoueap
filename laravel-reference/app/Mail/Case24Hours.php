<?php

namespace App\Mail;

use App\Models\Cases;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Case24Hours extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(protected Cases $case, protected User $user) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (view()->exists('emails.'.$this->user->languageWithOutScope->code.'.case24hours')) {
            return $this->view('emails.'.$this->user->languageWithOutScope->code.'.case24hours')
                ->from(config('mail.from.address'), 'CGP Europe')
                ->subject(__('email.case24hours', [], $this->user->languageWithOutScope->code))
                ->with(['case' => $this->case]);
        }

        return $this->view('emails.en.case24hours')
            ->from(config('mail.from.address'), 'CGP Europe')
            ->subject(__('email.case24hours', [], $this->user->languageWithOutScope->code))
            ->with(['case' => $this->case]);
    }
}
