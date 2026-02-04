<?php

namespace App\Enums;

enum WorkshopCaseExpertStatus: int
{
    case ACCEPTED = 1;
    case EXPERT_PRICE_CHANGE = 2;
    case ADMIN_PRICE_CHANGE = 3;
    case DENIED = 4;
}
