<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Factory\CommentFactory;
use App\Repository\CommentRepositoryInterface;
use App\Tests\Functional\KernelTestCase;

class CommentRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $comment = static::getContainer()->get(CommentFactory::class)->create();

        $commentRepository = static::getContainer()->get(CommentRepositoryInterface::class);

        $this->assertSame($comment, $commentRepository->find($comment->getId()));

        $updatedContent = 'test';
        $comment->setContent($updatedContent);

        $commentRepository->update();

        $this->assertSame($updatedContent, $commentRepository->findOneBy(['content' => $updatedContent])->getContent());

        $id = $comment->getId();
        $commentRepository->delete($comment);

        $this->assertNull($commentRepository->find($id));
    }
}
