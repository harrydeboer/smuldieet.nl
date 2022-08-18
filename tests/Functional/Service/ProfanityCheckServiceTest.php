<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Factory\ProfanityFactory;
use App\Service\ProfanityCheckService;
use App\Tests\Functional\KernelTestCase;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ProfanityCheckServiceTest extends KernelTestCase
{
    public function testCombine(): void
    {
        $profanity = static::getContainer()->get(ProfanityFactory::class)->create(['name' => 'scheldwoord']);

        $profanityCheckService = static::getContainer()->get(ProfanityCheckService::class);

        $this->assertEmpty($profanityCheckService->check('test'));

        $this->expectException(BadRequestException::class);

        $profanityCheckService->check($profanity->getName());
    }
}
