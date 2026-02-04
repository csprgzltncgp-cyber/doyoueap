<?php

namespace App\Enums;

use ReflectionEnum;

enum ConsultationType: int
{
    case PERSONAL = 80;
    case PHONE = 81;
    case CHAT = 82;
    case VIDEO = 83;

    public static function fromName(string $name): mixed
    {
        $reflection = new ReflectionEnum(ConsultationType::class);

        if (! $reflection->hasCase($name)) {
            return null;
        }

        return $reflection->getCase($name)->getValue();
    }
}
