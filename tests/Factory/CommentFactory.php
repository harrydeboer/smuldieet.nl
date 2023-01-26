<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Comment;
use App\Repository\CommentRepositoryInterface;

class CommentFactory extends AbstractFactory
{
    public function __construct(
        private readonly CommentRepositoryInterface $commentRepository,
        private readonly UserFactory $userFactory,
        private readonly PageFactory $pageFactory,
        private readonly RecipeFactory $recipeFactory,
    ) {
    }

    public function create(array $params = []): Comment
    {
        $user = $params['user'] ?? $this->userFactory->create();
        $page = $params['page'] ?? $this->pageFactory->create();
        $recipe = $params['recipe'] ?? $this->recipeFactory->create();

        $comment = new Comment();
        $comment->setUser($user);
        $comment->setContent(uniqid('content'));
        $comment->setCreatedAt(time());
        $isOnPage = rand(0,1);
        if (isset($params['page'])) {
            $comment->setPage($page);
        } elseif (isset($params['recipe'])) {
            $comment->setRecipe($recipe);
        } elseif ($isOnPage === 1) {
            $comment->setPage($page);
        } else {
            $comment->setRecipe($recipe);
        }

        $this->setParams($params, $comment);

        return $this->commentRepository->create($comment);
    }
}
