<?php

namespace App\Enums;

enum CaseExpertStatus: int
{
    case ASSIGNED_TO_EXPERT = -1; // Case assigned to expert. (Aftre the case was created or if another expert rejected the case and it got re-assigned)
    case ACCEPTED = 1; // Expert started working on the case. (Created the first consultation in the case).
    case REJECTED = 0; // Expert rejected the case. (For reasons)
}
