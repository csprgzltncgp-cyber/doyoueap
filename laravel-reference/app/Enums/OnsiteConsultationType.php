<?php

namespace App\Enums;

enum OnsiteConsultationType: string
{
    case WITH_EXPERT = 'with_expert';
    case WITHOUT_EXPERT = 'without_expert';
    case ONLINE_WITH_EXPERT = 'online_with_expert';
}
