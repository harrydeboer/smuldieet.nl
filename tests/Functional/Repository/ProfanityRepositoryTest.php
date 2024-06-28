<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\ProfanityRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProfanityRepositoryTest extends KernelTestCase
{
    private function getProfanityRepository(): ProfanityRepositoryInterface
    {
        return static::getContainer()->get(ProfanityRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $profanityRepository = $this->getProfanityRepository();

        $profanity = $profanityRepository->findOneBy(['name' => 'badBadBad']);

        $updatedName = 'badBadBad2';

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
