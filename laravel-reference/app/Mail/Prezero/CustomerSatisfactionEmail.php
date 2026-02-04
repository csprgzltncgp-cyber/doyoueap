<?php

namespace App\Mail\Prezero;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerSatisfactionEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(private $language, private $case_identifier) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('no-reply@chestnutce.com', 'CGP Europe'),
            subject: __('email.prezero_iberia_customer_satisfaction', [], $this->language),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        if (view()->exists('emails.'.$this->language.'.prezero_iberia_customer_satisfaction')) {
            return new Content(
                view: 'emails.'.$this->language.'.prezero_iberia_customer_satisfaction',
                with: ['case_identifier' => $this->case_identifier],
            );
        }

        return new Content(
            view: 'emails.en.prezero_iberia_customer_satisfaction',
            with: ['case_identifier' => $this->case_identifier],
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
