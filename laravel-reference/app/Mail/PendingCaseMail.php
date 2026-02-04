<?php

namespace App\Mail;

use App\Models\Cases;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class PendingCaseMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $name,
        private string $email,
        private Cases $case,
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Jelentkezés véglegesítése/Finalizing Application',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $link = URL::temporarySignedRoute(
            'employee.case.confirm', now()->addDay(), ['case' => $this->case, 'email' => $this->email]
        );

        return new Content(
            view: 'emails.pending-case',
            with: [
                'link' => $link,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
