<?php

namespace App\Mail;

use App\Models\Cases;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Case3Months extends Mailable
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
        return $this->view('emails.'.$this->user->languageWithOutScope->code.'.case3months')
            ->from(config('mail.from.address'), 'CGP Europe')
            ->subject(__('email.case3months', [], $this->user->languageWithOutScope->code))
            ->with(['case' => $this->case]);
    }
}
