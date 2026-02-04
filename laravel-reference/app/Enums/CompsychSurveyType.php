<?php

namespace App\Enums;

enum CompsychSurveyType: string
{
    case CASE_CREATED = 'case_created';
    case CASE_CLOSED = 'case_closed';
    case AFTER_90_DAY = 'after_90_day';
}
