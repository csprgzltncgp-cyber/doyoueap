<?php

namespace App\Traits;

trait ContractDateTrait
{
    public function getPeriodEnd($contract_date): string
    {
        $year = date('Y');
        $myMonthDay = date('m-d', strtotime((string) $contract_date));
        $end_date = $year.'-'.$myMonthDay;

        return (now() > $end_date) ? date('Y-m-d', strtotime($end_date.' +1 year')) : $end_date;
    }

    public function getPeriodStart($contract_date): string
    {
        $period_end = $this->getPeriodEnd($contract_date);

        return date('Y-m-d', strtotime($period_end.' -1 year'));
    }
}
