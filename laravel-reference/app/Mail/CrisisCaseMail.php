<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CrisisCaseMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(protected $case_identifier) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.crisis_case_mail')
            ->subject('Új Krízis hívás')
            ->with([
                'case_identifier' => $this->case_identifier,
            ]);
    }
}
