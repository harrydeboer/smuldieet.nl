<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Page;
use App\Repository\PageRepositoryInterface;
use InvalidArgumentException;

class PageFactory extends AbstractFactory
{
    public function __construct(
        private readonly PageRepositoryInterface $pageRepository,
        private readonly UserFactory $userFactory,
    ) {
    }

    public function create(array $params = []): Page
    {
        $user = $params['user'] ?? $this->userFactory->create();

        $page = new Page();
        $page->setUser($user);
        $page->setTitle(uniqid('title'));
        $page->setSlug(uniqid('slug'));
        $page->setCreatedAt(time());
        $page->setContent(uniqid('content'));

        if (isset($params['comments'])) {
            throw new InvalidArgumentException('Cannot add comments to page. Assign page in comment creation.');
        }

        $this->setParams($params, $page);

        return $this->pageRepository->create($page);
    }
}
