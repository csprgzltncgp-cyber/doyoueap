<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExpertAfterFirstLoginMail extends Mailable
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
    public function __construct(User $user)
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
        // Check if view exists
        if (view()->exists('emails.'.$this->user->languageWithOutScope->code.'.expert_first_login')) {
            $view = 'emails.'.$this->user->languageWithOutScope->code.'.expert_first_login';
        } else {
            $view = 'emails.en.expert_first_login';
        }

        // Check if attachment is avaiable in the users language
        if (file_exists(public_path('/pdf/expert-dashboard-guide-'.$this->user->languageWithOutScope->code.'.pdf'))) {
            $file = public_path('/pdf/expert-dashboard-guide-'.$this->user->languageWithOutScope->code.'.pdf');
        } else {
            $file = public_path('/pdf/expert-dashboard-guide-en.pdf');
        }

        return $this->view($view)
            ->attach($file, [
                'as' => 'Expert Dashboard Guide.pdf',
                'mime' => 'application/pdf',
            ])
            ->subject(__('email.activation', [], $this->user->languageWithOutScope->code))
            ->with(['user' => $this->user]);

    }
}
