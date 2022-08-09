<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Day;
use App\Repository\DayRepositoryInterface;

class DayFactory extends AbstractFactory
{
    public function __construct(
        private readonly DayRepositoryInterface $dayRepository,
        private readonly UserFactory $userFactory,
    ) {
    }

    public function create(array $params = []): Day
    {
        $paramsParent = [];
        if (isset($params['user'])) {
            $paramsParent['user'] = $params['user'];
        } else {
            $paramsParent['user'] = $this->userFactory->create();
        }
        $day = new Day();

        $day->setDate($this->randomDate());
        $day->setUser($paramsParent['user']);

        $this->setParams($params, $day);

        return $this->dayRepository->create($day);
    }
}
