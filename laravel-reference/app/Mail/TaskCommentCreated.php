<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskCommentCreated extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(private $task, private $sender, private $reciever) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (view()->exists('emails.'.$this->reciever->languageWithOutScope->code.'.task_comment_created')) {
            return $this->view('emails.'.$this->reciever->languageWithOutScope->code.'.task_comment_created')
                ->from($this->sender->email, $this->sender->name)
                ->subject(__('email.task_comment_created'))
                ->with(['task' => $this->task, 'sender' => $this->sender, 'reciever' => $this->reciever]);
        }

        return $this->view('emails.en.task_comment_created')
            ->from($this->sender->email, $this->sender->name)
            ->subject(__('email.task_comment_created'))
            ->with(['task' => $this->task, 'sender' => $this->sender, 'reciever' => $this->reciever]);
    }
}
