<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VolumeRequestEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        private readonly string $language,
        private readonly ?string $salutation,
        private readonly string $sender,
        private readonly string $signed_link
    ) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $view = 'emails.en.volume_request';

        if (view()->exists('emails.'.$this->language.'.volume_request')) {
            $view = 'emails.'.$this->language.'.volume_request';
        }

        return $this->view($view)
            ->subject(__('email.volume_request_subject', [], $this->language))
            ->from(config('mail.from.address'), 'CGP Europe')
            ->with([
                'salutation' => $this->salutation,
                'sender' => $this->sender,
                'signed_link' => $this->signed_link,
                'month' => Carbon::now()->subMonthNoOverflow()->format('m'),
            ]);
    }
}
