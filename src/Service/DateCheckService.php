<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;

/**
 * Date has to be in format d-m-Y.
 */
class DateCheckService
{
    public static function checkDate(string $date, bool $noFuture = false): bool
    {
        $format = 'd-m-Y';
        $dt = DateTime::createFromFormat($format, $date);

        if ($noFuture && $dt->getTimestamp() > time()) {
            return false;
        }

        return $dt && $dt->format($format) === $date;
    }
}
