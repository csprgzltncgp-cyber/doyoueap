<?php

namespace App\Traits;

trait ActivityIdPrefixTrait
{
    public function getActivityIdPref($contract_holder_id): string
    {
        return match ($contract_holder_id) {
            1 => 'lw',
            2 => 'cgp',
            3 => 'cp',
            4 => 'op',
            default => 'p',
        };
    }
}
