<?php

namespace App\Enums;

enum ActivityPlanCategoryFieldTypeEnum: string
{
    case TEXT = 'text';
    case NUMBER = 'number';
    case DATE = 'date';
    case EVENT_DATE = 'event_date';
    case BOOLEAN = 'boolean';
    case COUNTRY = 'country';
    case CITY = 'city';
    case COMPANY = 'company';
    case EXPERT = 'expert';
    case CGP_EMPLOYEE = 'cgp_employee';
    case TIME = 'time';

    public function getTranslation(): string
    {
        return match ($this) {
            self::TEXT => __('activity-plan.field-type-text'),
            self::NUMBER => __('activity-plan.field-type-number'),
            self::DATE => __('activity-plan.field-type-date'),
            self::EVENT_DATE => __('activity-plan.field-type-event-date'),
            self::BOOLEAN => __('activity-plan.field-type-boolean'),
            self::COUNTRY => __('activity-plan.field-type-country'),
            self::CITY => __('activity-plan.field-type-city'),
            self::COMPANY => __('activity-plan.field-type-company'),
            self::EXPERT => __('activity-plan.field-type-expert'),
            self::CGP_EMPLOYEE => __('activity-plan.field-type-cgp-employee'),
            self::TIME => __('activity-plan.field-type-time'),
        };
    }
}
