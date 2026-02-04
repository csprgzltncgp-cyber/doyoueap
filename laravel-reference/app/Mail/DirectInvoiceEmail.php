<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DirectInvoiceEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(protected $plus_attachments, protected $language, $custom_subject)
    {
        $this->subject($custom_subject);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('finance@cgpeu.com', 'CGP Europe')
        );
    }

    public function content(): Content
    {
        if (view()->exists('emails.'.$this->language.'.direct-invoice')) {
            return new Content(
                view: 'emails.'.$this->language.'.direct-invoice',
            );
        }

        return new Content(
            view: 'emails.en.direct-invoice',
        );
    }

    public function attachments()
    {
        return $this->plus_attachments;
    }
}
