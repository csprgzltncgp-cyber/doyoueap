<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetEmail extends Mailable
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
    public function __construct(User $user, public $password)
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
        if (view()->exists('emails.'.$this->user->languageWithOutScope->code.'.password_reset_to_expert')) {
            return $this->view('emails.'.$this->user->languageWithOutScope->code.'.password_reset_to_expert')
                ->subject(__('email.password_reset', [], $this->user->languageWithOutScope->code))
                ->with(['user' => $this->user, 'password' => $this->password]);
        }

        return $this->view('emails.en.password_reset_to_expert')
            ->subject(__('email.password_reset', [], $this->user->languageWithOutScope->code))
            ->with(['user' => $this->user, 'password' => $this->password]);
    }
}
