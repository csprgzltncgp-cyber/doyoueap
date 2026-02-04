<?php

namespace App\Mail;

use App\Models\Cases;
use App\Models\Country;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuestionCopyToExpert extends Mailable
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var Cases
     */
    public $case;

    /**
     * @var User
     */
    public $operator;

    /**
     * @var Country
     */
    public $country;

    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Cases $case, User $operator, Country $country, public $question)
    {
        //
        $this->user = $user;
        $this->case = $case;
        $this->operator = $operator;
        $this->country = $country;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = __('email.confirmation-of-email-sent', [], $this->user->languageWithOutScope->code).' - '.Carbon::parse($this->case->created_at)->format('Y.m.d.').' - '.$this->operator->name;

        if (view()->exists('emails.'.$this->user->languageWithOutScope->code.'.question_copy_to_expert')) {
            return $this->view('emails.'.$this->user->languageWithOutScope->code.'.question_copy_to_expert')
                ->subject($subject)
                ->from($this->country->email)
                ->with(['user' => $this->user, 'case' => $this->case, 'question' => $this->question]);
        }

        return $this->view('emails.en.question_copy_to_expert')
            ->subject($subject)
            ->from($this->country->email)
            ->with(['user' => $this->user, 'case' => $this->case, 'question' => $this->question]);
    }
}
