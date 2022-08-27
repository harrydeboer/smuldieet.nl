<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Tests\Factory\ProfanityFactory;
use App\Repository\ProfanityRepositoryInterface;
use App\Tests\Functional\KernelTestCase;

class ProfanityRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $profanity = static::getContainer()->get(ProfanityFactory::class)->create();

        $profanityRepository = static::getContainer()->get(ProfanityRepositoryInterface::class);

        $updatedName = 'tag2';

        $this->assertSame($profanity, $profanityRepository->find($profanity->getId()));

        $profanity->setName($updatedName);
        $profanityRepository->update();

        $this->assertSame($updatedName, $profanityRepository->findOneBy(['name' => $updatedName])->getName());

        $id = $profanity->getId();
        $profanityRepository->delete($profanity);

        $this->assertNull($profanityRepository->find($id));
    }
}
