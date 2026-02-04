<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AffiliateSearchCompleted extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(private $affiliateSearch) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (view()->exists('emails.'.$this->affiliateSearch->from->languageWithOutScope->code.'.affiliate_search_completed')) {
            return $this->view('emails.'.$this->affiliateSearch->from->languageWithOutScope->code.'.affiliate_search_completed')
                ->from($this->affiliateSearch->to->email, $this->affiliateSearch->to->name)
                ->subject(__('email.affiliate_search_completed'))
                ->with(['affiliateSearch' => $this->affiliateSearch]);
        }

        return $this->view('emails.en.affiliate_search_completed')
            ->from($this->affiliateSearch->to->email, $this->affiliateSearch->to->name)
            ->subject(__('email.affiliate_search_completed'))
            ->with(['affiliateSearch' => $this->affiliateSearch]);
    }
}
