<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Tests\Factory\ProfanityFactory;
use App\Service\ProfanityCheckService;
use App\Tests\Functional\KernelTestCase;
use Exception;

class ProfanityCheckServiceTest extends KernelTestCase
{
    public function testCombine(): void
    {
        $profanity = static::getContainer()->get(ProfanityFactory::class)->create(['name' => 'scheldwoord']);

        $profanityCheckService = static::getContainer()->get(ProfanityCheckService::class);

        $this->assertEmpty($profanityCheckService->check('test'));

        $this->expectException(Exception::class);

        $profanityCheckService->check($profanity->getName());
    }
}
