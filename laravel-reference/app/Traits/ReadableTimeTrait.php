<?php

namespace App\Traits;

use Exception;

trait ReadableTimeTrait
{
    public function readable_time($time): array
    {
        try {
            $readable = [];
            $exploded = explode(':', (string) $time);

            foreach ($exploded as $unit) {
                $formatted = null;

                if ($unit !== '00') {
                    $formatted = $unit[0] === '0' ? substr($unit, 1, strlen($unit)) : $unit;
                }

                $readable[] = $formatted;
            }

            return $readable;
        } catch (Exception) {
            return [];
        }
    }
}
