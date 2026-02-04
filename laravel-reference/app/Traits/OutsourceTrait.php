<?php

namespace App\Traits;

trait OutsourceTrait
{
    public function is_outsourceable(): bool
    {
        return
            (isset($this->company_id)
                &&
                isset($this->company_contact_email)
                &&
                isset($this->company_contact_phone)
                &&
                isset($this->country_id)
                &&
                isset($this->city_id)
                &&
                isset($this->date)
                &&
                isset($this->start_time)
                &&
                isset($this->end_time)
                &&
                isset($this->full_time)
                &&
                isset($this->activity_id)
                &&
                isset($this->language_id))
            || isset($this->expert_id);
    }

    public function is_outsourced(): bool
    {
        return isset($this->expert_id);
    }
}
