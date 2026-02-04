<?php

namespace App\Traits;

trait LiveWebinarActivityId
{
    public function generateActivityId(): string
    {
        $lastRecord = $this::query()->orderByDesc('id')->first();
        $nextId = $lastRecord ? $lastRecord->id + 1 : 1;

        return "lwcgp{$nextId}";
    }
}
