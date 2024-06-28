<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\TagRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TagRepositoryTest extends KernelTestCase
{
    private function getTagRepository(): TagRepositoryInterface
    {
        return static::getContainer()->get(TagRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $tagRepository = $this->getTagRepository();

        $tag = $tagRepository->findOneBy(['name' => 'test']);

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
