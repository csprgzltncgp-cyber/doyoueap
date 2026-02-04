<?php

namespace App\Mail;

use App\Models\Cases;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Case5Days extends Mailable
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
        if (view()->exists('emails.'.$this->user->languageWithOutScope->code.'.case5days')) {
            return $this->view('emails.'.$this->user->languageWithOutScope->code.'.case5days')
                ->from(config('mail.from.address'), 'CGP Europe')
                ->subject(__('email.case5days', [], $this->user->languageWithOutScope->code))
                ->with(['case' => $this->case]);
        }

        return $this->view('emails.en.case5days')
            ->from(config('mail.from.address'), 'CGP Europe')
            ->subject(__('email.case5days', [], $this->user->languageWithOutScope->code))
            ->with(['case' => $this->case]);
    }
}
