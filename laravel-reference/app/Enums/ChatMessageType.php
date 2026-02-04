<?php

namespace App\Enums;

enum ChatMessageType: string
{
    case EXPERT = 'expert';
    case USER = 'user';
}
