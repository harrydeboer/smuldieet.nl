<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Comment;
use App\Repository\CommentRepositoryInterface;
use InvalidArgumentException;

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
        $paramsParent = [];
        if (isset($params['user'])) {
            $paramsParent['user'] = $params['user'];
        } else {
            $paramsParent['user'] = $this->userFactory->create();
        }
        if (isset($params['page'])) {
            $paramsParent['page'] = $params['page'];
        } else {
            $paramsParent['page'] = $this->pageFactory->create();
        }
        if (isset($params['recipe'])) {
            $paramsParent['recipe'] = $params['recipe'];
        } else {
            $paramsParent['recipe'] = $this->recipeFactory->create();
        }
        $comment = new Comment();
        $comment->setUser($paramsParent['user']);
        $comment->setContent(uniqid('content'));
        $comment->setTimestamp(time());
        $isOnPage = rand(0,1);
        if (isset($params['page'])) {
            $comment->setPage($paramsParent['page']);
        } elseif (isset($params['recipe'])) {
            $comment->setRecipe($paramsParent['recipe']);
        } elseif ($isOnPage === 1) {
            $comment->setPage($paramsParent['page']);
        } else {
            $comment->setRecipe($paramsParent['recipe']);
        }

        $this->setParams($params, $comment);

        $this->commentRepository->create($comment);

        return $comment;
    }
}
