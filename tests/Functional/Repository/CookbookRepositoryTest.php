<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Factory\CookbookFactory;
use App\Repository\CookbookRepositoryInterface;
use App\Tests\Functional\KernelTestCase;

class CookbookRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $cookbook = static::getContainer()->get(CookbookFactory::class)->create();

        $cookbookRepository = static::getContainer()->get(CookbookRepositoryInterface::class);

        $this->assertSame($cookbook, $cookbookRepository->find($cookbook->getId()));

        $updatedTitle = 'Test';
        $cookbook->setTitle($updatedTitle);

        $cookbookRepository->update($cookbook);

        $this->assertSame($updatedTitle, $cookbookRepository->findOneBy(['title' => $updatedTitle])->getTitle());

        $id = $cookbook->getId();

        $this->assertSame($cookbook, $cookbookRepository->getFromUser($id, $cookbook->getUser()->getId()));

        $cookbookRepository->delete($cookbook);

        $this->assertNull($cookbookRepository->find($id));
    }
}
