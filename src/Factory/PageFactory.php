<?php

declare(strict_types=1);

namespace App\Factory;

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
        $paramsParent = [];
        if (isset($params['user'])) {
            $paramsParent['user'] = $params['user'];
        } else {
            $paramsParent['user'] = $this->userFactory->create();
        }
        $page = new Page();
        $page->setUser($paramsParent['user']);
        $page->setTitle(uniqid('title'));
        $page->setSlug(uniqid('slug'));
        $page->setTimestamp(time());
        $page->setContent(uniqid('content'));

        if (isset($params['comments'])) {
            throw new InvalidArgumentException('Cannot add comments to page. Assign page in comment creation.');
        }

        $this->setParams($params, $page);

        $this->pageRepository->create($page);

        return $page;
    }
}
