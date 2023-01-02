<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Tests\Factory\ProfanityFactory;
use App\Repository\ProfanityRepositoryInterface;
use App\Tests\Functional\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProfanityRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $profanity = static::getContainer()->get(ProfanityFactory::class)->create();

        $profanityRepository = static::getContainer()->get(ProfanityRepositoryInterface::class);

        $updatedName = 'tag2';

        $this->assertSame($profanity, $profanityRepository->get($profanity->getId()));

        $profanity->setName($updatedName);
        $profanityRepository->update();

        $this->assertSame($updatedName, $profanityRepository->findOneBy(['name' => $updatedName])->getName());

        $id = $profanity->getId();
        $profanityRepository->delete($profanity);

        $this->expectException(NotFoundHttpException::class);

        $profanityRepository->get($id);
    }
}
