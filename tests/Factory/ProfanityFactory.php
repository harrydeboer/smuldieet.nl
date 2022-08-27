<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Profanity;
use App\Repository\ProfanityRepositoryInterface;

class ProfanityFactory extends AbstractFactory
{
    public function __construct(
        private readonly ProfanityRepositoryInterface $profanityRepository,
    ) {
    }

    public function create(array $params = []): Profanity
    {
        $profanity = new Profanity();
        $profanity->setName(uniqid('name'));

        $this->setParams($params, $profanity);

        return $this->profanityRepository->create($profanity);
    }
}
