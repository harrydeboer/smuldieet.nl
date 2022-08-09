<?php

declare(strict_types=1);

namespace App\Factory;

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

    protected function randomDate(bool $noFuture = false): string
    {
        $daysPerMonth = [31,29,31,30,31,30,31,31,30,31,30,31];

        $monthNumber = rand(1,12);
        $dayNumber = rand(1, $daysPerMonth[$monthNumber - 1]);
        if ($noFuture) {
            $year = rand(1900, (int) date("Y"));
            $monthNumber = rand(1, (int) date('m'));
            $dayNumber = rand(1, (int) date('d'));
        } else {
            $year = rand(1900, 9999);
        }

        if ($year % 4 !== 0 && $monthNumber === 2 && $dayNumber === 29) {
            $dayNumber = 28;
        }

        $month = (string) $monthNumber;
        $day = (string) $dayNumber;
        $year = (string) $year;

        if (strlen($day) === 1) {
            $day = '0' . $day;
        }
        if (strlen($month) === 1) {
            $month = '0' . $month;
        }

        return $day . '-' . $month . '-' . $year;
    }
}
