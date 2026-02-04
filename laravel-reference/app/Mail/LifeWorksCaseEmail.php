<?php

namespace App\Mail;

use App\Models\TelusCaseCode;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class LifeWorksCaseEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(protected TelusCaseCode $case_code, protected string $company) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('eap@cgpeu.com', 'EAP Team'),
            subject: $this->company.' case - #'.$this->case_code->case->case_identifier,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $url = URL::temporarySignedRoute(
            name: 'telus-case.show',
            expiration: now()->addDays(5),
            parameters: ['code' => $this->case_code]
        );

        return new Content(
            view: 'emails.lifeworks_case_email',
            with: ['url' => $url]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
