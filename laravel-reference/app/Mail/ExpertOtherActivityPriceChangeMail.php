<?php

namespace App\Mail;

use App\Enums\OtherActivityType;
use App\Models\OtherActivity;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExpertOtherActivityPriceChangeMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $user;

    public $activityType;

    /**
     * @var OtherActivity
     */
    public $otherActivity;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(OtherActivity $otherActivity)
    {
        $this->user = $otherActivity->user;
        $this->activityType = $otherActivity->type;
        $this->otherActivity = $otherActivity;
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

        if (view()->exists('emails.'.$this->user->languageWithOutScope->code.'.'.$activityName.'_price_change_to_expert')) {
            return $this->view('emails.'.$this->user->languageWithOutScope->code.'.'.$activityName.'_price_change_to_expert')
                ->subject(__('email.'.$activityName.'_price_change', [], $this->user->languageWithOutScope->code));
        }

        return $this->view('emails.hu.'.$activityName.'_price_change_to_expert')
            ->subject(__('email.'.$activityName.'_price_change', [], $this->user->languageWithOutScope->code));
    }
}
