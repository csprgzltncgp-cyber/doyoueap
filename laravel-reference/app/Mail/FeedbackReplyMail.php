<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackReplyMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(protected string $message) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('eap@cgpeu.com', 'EAP Feedback')
            ->subject('EAP Feedback')
            ->view('emails.feedback_reply')
            ->with(['mail_message' => $this->message]);
    }
}
