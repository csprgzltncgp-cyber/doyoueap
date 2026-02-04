<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskCompleted extends Mailable
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
        if (view()->exists('emails.'.$this->task->from->languageWithOutScope->code.'.task_completed')) {
            return $this->view('emails.'.$this->task->from->languageWithOutScope->code.'.task_completed')
                ->from($this->task->to->email, $this->task->to->name)
                ->subject(__('email.task_completed'))
                ->with(['task' => $this->task]);
        }

        return $this->view('emails.en.task_completed')
            ->from($this->task->to->email, $this->task->to->name)
            ->subject(__('email.task_completed'))
            ->with(['task' => $this->task]);
    }
}
