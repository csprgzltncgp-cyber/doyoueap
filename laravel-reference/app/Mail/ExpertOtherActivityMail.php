<?php

namespace App\Mail;

use App\Enums\OtherActivityType;
use App\Models\OtherActivity;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExpertOtherActivityMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $user;

    public $activityType;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(OtherActivity $otherActivity)
    {
        $this->user = $otherActivity->user;
        $this->activityType = $otherActivity->type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $activityName = match ($this->activityType) {
            OtherActivityType::TYPE_ORIENTATION => 'orientation',
            OtherActivityType::TYPE_HEALTH_DAY => 'health_day',
            OtherActivityType::TYPE_EXPERT_OUTPLACEMENT => 'expert_outplacement',
            default => 'orientation',
        };

        if (view()->exists('emails.'.$this->user->languageWithOutScope->code.'.expert_to_'.$activityName)) {
            return $this->view('emails.'.$this->user->languageWithOutScope->code.'.expert_to_'.$activityName)
                ->subject(__('email.'.$activityName.'_meditation', [], $this->user->languageWithOutScope->code));
        }

        return $this->view('emails.en.expert_to_'.$activityName)
            ->subject(__('email.'.$activityName.'_meditation', [], $this->user->languageWithOutScope->code));
    }
}
