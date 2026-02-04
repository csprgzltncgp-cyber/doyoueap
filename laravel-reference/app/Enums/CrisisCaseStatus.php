<?php

namespace App\Enums;

enum CrisisCaseStatus: int
{
    case OUTSOURCED = 1;
    case PRICE_ACCEPTED = 2;
    case CLOSED = 3;
}
