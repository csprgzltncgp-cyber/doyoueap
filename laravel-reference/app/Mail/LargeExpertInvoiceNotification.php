<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LargeExpertInvoiceNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    public $expert_name;

    /**
     * @var string
     */
    public $amount;

    /**
     * @var string
     */
    public $date;

    /**
     * @var string
     */
    public $invoice_number;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $expert_name, string $amount, string $date, string $invoice_number)
    {
        $this->expert_name = $expert_name;
        $this->amount = $amount;
        $this->date = $date;
        $this->invoice_number = $invoice_number;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Large Expert Invoice Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.large_expert_notification',
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
