<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Repository\ProfanityRepositoryInterface;
use App\Service\ProfanityCheckService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProfanityCheckServiceTest extends KernelTestCase
{
    public function testCheck(): void
    {
        $profanity = static::getContainer()
            ->get(ProfanityRepositoryInterface::class)
            ->findOneBy(['name' => 'badBadBad']);

        $profanityCheckService = static::getContainer()->get(ProfanityCheckService::class);

        $profanityCheckService->check('test');

        $this->expectException(Exception::class);

        $profanityCheckService->check($profanity->getName());
    }
}
