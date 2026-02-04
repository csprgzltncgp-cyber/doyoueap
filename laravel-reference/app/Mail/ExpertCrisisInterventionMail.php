<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExpertCrisisInterventionMail extends Mailable
{
    /**
     * @var User
     */
    public $user;

    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, public $case)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (view()->exists('emails.'.$this->user->languageWithOutScope->code.'.expert_to_crisis_intervention')) {
            return $this->view('emails.'.$this->user->languageWithOutScope->code.'.expert_to_crisis_intervention')
                ->subject(__('email.crisis_meditation', [], $this->user->languageWithOutScope->code))
                ->with(['user' => $this->user, 'case' => $this->case]);
        }

        return $this->view('emails.en.expert_to_crisis_intervention')
            ->subject(__('email.crisis_meditation', [], $this->user->languageWithOutScope->code))
            ->with(['user' => $this->user, 'case' => $this->case]);
    }
}
