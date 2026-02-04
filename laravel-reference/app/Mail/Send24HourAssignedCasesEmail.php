<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Send24HourAssignedCasesEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected ?string $file_path, protected ?string $file_path_wpo, public Carbon $date_from, public Carbon $date_to)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), 'CGP Europe'),
            subject: 'Kiközvetített esetek '.$this->date_from->format('Y.m.d').'-'.$this->date_to->format('Y.m.d'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.24_hour_assigned_cases_email',
            with: ['from' => $this->date_from, 'to' => $this->date_to]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $attachment = collect([]);

        if ($this->file_path) {
            $attachment->push(Attachment::fromPath($this->file_path));
        }

        if ($this->file_path_wpo) {
            $attachment->push(Attachment::fromPath($this->file_path_wpo));
        }

        return $attachment->toArray();
    }
}
