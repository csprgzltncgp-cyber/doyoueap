<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExpertCrisisInterventionPriceChangeMail extends Mailable
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
        if (view()->exists('emails.'.$this->user->languageWithOutScope->code.'.crisis_intervention_price_change_to_expert')) {
            return $this->view('emails.'.$this->user->languageWithOutScope->code.'.crisis_intervention_price_change_to_expert')
                ->subject(__('email.crisis_price_change', [], $this->user->languageWithOutScope->code))
                ->with(['user' => $this->user, 'case' => $this->case]);
        }

        return $this->view('emails.en.crisis_intervention_price_change_to_expert')
            ->subject(__('email.crisis_price_change', [], $this->user->languageWithOutScope->code))
            ->with(['user' => $this->user, 'case' => $this->case]);
    }
}
