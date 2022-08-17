<?php

declare(strict_types=1);

namespace App\Factory;

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

        $this->setParams($params, $tag);

        $this->tagRepository->create($tag);

        return $tag;
    }
}
