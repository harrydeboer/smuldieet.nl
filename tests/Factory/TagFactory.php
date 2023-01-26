<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Tag;
use App\Repository\TagRepositoryInterface;

class TagFactory extends AbstractFactory
{
    public function __construct(
        private readonly TagRepositoryInterface $tagRepository,
    ) {
    }

    public function create(array $params = []): Tag
    {
        $tag = new Tag();
        $tag->setName(uniqid('name'));
        $tag->setCreatedAt(time());

        $this->setParams($params, $tag);

        return $this->tagRepository->create($tag);
    }
}
