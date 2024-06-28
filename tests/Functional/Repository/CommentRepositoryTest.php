<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\CommentRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentRepositoryTest extends KernelTestCase
{
    private function getCommentRepository(): CommentRepositoryInterface
    {
        return static::getContainer()->get(CommentRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $commentRepository = $this->getCommentRepository();

        $commentRecipe = $commentRepository->findOneBy(['content' => 'testRecipePending']);
        $recipe = $commentRecipe->getRecipe();
        $commentRecipeNotPending = $commentRepository->findOneBy(['content' => 'testRecipe']);

        $commentPage = $commentRepository->findOneBy(['content' => 'testPagePending']);
        $page = $commentPage->getPage();
        $commentPageNotPending = $commentRepository->findOneBy(['content' => 'testPage']);

        $this->assertSame($commentRecipe, $commentRepository->get($commentRecipe->getId()));

        $updatedContent = 'updated';
        $commentRecipe->setContent($updatedContent);

        $commentRepository->update($commentRecipe);

        $this->assertSame($updatedContent, $commentRepository->findOneBy(['content' => $updatedContent])->getContent());

        $comments = $commentRepository->findAllPendingComments();
        $this->assertTrue(in_array($commentRecipe, $comments));
        $this->assertTrue(in_array($commentPage, $comments));
        $this->assertTrue(in_array($commentRecipeNotPending, $commentRepository
            ->findCommentsFromRecipe($recipe->getId(), 1)
            ->getResults()));
        $this->assertTrue(in_array($commentPageNotPending, $commentRepository->findCommentsFromPage($page->getId(), 1)
            ->getResults()));

        $id = $commentRecipe->getId();
        $commentRepository->delete($commentRecipe);

        $this->expectException(NotFoundHttpException::class);

        $commentRepository->get($id);
    }
}
