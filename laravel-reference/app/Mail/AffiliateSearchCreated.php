<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AffiliateSearchCreated extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(private $affiliate_search) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (view()->exists('emails.'.$this->affiliate_search->to->languageWithOutScope->code.'.affiliate_search_created')) {
            return $this->view('emails.'.$this->affiliate_search->to->languageWithOutScope->code.'.affiliate_search_created')
                ->from($this->affiliate_search->from->email, $this->affiliate_search->from->name)
                ->subject(__('email.affiliate_search_created'))
                ->with(['affiliate_search' => $this->affiliate_search]);
        }

        return $this->view('emails.en.affiliate_search_created')
            ->from($this->affiliate_search->from->email, $this->affiliate_search->from->name)
            ->subject(__('email.affiliate_search_created'))
            ->with(['affiliate_search' => $this->affiliate_search]);
    }
}
