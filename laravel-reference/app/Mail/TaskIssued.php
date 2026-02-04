<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskIssued extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(private $task) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (view()->exists('emails.'.$this->task->to->languageWithOutScope->code.'.task_issued')) {
            return $this->view('emails.'.$this->task->to->languageWithOutScope->code.'.task_issued')
                ->from($this->task->from->email, $this->task->from->name)
                ->subject(__('email.task_issued'))
                ->with(['task' => $this->task]);
        }

        return $this->view('emails.en.task_issued')
            ->from($this->task->from->email, $this->task->from->name)
            ->subject(__('email.task_issued'))
            ->with(['task' => $this->task]);
    }
}
