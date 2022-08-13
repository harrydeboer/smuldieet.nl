<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Page;
use App\Repository\PageRepositoryInterface;

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

        $this->setParams($params, $page);

        return $this->pageRepository->create($page);
    }
}
