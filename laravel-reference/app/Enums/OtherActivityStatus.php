<?php

namespace App\Enums;

enum OtherActivityStatus: int
{
    case STATUS_OUTSOURCED = 1;
    case STATUS_IN_PROGRESS = 2;
    case STATUS_CLOSED = 3;
}
