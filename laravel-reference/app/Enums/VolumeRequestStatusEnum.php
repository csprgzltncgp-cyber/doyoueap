<?php

namespace App\Enums;

enum VolumeRequestStatusEnum: string
{
    case PENDING = 'pending'; // this status means, that the request is pending
    case COMPLETED = 'completed'; // this status means, that the request was completed by the company
    case AUTO_COMPLETED = 'auto_completed'; // this status means, that the request was automatically completed by the system
}
