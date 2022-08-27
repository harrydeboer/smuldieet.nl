<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use DateTime;
use InvalidArgumentException;

abstract class AbstractFactory
{
    protected function setParams(array $params, object $entity): void
    {
        foreach ($params as $key => $param) {
            if ($key === 'id') {
                throw new InvalidArgumentException(
                    'The create method of this factory is not allowed to set the id.');
            }
            $method = 'set' . ucfirst($key);
            if (!method_exists($entity, $method)) {
                throw new InvalidArgumentException('The setter ' . $method . ' does not exist in ' .
                    $entity::class . ' for property ' . $key . ' .');
            }
            $entity->$method($param);
        }
    }

    protected function generateRandomString(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

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
