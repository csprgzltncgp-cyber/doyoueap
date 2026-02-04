<?php

namespace App\Mail;

use App\Models\Cases;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AssignCaseMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var Cases
     */
    public $case;

    public $operator;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Cases $case)
    {
        $this->user = $user;
        $this->case = $case;
        $this->operator = User::query()->where('id', $this->case->created_by)->first();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (view()->exists('emails.'.$this->user->languageWithOutScope->code.'.expert_to_case')) {
            return $this->view('emails.'.$this->user->languageWithOutScope->code.'.expert_to_case')
                ->subject(Carbon::parse($this->case->created_at)->format('Y-m-d'))
                ->with(['user' => $this->user, 'case' => $this->case, 'operator' => $this->operator]);
        }

        return $this->view('emails.en.expert_to_case')
            ->subject(Carbon::parse($this->case->created_at)->format('Y-m-d'))
            ->with(['user' => $this->user, 'case' => $this->case, 'operator' => $this->operator]);
    }
}
