<?php

namespace App\Mail\Pulso;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OuttakeEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(private $language, private $case_identifier) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (view()->exists('emails.'.$this->language.'.pulso_outtake')) {
            return $this->view('emails.'.$this->language.'.pulso_outtake')
                ->subject(__('email.pulso'))
                ->with(['case_identifier' => $this->case_identifier]);
        }

        return $this->view('emails.en.pulso_outtake')
            ->subject(__('email.pulso'))
            ->with(['case_identifier' => $this->case_identifier]);
    }
}
