<?php

namespace App\Services;

use Carbon\Carbon;

class DateService
{
    public static function last_five_week_day_check(): bool
    {
        $today = Carbon::today()->startOfDay();

        $last_day_of_month = $today->copy()->endOfMonth();

        $last_5_week_days = [];

        // Loop backward from the last day of the month until we've found the last 5 weekdays
        while (count($last_5_week_days) < 5) {
            if ($last_day_of_month->isWeekday()) { // Check if it's a weekday (Monday to Friday)
                $last_5_week_days[] = $last_day_of_month->copy()->startOfDay();
            }

            $last_day_of_month->subDay();
        }

        // Check if today is in the last 5 weekdays and if it's Monday, Wednesday, or Friday
        return in_array($today, $last_5_week_days) &&
            ($today->isMonday() || $today->isWednesday() || $today->isFriday());
    }

    public static function is_work_day_of_month(int $work_day): bool
    {
        $today = Carbon::now();

        $date = Carbon::now()->setDay($work_day);

        // Iterate through the days of the month
        while ($date->day <= $today->day) {

            // If the day is a weekday (Monday to Friday)
            if (! $date->isWeekend()) {
                return $date->isSameDay($today);
            }

            // Move to the next day
            $date->addDay();
        }

        return false;
    }

    /**
     * @param  $day  The target day of the month we check if it is today.
     * @param  $preceding_day  Marks an event that can't happen on the week end. It must be before the $day
     * @param  $is_past  IF true we check if the target date (delayed or not) is in the past.
     */
    public static function is_day_of_month(int $day, int $preceding_day, bool $is_past = false): bool
    {
        $date = Carbon::now()->setDay($day);

        $preceding_date = Carbon::now()->setDay($preceding_day);

        /**
         * Check if the preciding day is on a weekend.
         */
        if ($preceding_date->isWeekend()) {
            $delay_date = $preceding_date;
            while ($delay_date->isWeekend()) {
                $delay_date->addDay();
            }

            $date = $delay_date->addDay();
        }

        if ($is_past && $date->isPast()) {
            return true;
        }

        return $date->isToday();
    }
}
