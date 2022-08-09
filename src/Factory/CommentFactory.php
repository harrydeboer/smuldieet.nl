<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Comment;
use App\Repository\CommentRepositoryInterface;

class CommentFactory extends AbstractFactory
{
    public function __construct(
        private readonly CommentRepositoryInterface $commentRepository,
        private readonly UserFactory $userFactory
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
        $comment = new Comment();
        $comment->setUser($paramsParent['user']);
        $comment->setContent(uniqid('content'));
        $comment->setTimestamp(time());

        $this->setParams($params, $comment);

        return $this->commentRepository->create($comment);
    }
}
