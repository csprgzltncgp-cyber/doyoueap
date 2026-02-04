<?php

namespace App\Enums;

enum InvoicingType: string
{
    case TYPE_NORMAL = 'normal';
    case TYPE_FIXED = 'fixed';
    case TYPE_CUSTOM = 'custom';
}
