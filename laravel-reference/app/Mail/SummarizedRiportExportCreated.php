<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SummarizedRiportExportCreated extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(private $name, private $quarter, private $filename) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // if (view()->exists('emails.'.$this->user->languageWithOutScope->code.'.summarized_riport_created')) {
        //     return $this->view('emails.'.$this->user->languageWithOutScope->code.'.summarized_riport_created')
        //         ->subject(__('email.summarized_riport_created'))
        //         ->with([
        //             'user' => $this->user,
        //             'quarter' => $this->quarter,
        //             'filename' => $this->filename,
        //         ]);
        // } else {
        return $this->view('emails.en.summarized_riport_created')
            ->subject(__('email.summarized_riport_created'))
            ->with([
                'name' => $this->name,
                'quarter' => $this->quarter,
                'filename' => $this->filename,
            ]);
        // }
    }
}
