<?php

namespace App\Helpers;

use Carbon\Carbon;

class QuarterDates
{
    private function __construct() {}

    public static function dates(?int $quarter = null)
    {
        $year = (Carbon::now()->quarter === 1) ? Carbon::now()->subYear() : Carbon::now();

        return match ($quarter) {
            1 => [$year->copy()->setMonth(1)->startOfQuarter(), $year->copy()->setMonth(1)->endOfQuarter()],
            2 => [$year->copy()->setMonth(4)->startOfQuarter(), $year->copy()->setMonth(4)->endOfQuarter()],
            3 => [$year->copy()->setMonth(7)->startOfQuarter(), $year->copy()->setMonth(7)->endOfQuarter()],
            4 => [$year->copy()->setMonth(10)->startOfQuarter(), $year->copy()->setMonth(10)->endOfQuarter()],
            default => [$year->copy()->setMonth(1)->startOfQuarter(), $year->copy()->setMonth(1)->endOfQuarter()],
        };
    }
}
