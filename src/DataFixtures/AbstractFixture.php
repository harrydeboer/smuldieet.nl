<?php

declare(strict_types=1);

namespace App\DataFixtures;

use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;

abstract class AbstractFixture extends Fixture
{
    protected function randomDate(int $yearLimit = null): DateTime
    {
        $day = rand(1, 28);
        $month = rand(1, 12);
        $currentYear = (int) date('Y');
        if (is_null($yearLimit)) {
            $year = rand(1900, $currentYear - 1);
        } else {
            $year = rand($currentYear - $yearLimit, $currentYear - 1);
        }
        $date = new DateTime();
        $date->setDate($year, $month, $day);

        return $date;
    }
}
