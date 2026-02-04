<?php

namespace App\Enums;

enum CantAssignCaseReasonEnum: string
{
    case NOT_AVAILABLE = 'not_available';
    case PROFESSIONAL_REASONS = 'professional_reasons';
    case ETHICAL_REASONS = 'ethical_reasons';
}
