<?php

namespace App\Enums\BusinessBreakfast;

enum InteractionType: string
{
    case A_LATER_DATE_WOULD_BE_SUITABLE = 'A_LATER_DATE_WOULD_BE_SUITABLE';
    case I_AM_NOT_INTERESTED = 'I_AM_NOT_INTERESTED';
    case IN_THE_NEXT_2_4_MONTHS = 'IN_THE_NEXT_2_4_MONTHS';
    case IN_THE_NEXT_5_6_MONTHS = 'IN_THE_NEXT_5_6_MONTHS';
    case IN_THE_NEXT_7_8_MONTHS = 'IN_THE_NEXT_7_8_MONTHS';
}
