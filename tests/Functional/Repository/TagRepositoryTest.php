<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Tests\Factory\TagFactory;
use App\Repository\TagRepositoryInterface;
use App\Tests\Functional\KernelTestCase;

class TagRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $tag = static::getContainer()->get(TagFactory::class)->create();

        $tagRepository = static::getContainer()->get(TagRepositoryInterface::class);

        $this->assertSame($tag, $tagRepository->find($tag->getId()));

        $updatedName = 'tag2';
        $tag->setName($updatedName);

        $tagRepository->update();

        $this->assertSame($updatedName, $tagRepository->findOneBy(['name' => $updatedName])->getName());

        $id = $tag->getId();
        $tagRepository->delete($tag);

        $this->assertNull($tagRepository->find($id));
    }
}
