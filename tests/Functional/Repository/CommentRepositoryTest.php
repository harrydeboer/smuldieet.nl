<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Tests\Factory\CommentFactory;
use App\Repository\CommentRepositoryInterface;
use App\Tests\Factory\PageFactory;
use App\Tests\Factory\RecipeFactory;
use App\Tests\Functional\KernelTestCase;

class CommentRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $recipe = static::getContainer()->get(RecipeFactory::class)->create();
        $commentRecipe = static::getContainer()->get(CommentFactory::class)
            ->create(['isPending' => true, 'recipe' => $recipe]);
        $commentRecipeNotPending = static::getContainer()->get(CommentFactory::class)
            ->create(['isPending' => false, 'recipe' => $recipe]);
        $page = static::getContainer()->get(PageFactory::class)->create();
        $commentPage = static::getContainer()->get(CommentFactory::class)
            ->create(['isPending' => true, 'page' => $page]);
        $commentPageNotPending = static::getContainer()->get(CommentFactory::class)
            ->create(['isPending' => false, 'page' => $page]);

        $commentRepository = static::getContainer()->get(CommentRepositoryInterface::class);

        $this->assertSame($commentRecipe, $commentRepository->find($commentRecipe->getId()));

        $updatedContent = 'test';
        $commentRecipe->setContent($updatedContent);

        $commentRepository->update($commentRecipe);

        $this->assertSame($updatedContent, $commentRepository->findOneBy(['content' => $updatedContent])->getContent());

        $comments = $commentRepository->findAllPendingComments();
        $this->assertTrue(in_array($commentRecipe, $comments));
        $this->assertTrue(in_array($commentPage, $comments));
        $this->assertSame($commentRepository->findCommentsFromRecipe($recipe->getId(), 1)
            ->getResults()[0], $commentRecipeNotPending);
        $this->assertSame($commentRepository->findCommentsFromPage($page->getId(), 1)
            ->getResults()[0], $commentPageNotPending);

        $id = $commentRecipe->getId();
        $commentRepository->delete($commentRecipe);

        $this->assertNull($commentRepository->find($id));
    }
}
